<?php

namespace App\Http\Controllers;

use App\Models\PrivateStreamRequest;
use App\Models\PrivateStreamFeedback;
use App\Models\StreamerAvailability;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Chat;
use App\Events\PrivateStreamChatEvent;
use App\Events\PrivateStreamStateChanged;
use App\Events\PrivateStreamCountdownUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Refund;
use Stripe\Exception\ApiErrorException;
use App\Models\Tips;

/**
 * PrivateStreamRequestController
 * 
 * Handles all private streaming functionality including:
 * - Availability and scheduling
 * - Request creation and management 
 * - Payment processing (Stripe, Mercado Pago)
 * - Stream session lifecycle
 * - Chat and tips
 * - Feedback and dispute resolution
 * - Refunds and financial transactions
 */
class PrivateStreamRequestController extends Controller
{
    // ============================================================================
    // CONSTRUCTOR & CORE SETUP
    // ============================================================================

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ============================================================================
    // UTILITY & HELPER METHODS
    // ============================================================================

    /**
     * Record a transaction in the system.
     *
     * @param int $userId
     * @param string $type
     * @param int $referenceId
     * @param string $referenceType
     * @param float $amount
     * @param string $paymentMethod
     * @param string|null $paymentId
     * @param string $status
     * @param string $description
     * @param array $metadata
     * @return \App\Models\Transaction
     */
    private function recordTransaction($userId, $type, $referenceId, $referenceType, $amount, $paymentMethod, $paymentId, $status, $description, $metadata = [])
    {
        return Transaction::create([
            'user_id' => $userId,
            'transaction_type' => $type,
            'reference_id' => $referenceId,
            'reference_type' => $referenceType,
            'amount' => $amount,
            'currency' => opt('payment-settings.currency_code', 'USD'),
            'payment_method' => $paymentMethod,
            'payment_id' => $paymentId,
            'status' => $status,
            'description' => $description,
            'metadata' => !empty($metadata) ? json_encode($metadata) : null,
        ]);
    }

    /**
     * Helper method to safely parse requested date and time into a Carbon instance
     *
     * @param mixed $requestedDate
     * @param string $requestedTime
     * @return \Carbon\Carbon
     */
    private function parseRequestedDateTime($requestedDate, $requestedTime)
    {
        try {
            // Ensure we get just the date part (Y-m-d format)
            $dateString = Carbon::parse($requestedDate)->format('Y-m-d');

            // Handle different time formats that might be stored
            $timeString = $requestedTime;

            // If time doesn't contain seconds, add them
            if (substr_count($timeString, ':') === 1) {
                $timeString .= ':00';
            }

            // Create the full datetime string and parse it
            $parsedDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $dateString . ' ' . $timeString);

            // Validate the parsed datetime
            if (!$parsedDateTime || !$parsedDateTime->isValid()) {
                throw new \Exception('Invalid datetime created');
            }

            return $parsedDateTime;
        } catch (\Exception $e) {
            // Fallback: try to parse the original concatenation (but with error handling)
            try {
                $fallbackDateTime = Carbon::parse($requestedDate . ' ' . $requestedTime);
                if ($fallbackDateTime && $fallbackDateTime->isValid()) {
                    return $fallbackDateTime;
                }
            } catch (\Exception $fallbackException) {
                // Log both errors for debugging
                Log::error('Failed to parse datetime in both attempts', [
                    'requested_date' => $requestedDate,
                    'requested_time' => $requestedTime,
                    'original_error' => $e->getMessage(),
                    'fallback_error' => $fallbackException->getMessage()
                ]);
            }

            // If all else fails, return current time (so app doesn't crash)
            Log::warning('Using current time as fallback for invalid datetime', [
                'requested_date' => $requestedDate,
                'requested_time' => $requestedTime,
            ]);

            return Carbon::now();
        }
    }

    /**
     * Check if a user has access to a private stream session
     *
     * @param PrivateStreamRequest $streamRequest
     * @param User $user
     * @return bool
     */
    private function hasAccessToStream($streamRequest, $user)
    {
        $isOwner = $streamRequest->user_id === $user->id;
        $isStreamer = $streamRequest->streamer_id === $user->id;
        $isAdmin = $user->hasRole('admin') || $user->is_admin ?? false; // Adjust based on your admin check

        return $isOwner || $isStreamer || $isAdmin;
    }

    /**
     * Generate a unique idempotency key for Mercado Pago API calls
     */
    private function generateIdempotencyKey()
    {
        return sprintf(
            '%s-%s-%s-%s-%s',
            Str::random(8),
            Str::random(4),
            Str::random(4),
            Str::random(4),
            Str::random(12)
        );
    }

    /**
     * Check for pending payments for a user
     *
     * @param int $userId
     * @return array
     */
    private function checkForPendingPayments($userId)
    {
        // Check session for pending payments
        $pendingPayments = [];
        $sessionKeys = array_keys(session()->all());
        
        foreach ($sessionKeys as $key) {
            if (strpos($key, 'pending_stream_requests.') === 0) {
                $requestData = session($key);
                if ($requestData && isset($requestData['user_id']) && $requestData['user_id'] == $userId) {
                    $pendingPayments[] = [
                        'payment_reference' => str_replace('pending_stream_requests.', '', $key),
                        'streamer_id' => $requestData['streamer_id'],
                        'requested_date' => $requestData['requested_date'],
                        'requested_time' => $requestData['requested_time'],
                        'created_at' => $requestData['expires_at'] ? Carbon::parse($requestData['expires_at'])->subHours(24)->toISOString() : null
                    ];
                }
            }
        }

        if (!empty($pendingPayments)) {
            return [
                'has_pending' => true,
                'message' => 'You have a pending payment for a private stream request. Please complete or cancel the existing payment before creating a new request.',
                'pending_payments' => $pendingPayments
            ];
        }

        return [
            'has_pending' => false,
            'message' => null,
            'pending_payments' => []
        ];
    }

    /**
     * Create Stripe payment intent without creating stream request
     *
     * @param User $user
     * @param User $streamer
     * @param float $streamerFee
     * @param string $paymentReference
     * @return \Stripe\PaymentIntent
     */
    private function createStripePaymentIntent(User $user, User $streamer, $streamerFee, $paymentReference)
    {
        Stripe::setApiKey(opt('STRIPE_SECRET_KEY'));

        $paymentIntent = PaymentIntent::create([
            'amount' => $streamerFee * 100, // Convert to cents
            'currency' => 'usd',
            'description' => 'Private stream with ' . $streamer->name,
            'metadata' => [
                'user_id' => $user->id,
                'streamer_id' => $streamer->id,
                'payment_reference' => $paymentReference,
                'type' => 'private_stream',
            ],
            'capture_method' => 'manual', // For holding the payment in escrow
        ]);

                 return $paymentIntent;
     }

     /**
      * Clean up expired pending payments from session
      *
      * @return void
      */
     public function cleanupExpiredPendingPayments()
     {
         $sessionKeys = array_keys(session()->all());
         $cleanedUp = 0;
         
         foreach ($sessionKeys as $key) {
             if (strpos($key, 'pending_stream_requests.') === 0) {
                 $requestData = session($key);
                 if ($requestData && isset($requestData['expires_at'])) {
                     $expiresAt = Carbon::parse($requestData['expires_at']);
                     if (Carbon::now()->gt($expiresAt)) {
                         session()->forget($key);
                         $cleanedUp++;
                     }
                 }
             }
         }
         
         if ($cleanedUp > 0) {
             Log::info("Cleaned up {$cleanedUp} expired pending payment sessions");
         }
     }

     /**
      * Cancel a pending payment session
      *
      * @param  \Illuminate\Http\Request  $request
      * @return \Illuminate\Http\JsonResponse
      */
     public function cancelPendingPayment(Request $request)
     {
         $request->validate([
             'payment_reference' => 'required|string'
         ]);

         $paymentReference = $request->payment_reference;
         $sessionKey = 'pending_stream_requests.' . $paymentReference;
         
         // Get request data from session
         $requestData = session($sessionKey);
         
         if (!$requestData) {
             return response()->json([
                 'status' => false,
                 'message' => 'Pending payment not found or already expired.'
             ]);
         }

         // Verify the user matches
         if ($requestData['user_id'] !== Auth::id()) {
             return response()->json([
                 'status' => false,
                 'message' => 'Unauthorized action.'
             ]);
         }

         // Remove from session
         session()->forget($sessionKey);

         return response()->json([
             'status' => true,
             'message' => 'Pending payment cancelled successfully.'
         ]);
     }

    // ============================================================================
    // AVAILABILITY & SCHEDULING METHODS
    // ============================================================================

    /**
     * Get available dates for a streamer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $streamerId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvailableDates(Request $request, $streamerId)
    {
        $streamer = User::findOrFail($streamerId);

        // Get all availability slots for the streamer
        $availabilitySlots = StreamerAvailability::where('streamer_id', $streamerId)->get();

        if ($availabilitySlots->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No availability slots found for this streamer.'
            ]);
        }

        // Get the next 30 days
        $dates = collect();
        $startDate = Carbon::today();
        $endDate = Carbon::today()->addDays(30);

        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            $dayOfWeek = $date->dayOfWeek;

            // Check if any availability slot includes this day of the week
            $hasSlot = $availabilitySlots->filter(function ($slot) use ($dayOfWeek) {
                $days = is_array($slot->days_of_week)
                    ? $slot->days_of_week
                    : explode(',', $slot->days_of_week);

                return in_array($dayOfWeek, array_map('intval', $days));
            })->isNotEmpty();

            if ($hasSlot) {
                // For today, check if there are actually available time slots
                $shouldInclude = true;
                if ($date->isToday()) {
                    // Quick check if there are any time slots available for today
                    $testSlots = $this->getTimeSlotsForDate($date->format('Y-m-d'), $streamerId, $availabilitySlots);
                    $shouldInclude = count($testSlots) > 0;
                    

                }
                
                if ($shouldInclude) {
                    $dateEntry = [
                        'date' => $date->format('Y-m-d'),
                        'display' => $date->format('D, M d, Y'),
                        'day_of_week' => $dayOfWeek,
                    ];
                    $dates->push($dateEntry);
                }
            }
        }
        
        return response()->json([
            'status' => true,
            'dates' => $dates
        ]);
    }

    /**
     * Get available time slots for a specific date.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $streamerId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvailableTimeSlots(Request $request, $streamerId)
    {
        $date = $request->date;
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;

        // Get available slots for this day of the week
        $availabilitySlots = StreamerAvailability::where('streamer_id', $streamerId)
            ->get()
            ->filter(function ($slot) use ($dayOfWeek) {
                $days = is_array($slot->days_of_week)
                    ? $slot->days_of_week
                    : explode(',', $slot->days_of_week);

                return in_array($dayOfWeek, array_map('intval', $days));
            });

        if ($availabilitySlots->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No availability slots found for this date.'
            ]);
        }

        // Get existing bookings for this date to check for conflicts
        // Only consider active bookings (pending, accepted, or confirmed)
        $existingBookings = PrivateStreamRequest::where('streamer_id', $streamerId)
            ->where('requested_date', $date)
            ->whereIn('status', ['pending', 'accepted', 'confirmed'])
            ->get();

        $timeSlots = [];

        foreach ($availabilitySlots as $slot) {
            $startTime = Carbon::parse($slot->start_time);
            $endTime = Carbon::parse($slot->end_time);

            // Generate time slots in 15-minute increments
            $currentSlot = clone $startTime;

            while ($currentSlot->lt($endTime)) {
                $slotEndTime = (clone $currentSlot)->addMinutes(15);

                // Check for conflicts with existing bookings
                $isBooked = false;
                foreach ($existingBookings as $booking) {
                    $bookingStartTime = Carbon::parse($booking->requested_time);
                    $bookingEndTime = (clone $bookingStartTime)->addMinutes($booking->duration_minutes);

                    // Simplified overlap detection: Two time ranges overlap if start1 < end2 AND start2 < end1
                    $hasOverlap = ($currentSlot->lt($bookingEndTime) && $bookingStartTime->lt($slotEndTime));
                    
                    if ($hasOverlap) {
                        $isBooked = true;
                        break;
                    }
                }

                // Check if this time slot is in the past or within the next hour
                $slotDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $currentSlot->format('H:i:s'));
                $now = Carbon::now();
                $oneHourFromNow = Carbon::now()->addHour();
                
                // Check for past slots or slots too close to current time
                $isPastSlot = false;
                $selectedDate = Carbon::parse($date);
                $today = Carbon::today();
                
                if ($selectedDate->isSameDay($today)) {
                    // Show slots that are at least 1 hour from now
                    $isPastSlot = $slotDateTime->lte($oneHourFromNow);
                }

                if (!$isBooked && !$isPastSlot) {
                    $timeSlots[] = [
                        'id' => $slot->id,
                        'time' => $currentSlot->format('H:i'),
                        'display' => $currentSlot->format('h:i A'),
                        'tokens_per_minute' => opt('private_room_rental_tokens_per_minute', 5), // Use admin setting
                    ];
                }

                $currentSlot->addMinutes(15);
            }
        }
        
        return response()->json([
            'status' => true,
            'timeSlots' => $timeSlots
        ]);
    }

    /**
     * Helper method to get time slots for a specific date (used by getAvailableDates)
     *
     * @param string $date
     * @param int $streamerId
     * @param \Illuminate\Support\Collection $availabilitySlots
     * @return array
     */
    private function getTimeSlotsForDate($date, $streamerId, $availabilitySlots)
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;

        // Get available slots for this day of the week
        $daySlots = $availabilitySlots->filter(function ($slot) use ($dayOfWeek) {
            $days = is_array($slot->days_of_week)
                ? $slot->days_of_week
                : explode(',', $slot->days_of_week);

            return in_array($dayOfWeek, array_map('intval', $days));
        });

        if ($daySlots->isEmpty()) {
            return [];
        }

        // Get existing bookings for this date
        $existingBookings = PrivateStreamRequest::where('streamer_id', $streamerId)
            ->where('requested_date', $date)
            ->whereIn('status', ['pending', 'accepted', 'confirmed'])
            ->get();

        $timeSlots = [];
        $now = Carbon::now();
        $selectedDate = Carbon::parse($date);
        $today = Carbon::today();

        foreach ($daySlots as $slot) {
            $startTime = Carbon::parse($slot->start_time);
            $endTime = Carbon::parse($slot->end_time);
            $currentSlot = clone $startTime;

            while ($currentSlot->lt($endTime)) {
                $slotEndTime = (clone $currentSlot)->addMinutes(15);

                // Check for conflicts with existing bookings
                $isBooked = false;
                foreach ($existingBookings as $booking) {
                    $bookingStartTime = Carbon::parse($booking->requested_time);
                    $bookingEndTime = (clone $bookingStartTime)->addMinutes($booking->duration_minutes);

                    if ($currentSlot->lt($bookingEndTime) && $bookingStartTime->lt($slotEndTime)) {
                        $isBooked = true;
                        break;
                    }
                }

                // Check if this time slot is in the past or within the next hour
                $slotDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $currentSlot->format('H:i:s'));
                $oneHourFromNow = Carbon::now()->addHour();
                $isPastSlot = false;
                if ($selectedDate->isSameDay($today)) {
                    // Show slots that are at least 1 hour from now
                    $isPastSlot = $slotDateTime->lte($oneHourFromNow);
                }

                if (!$isBooked && !$isPastSlot) {
                    $timeSlots[] = [
                        'id' => $slot->id,
                        'time' => $currentSlot->format('H:i'),
                        'display' => $currentSlot->format('h:i A'),
                        'tokens_per_minute' => opt('private_room_rental_tokens_per_minute', 5),
                    ];
                }

                $currentSlot->addMinutes(15);
            }
        }

        return $timeSlots;
    }

    // ============================================================================
    // REQUEST CREATION & MANAGEMENT
    // ============================================================================

    /**
     * Create a new private stream request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createRequest(Request $request)
    {
        $request->validate([
            'streamer_id' => 'required|exists:users,id',
            'availability_id' => 'required|exists:streamer_availability,id',
            'requested_date' => 'required|date|after_or_equal:today',
            'requested_time' => 'required',
            'duration_minutes' => 'required|in:3,5,7',
            'streamer_fee' => 'required|numeric|min:1',
            'message' => 'nullable|string|max:500',
            'payment_method' => 'nullable|in:stripe,mercado_pago',
            'email' => 'required_if:payment_method,mercado_pago|email',
        ]);

        $user = Auth::user();
        $streamer = User::findOrFail($request->streamer_id);
        $availability = StreamerAvailability::findOrFail($request->availability_id);

        // Check for existing pending payments for this user
        $pendingPaymentCheck = $this->checkForPendingPayments($user->id);
        if ($pendingPaymentCheck['has_pending']) {
            return response()->json([
                'status' => false,
                'message' => $pendingPaymentCheck['message'],
                'pending_payments' => $pendingPaymentCheck['pending_payments']
            ]);
        }

        // Check for duplicate pending requests (this should be minimal now since we don't create until payment)
        $existingRequest = PrivateStreamRequest::where('user_id', $user->id)
            ->where('streamer_id', $request->streamer_id)
            ->where('requested_date', $request->requested_date)
            ->where('requested_time', $request->requested_time)
            ->whereIn('status', ['pending', 'accepted'])
            ->first();

        if ($existingRequest) {
            return response()->json([
                'status' => false,
                'message' => 'You already have a pending or accepted request for this time slot.',
                'duplicate_request_id' => $existingRequest->id
            ]);
        }

        // Calculate room rental tokens using admin setting
        $tokensPerMinute = opt('private_room_rental_tokens_per_minute', 5); // Default to 5 if not set
        $roomRentalTokens = $tokensPerMinute * $request->duration_minutes;

        // Check if user has enough tokens for room rental
        if ($user->tokens < $roomRentalTokens) {
            $tokensNeeded = $roomRentalTokens - $user->tokens;
            return response()->json([
                'status' => false,
                'message' => "Insufficient tokens. You need {$tokensNeeded} more tokens for room rental.",
                'token_balance' => $user->tokens,
                'tokens_needed' => $roomRentalTokens,
                'tokens_missing' => $tokensNeeded,
                'redirect' => route('token.packages')
            ]);
        }

        // Set default payment method to stripe
        $paymentMethod = $request->payment_method ?? 'stripe';

        try {
            // Prepare request data that will be used after payment confirmation
            $requestData = [
                'user_id' => $user->id,
                'streamer_id' => $streamer->id,
                'availability_id' => $availability->id,
                'requested_date' => $request->requested_date,
                'requested_time' => $request->requested_time,
                'duration_minutes' => $request->duration_minutes,
                'room_rental_tokens' => $roomRentalTokens,
                'streamer_fee' => $request->streamer_fee,
                'currency' => 'USD',
                'message' => $request->message,
                'payment_method' => $paymentMethod,
                'status' => 'pending',
                'expires_at' => Carbon::now()->addHours(24),
            ];

            // Generate unique reference for this payment attempt
            $paymentReference = 'private_stream_' . $user->id . '_' . time() . '_' . rand(1000, 9999);
            
            // Store request data in session using the payment reference (for both payment methods)
            session(['pending_stream_requests.' . $paymentReference => $requestData]);

            if ($paymentMethod === 'mercado_pago') {
                // Process Mercado Pago payment
                try {
                    $mercadoResponse = $this->processMercadoPagoPayment($requestData, $user, $streamer, $request->streamer_fee, $paymentReference, $request->email);
                    
                    if ($mercadoResponse && $mercadoResponse['success'] && isset($mercadoResponse['payment_url'])) {
                        return response()->json([
                            'status' => true,
                            'message' => 'Redirecting to Mercado Pago payment...',
                            'payment_reference' => $paymentReference,
                            'redirect_url' => $mercadoResponse['payment_url'],
                            'preference_url' => $mercadoResponse['preference_url']
                        ]);
                    } else {
                        // Remove from session if payment setup failed
                        session()->forget('pending_stream_requests.' . $paymentReference);
                        throw new \Exception('Failed to initialize Mercado Pago payment');
                    }
                } catch (\Exception $e) {
                    // Remove from session if payment setup failed
                    session()->forget('pending_stream_requests.' . $paymentReference);
                    throw $e;
                }
            } else {
                // Process Stripe payment - create payment intent without creating stream request
                try {
                    $paymentIntent = $this->createStripePaymentIntent($user, $streamer, $request->streamer_fee, $paymentReference);
                    
                    return response()->json([
                        'status' => true,
                        'message' => 'Payment intent created successfully.',
                        'payment_reference' => $paymentReference,
                        'client_secret' => $paymentIntent->client_secret,
                        'payment_intent_id' => $paymentIntent->id
                    ]);
                } catch (\Exception $e) {
                    // Remove from session if payment setup failed
                    session()->forget('pending_stream_requests.' . $paymentReference);
                    throw $e;
                }
            }
        } catch (ApiErrorException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error processing payment: ' . $e->getMessage()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Confirm payment for a private stream request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirmPayment(Request $request)
    {
        $request->validate([
            'payment_intent_id' => 'required',
            'payment_reference' => 'required',
        ]);

        $paymentReference = $request->payment_reference;
        $paymentIntentId = $request->payment_intent_id;

        // Get request data from session
        $requestData = session('pending_stream_requests.' . $paymentReference);
        
        if (!$requestData) {
            return response()->json([
                'status' => false,
                'message' => 'Payment session expired or not found. Please try again.'
            ]);
        }

        // Verify the user matches
        if ($requestData['user_id'] !== Auth::id()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized action.'
            ]);
        }

        try {
            DB::beginTransaction();

            // Verify payment with Stripe
            Stripe::setApiKey(opt('STRIPE_SECRET_KEY'));
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);
            
            if ($paymentIntent->status !== 'requires_capture') {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Payment verification failed. Please try again.'
                ]);
            }

            // Get user and streamer
            $user = User::findOrFail($requestData['user_id']);
            $streamer = User::findOrFail($requestData['streamer_id']);

            // Final check for duplicates (race condition protection)
            $duplicateCheck = PrivateStreamRequest::where('user_id', $requestData['user_id'])
                ->where('streamer_id', $requestData['streamer_id'])
                ->where('requested_date', $requestData['requested_date'])
                ->where('requested_time', $requestData['requested_time'])
                ->whereIn('status', ['pending', 'accepted'])
                ->lockForUpdate()
                ->first();

            if ($duplicateCheck) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'A request for this time slot already exists. Please check your existing requests.',
                    'duplicate_request_id' => $duplicateCheck->id
                ]);
            }

            // Check if user still has enough tokens for room rental
            if ($user->tokens < $requestData['room_rental_tokens']) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Insufficient tokens for room rental. Please purchase more tokens.',
                    'redirect' => route('token.packages')
                ]);
            }

            // Create the stream request
            $streamRequest = PrivateStreamRequest::create([
                'user_id' => $requestData['user_id'],
                'streamer_id' => $requestData['streamer_id'],
                'availability_id' => $requestData['availability_id'],
                'requested_date' => $requestData['requested_date'],
                'requested_time' => $requestData['requested_time'],
                'duration_minutes' => $requestData['duration_minutes'],
                'room_rental_tokens' => $requestData['room_rental_tokens'],
                'streamer_fee' => $requestData['streamer_fee'],
                'currency' => $requestData['currency'],
                'message' => $requestData['message'],
                'payment_method' => 'stripe',
                'payment_id' => $paymentIntentId,
                'payment_status' => 'confirmed',
                'status' => 'pending',
                'expires_at' => $requestData['expires_at'],
            ]);

            // Deduct tokens for room rental
            $user->tokens -= $requestData['room_rental_tokens'];
            $user->save();

            // Record token deduction transaction
            $this->recordTransaction(
                $user->id,
                'room_rental',
                $streamRequest->id,
                PrivateStreamRequest::class,
                $requestData['room_rental_tokens'],
                'tokens',
                null,
                'completed',
                'Room rental fee for private stream with ' . $streamer->name,
                [
                    'streamer_id' => $streamer->id,
                    'streamer_name' => $streamer->name,
                    'requested_date' => $requestData['requested_date'],
                    'requested_time' => $requestData['requested_time'],
                    'duration_minutes' => $requestData['duration_minutes']
                ]
            );

            // Record the payment transaction
            $this->recordTransaction(
                $user->id,
                'private_stream_fee',
                $streamRequest->id,
                PrivateStreamRequest::class,
                $requestData['streamer_fee'],
                'stripe',
                $paymentIntentId,
                'authorized',
                'Streamer fee for private stream with ' . $streamer->name,
                [
                    'streamer_id' => $streamer->id,
                    'streamer_name' => $streamer->name,
                    'payment_method' => 'stripe',
                    'requested_date' => $requestData['requested_date'],
                    'requested_time' => $requestData['requested_time'],
                    'duration_minutes' => $requestData['duration_minutes']
                ]
            );

            // Clean up session data
            session()->forget('pending_stream_requests.' . $paymentReference);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Payment confirmed. Your request has been sent to the streamer.',
                'request_id' => $streamRequest->id,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Accept a private stream request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function acceptRequest(Request $request, $id)
    {
        $streamRequest = PrivateStreamRequest::findOrFail($id);

        // Verify that the user is the streamer for this request
        if ($streamRequest->streamer_id !== Auth::id()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized action.'
            ]);
        }

        // Check if the request can be accepted
        if (!$streamRequest->canBeAccepted()) {
            return response()->json([
                'status' => false,
                'message' => 'This request cannot be accepted anymore.'
            ]);
        }

        try {
            DB::beginTransaction();

            // Update the request status
            $streamRequest->update([
                'status' => 'accepted',
                'accepted_at' => Carbon::now(),
            ]);

            // Update any related transactions
            Transaction::where('reference_id', $streamRequest->id)
                ->where('reference_type', PrivateStreamRequest::class)
                ->where('transaction_type', 'private_stream_fee')
                ->update(['status' => 'accepted']);

            // Capture payment and award tokens to streamer immediately
            if ($streamRequest->payment_method === 'stripe' && $streamRequest->payment_id) {
                try {
                    // Capture the payment from Stripe
                    Stripe::setApiKey(opt('STRIPE_SECRET_KEY'));
                    $paymentIntent = PaymentIntent::retrieve($streamRequest->payment_id);
                    
                    if ($paymentIntent->status === 'requires_capture') {
                        $paymentIntent->capture();
                        
                        // Update payment status
                        $streamRequest->update([
                            'payment_status' => 'captured'
                        ]);
                        
                        // Award tokens to streamer immediately
                        $this->convertPaymentToTokens($streamRequest);
                        
                        // Record platform commission
                        $this->recordPlatformCommission($streamRequest);
                        
                        \Log::info("Stripe payment captured and tokens awarded for stream request {$streamRequest->id}");
                    }
                } catch (\Exception $e) {
                    \Log::error("Failed to capture Stripe payment for stream request {$streamRequest->id}: " . $e->getMessage());
                    // Don't fail the acceptance, but log the error
                }
            } elseif ($streamRequest->payment_method === 'mercado_pago') {
                // For Mercado Pago, payment should already be captured via webhook
                // Just award tokens if payment is confirmed
                if ($streamRequest->payment_status === 'escrow_held') {
                    $this->convertPaymentToTokens($streamRequest);
                    $this->recordPlatformCommission($streamRequest);
                    
                    $streamRequest->update([
                        'payment_status' => 'captured'
                    ]);
                    
                    \Log::info("Mercado Pago payment processed and tokens awarded for stream request {$streamRequest->id}");
                }
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Request accepted successfully. Payment captured and tokens awarded.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Reject a private stream request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function rejectRequest(Request $request, $id)
    {
        $streamRequest = PrivateStreamRequest::findOrFail($id);

        // Verify that the user is the streamer for this request
        if ($streamRequest->streamer_id !== Auth::id()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized action.'
            ]);
        }

        // Check if the request is still pending
        if ($streamRequest->status !== 'pending') {
            return response()->json([
                'status' => false,
                'message' => 'This request cannot be rejected anymore.'
            ]);
        }

        try {
            DB::beginTransaction();

            // Update the request status
            $streamRequest->update([
                'status' => 'rejected',
            ]);

            // Refund the room rental tokens to the user
            $user = User::findOrFail($streamRequest->user_id);
            $user->tokens += $streamRequest->room_rental_tokens;
            $user->save();

            // Record token refund transaction
            $this->recordTransaction(
                $user->id,
                'room_rental_refund',
                $streamRequest->id,
                PrivateStreamRequest::class,
                $streamRequest->room_rental_tokens,
                'tokens',
                null,
                'completed',
                'Refund for rejected private stream request',
                [
                    'streamer_id' => $streamRequest->streamer_id,
                    'streamer_name' => $streamRequest->streamer->name,
                ]
            );

            // Update fee transaction status
            Transaction::where('reference_id', $streamRequest->id)
                ->where('reference_type', PrivateStreamRequest::class)
                ->where('transaction_type', 'private_stream_fee')
                ->update(['status' => 'rejected']);

            // Process refund based on payment method
            if ($streamRequest->payment_id) {
                if ($streamRequest->payment_method === 'stripe') {
                    // Cancel the Stripe payment intent to refund the payment
                    try {
                        Stripe::setApiKey(opt('STRIPE_SECRET_KEY'));
                        PaymentIntent::retrieve($streamRequest->payment_id)->cancel();
                        
                        // Record successful Stripe refund transaction
                        $this->recordTransaction(
                            $user->id,
                            'stripe_refund',
                            $streamRequest->id,
                            PrivateStreamRequest::class,
                            $streamRequest->streamer_fee,
                            'stripe',
                            $streamRequest->payment_id,
                            'completed',
                            'Stripe refund for rejected private stream request',
                            [
                                'payment_id' => $streamRequest->payment_id,
                                'reason' => 'Rejected by streamer',
                                'rejected_at' => Carbon::now()->toISOString()
                            ]
                        );
                    } catch (\Exception $e) {
                        // Log the error but don't fail the rejection
                        \Log::error("Stripe refund failed for private stream rejection: " . $e->getMessage(), [
                            'request_id' => $streamRequest->id,
                            'payment_id' => $streamRequest->payment_id
                        ]);
                    }
                } elseif ($streamRequest->payment_method === 'mercado_pago') {
                    // Process Mercado Pago refund
                    try {
                        $refundResult = $this->processMercadoPagoRefund($streamRequest->payment_id, $streamRequest->streamer_fee);
                        
                        if ($refundResult['success']) {
                            // Record successful Mercado Pago refund transaction
                            $this->recordTransaction(
                                $user->id,
                                'mercado_pago_refund',
                                $streamRequest->id,
                                PrivateStreamRequest::class,
                                $streamRequest->streamer_fee,
                                'mercado_pago',
                                $streamRequest->payment_id,
                                'completed',
                                'Mercado Pago refund for rejected private stream request',
                                [
                                    'payment_id' => $streamRequest->payment_id,
                                    'refund_id' => $refundResult['refund_id'],
                                    'reason' => 'Rejected by streamer',
                                    'rejected_at' => Carbon::now()->toISOString()
                                ]
                            );
                        } else {
                            throw new \Exception($refundResult['error']);
                        }
                    } catch (\Exception $e) {
                        // Create pending refund transaction for manual review
                        $this->recordTransaction(
                            $user->id,
                            'mercado_pago_refund_pending',
                            $streamRequest->id,
                            PrivateStreamRequest::class,
                            $streamRequest->streamer_fee,
                            'mercado_pago',
                            $streamRequest->payment_id,
                            'pending',
                            'Mercado Pago refund failed - requires manual processing',
                            [
                                'payment_id' => $streamRequest->payment_id,
                                'error' => $e->getMessage(),
                                'reason' => 'Rejected by streamer',
                                'rejected_at' => Carbon::now()->toISOString(),
                                'requires_manual_review' => true
                            ]
                        );
                        
                        // Log the error but don't fail the rejection
                        \Log::error("Mercado Pago refund failed for private stream rejection: " . $e->getMessage(), [
                            'request_id' => $streamRequest->id,
                            'payment_id' => $streamRequest->payment_id
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Request rejected successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Mark a private stream as completed.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function completeStream(Request $request, $id)
    {
        $streamRequest = PrivateStreamRequest::findOrFail($id);

        // Verify that the user is either the streamer or the user for this request
        if (!in_array(Auth::id(), [$streamRequest->streamer_id, $streamRequest->user_id])) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized action.'
            ]);
        }

        // Check if the request is accepted
        if ($streamRequest->status !== 'accepted') {
            return response()->json([
                'status' => false,
                'message' => 'This stream cannot be marked as completed.'
            ]);
        }

        try {
            DB::beginTransaction();

            // Update the request status
            $streamRequest->update([
                'status' => 'completed',
                'completed_at' => Carbon::now(),
            ]);

            // Update fee transaction status
            Transaction::where('reference_id', $streamRequest->id)
                ->where('reference_type', PrivateStreamRequest::class)
                ->where('transaction_type', 'private_stream_fee')
                ->update(['status' => 'completed']);

            // Note: Payment capture and token awarding now happens upon acceptance
            // No need to capture payment again here

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Stream marked as completed successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Mark a user as no-show for a private stream.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markNoShow(Request $request, $id)
    {
        $streamRequest = PrivateStreamRequest::findOrFail($id);

        // Verify that the user is the streamer for this request
        if ($streamRequest->streamer_id !== Auth::id()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized action.'
            ]);
        }

        // Check if the request is accepted
        if ($streamRequest->status !== 'accepted') {
            return response()->json([
                'status' => false,
                'message' => 'This user cannot be marked as no-show.'
            ]);
        }

        try {
            DB::beginTransaction();

            // Update the request status
            $streamRequest->update([
                'status' => 'no_show',
                'completed_at' => Carbon::now(),
            ]);

            // Update fee transaction status
            Transaction::where('reference_id', $streamRequest->id)
                ->where('reference_type', PrivateStreamRequest::class)
                ->where('transaction_type', 'private_stream_fee')
                ->update(['status' => 'completed']);

            // Note: Payment capture and token awarding now happens upon acceptance
            // Streamer keeps the tokens even for no-show scenarios

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'User marked as no-show successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Cancel an accepted private stream request before it starts.
     * This provides full refund to the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelStream(Request $request, $id)
    {
        $streamRequest = PrivateStreamRequest::findOrFail($id);

        // Verify that the user is the streamer for this request
        if ($streamRequest->streamer_id !== Auth::id()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized action.'
            ]);
        }

        // Check if the request is accepted
        if ($streamRequest->status !== 'accepted') {
            return response()->json([
                'status' => false,
                'message' => 'This stream cannot be cancelled.'
            ]);
        }

        // Check if the stream has already started
        $now = Carbon::now();
        $streamDateTime = $this->parseRequestedDateTime($streamRequest->requested_date, $streamRequest->requested_time);

        if ($now->gte($streamDateTime)) {
            return response()->json([
                'status' => false,
                'message' => 'Cannot cancel a stream that has already started. You can mark it as completed or no-show instead.'
            ]);
        }

        try {
            DB::beginTransaction();

            // Update the request status to cancelled
            $streamRequest->update([
                'status' => 'cancelled',
                'cancelled_at' => Carbon::now(),
            ]);

            // Refund the room rental tokens to the user
            $user = User::findOrFail($streamRequest->user_id);
            $user->tokens += $streamRequest->room_rental_tokens;
            $user->save();

            // Record token refund transaction
            $this->recordTransaction(
                $user->id,
                'room_rental_refund',
                $streamRequest->id,
                PrivateStreamRequest::class,
                $streamRequest->room_rental_tokens,
                'tokens',
                null,
                'completed',
                'Refund for cancelled private stream request',
                [
                    'streamer_id' => $streamRequest->streamer_id,
                    'streamer_name' => $streamRequest->streamer->name,
                    'reason' => 'Cancelled by streamer before stream start',
                ]
            );

            // Update fee transaction status to cancelled
            Transaction::where('reference_id', $streamRequest->id)
                ->where('reference_type', PrivateStreamRequest::class)
                ->where('transaction_type', 'private_stream_fee')
                ->update(['status' => 'cancelled']);

            // Refund tokens from streamer if they were already awarded
            if ($streamRequest->payment_status === 'captured' && $streamRequest->tokens_awarded > 0) {
                $streamer = User::findOrFail($streamRequest->streamer_id);
                
                // Check if streamer has enough tokens to refund
                if ($streamer->tokens >= $streamRequest->tokens_awarded) {
                    $streamer->tokens -= $streamRequest->tokens_awarded;
                    $streamer->save();
                    
                    // Record token refund transaction
                    $this->recordTransaction(
                        $streamer->id,
                        'private_stream_token_refund',
                        $streamRequest->id,
                        PrivateStreamRequest::class,
                        $streamRequest->tokens_awarded,
                        'tokens',
                        null,
                        'completed',
                        'Token refund due to cancelled stream',
                        [
                            'user_id' => $streamRequest->user_id,
                            'user_name' => $streamRequest->user->name,
                            'reason' => 'Stream cancelled by streamer',
                            'tokens_refunded' => $streamRequest->tokens_awarded,
                            'cancelled_at' => Carbon::now()->toISOString()
                        ]
                    );
                    
                    \Log::info("Refunded {$streamRequest->tokens_awarded} tokens from streamer for cancelled stream {$streamRequest->id}");
                } else {
                    \Log::warning("Streamer {$streamer->id} doesn't have enough tokens to refund for cancelled stream {$streamRequest->id}");
                }
            }

            // Process refund based on payment method
            if ($streamRequest->payment_id) {
                if ($streamRequest->payment_method === 'stripe') {
                    // Handle Stripe refund based on payment status
                    try {
                        Stripe::setApiKey(opt('STRIPE_SECRET_KEY'));
                        $paymentIntent = PaymentIntent::retrieve($streamRequest->payment_id);
                        
                        if ($paymentIntent->status === 'requires_capture') {
                            // Payment not captured yet, can cancel
                            $paymentIntent->cancel();
                        } elseif ($paymentIntent->status === 'succeeded') {
                            // Payment already captured, need to refund
                            Refund::create(['payment_intent' => $streamRequest->payment_id]);
                        }
                        
                        // Record successful Stripe refund transaction
                        $this->recordTransaction(
                            $user->id,
                            'stripe_refund',
                            $streamRequest->id,
                            PrivateStreamRequest::class,
                            $streamRequest->streamer_fee,
                            'stripe',
                            $streamRequest->payment_id,
                            'completed',
                            'Stripe refund for cancelled private stream request',
                            [
                                'payment_id' => $streamRequest->payment_id,
                                'payment_status' => $paymentIntent->status,
                                'reason' => 'Cancelled by streamer before stream start',
                                'cancelled_at' => Carbon::now()->toISOString()
                            ]
                        );
                    } catch (\Exception $e) {
                        // Log the error but don't fail the cancellation
                        \Log::error("Stripe refund failed for private stream cancellation: " . $e->getMessage(), [
                            'request_id' => $streamRequest->id,
                            'payment_id' => $streamRequest->payment_id
                        ]);
                    }
                } elseif ($streamRequest->payment_method === 'mercado_pago') {
                    // Process Mercado Pago refund
                    try {
                        $refundResult = $this->processMercadoPagoRefund($streamRequest->payment_id, $streamRequest->streamer_fee);
                        
                        if ($refundResult['success']) {
                            // Record successful Mercado Pago refund transaction
                            $this->recordTransaction(
                                $user->id,
                                'mercado_pago_refund',
                                $streamRequest->id,
                                PrivateStreamRequest::class,
                                $streamRequest->streamer_fee,
                                'mercado_pago',
                                $streamRequest->payment_id,
                                'completed',
                                'Mercado Pago refund for cancelled private stream request',
                                [
                                    'payment_id' => $streamRequest->payment_id,
                                    'refund_id' => $refundResult['refund_id'],
                                    'reason' => 'Cancelled by streamer before stream start',
                                    'cancelled_at' => Carbon::now()->toISOString()
                                ]
                            );
                        } else {
                            throw new \Exception($refundResult['error']);
                        }
                    } catch (\Exception $e) {
                        // Create pending refund transaction for manual review
                        $this->recordTransaction(
                            $user->id,
                            'mercado_pago_refund_pending',
                            $streamRequest->id,
                            PrivateStreamRequest::class,
                            $streamRequest->streamer_fee,
                            'mercado_pago',
                            $streamRequest->payment_id,
                            'pending',
                            'Mercado Pago refund failed - requires manual processing',
                            [
                                'payment_id' => $streamRequest->payment_id,
                                'error' => $e->getMessage(),
                                'reason' => 'Cancelled by streamer before stream start',
                                'cancelled_at' => Carbon::now()->toISOString(),
                                'requires_manual_review' => true
                            ]
                        );
                        
                        // Log the error but don't fail the cancellation
                        \Log::error("Mercado Pago refund failed for private stream cancellation: " . $e->getMessage(), [
                            'request_id' => $streamRequest->id,
                            'payment_id' => $streamRequest->payment_id
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Stream cancelled successfully. Full refund has been processed.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'An error occurred while cancelling the stream: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * List pending requests for a streamer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function listPendingRequests(Request $request)
    {
        $user = Auth::user();
        $now = Carbon::now();

        // Get all requests for the streamer
        $allRequests = PrivateStreamRequest::where('streamer_id', $user->id)
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Separate into categories
        $pendingRequests = $allRequests->where('status', 'pending');

        $upcomingRequests = $allRequests->where('status', 'accepted')
            ->filter(function ($request) use ($now) {
                $streamDateTime = $this->parseRequestedDateTime($request->requested_date, $request->requested_time);
                return $streamDateTime->gt($now);
            });

        $pastRequests = $allRequests->filter(function ($request) use ($now) {
            // Include all completed/ended statuses
            if (in_array($request->status, [
                'completed', 
                'rejected', 
                'no_show', 
                'cancelled',
                'awaiting_feedback',
                'completed_with_issues',
                'streamer_no_show',
                'user_no_show',
                'disputed',
                'resolved'
            ])) {
                return true;
            }
            
            // Include accepted streams that have passed their end time
            if ($request->status === 'accepted') {
                $streamDateTime = $this->parseRequestedDateTime($request->requested_date, $request->requested_time);
                $endDateTime = $streamDateTime->addMinutes($request->duration_minutes);
                return $endDateTime->lt($now);
            }
            
            return false;
        });

        return Inertia::render('Streaming/StreamerDashboard', [
            'pendingRequests' => $pendingRequests->values(),
            'upcomingRequests' => $upcomingRequests->values(),
            'pastRequests' => $pastRequests->values(),
            'totalRequests' => $allRequests->count(),
        ]);
    }

    /**
     * List upcoming accepted requests for a streamer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function listUpcomingStreams(Request $request)
    {
        $user = Auth::user();
        $requests = PrivateStreamRequest::where('streamer_id', $user->id)
            ->with(['user'])
            ->where('status', 'accepted')
            ->where('requested_date', '>=', Carbon::today())
            ->orderBy('requested_date')
            ->orderBy('requested_time')
            ->get();

        return Inertia::render('Streaming/UpcomingStreams', [
            'requests' => $requests
        ]);
    }

    /**
     * List requests made by the current user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function listMyRequests(Request $request)
    {
        $user = Auth::user();
        $requests = PrivateStreamRequest::where('user_id', $user->id)
            ->with(['streamer'])
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Streaming/MyRequests', [
            'requests' => $requests
        ]);
    }

    /**
     * List private stream bookings made by the current user (for non-streamers).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function listMyBookings(Request $request)
    {
        $user = Auth::user();
        $now = Carbon::now();

        // Get all requests made by this user
        $allRequests = PrivateStreamRequest::where('user_id', $user->id)
            ->with(['streamer'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Separate into categories
        $pendingRequests = $allRequests->where('status', 'pending');

        $upcomingRequests = $allRequests->where('status', 'accepted')
            ->filter(function ($request) use ($now) {
                $streamDateTime = $this->parseRequestedDateTime($request->requested_date, $request->requested_time);
                return $streamDateTime->gt($now);
            });

        $pastRequests = $allRequests->filter(function ($request) use ($now) {
            // Include all completed/ended statuses
            if (in_array($request->status, [
                'completed', 
                'rejected', 
                'no_show', 
                'cancelled',
                'awaiting_feedback',
                'completed_with_issues',
                'streamer_no_show',
                'user_no_show',
                'disputed',
                'resolved'
            ])) {
                return true;
            }
            
            // Include accepted streams that have passed their end time
            if ($request->status === 'accepted') {
                $streamDateTime = $this->parseRequestedDateTime($request->requested_date, $request->requested_time);
                $endDateTime = $streamDateTime->addMinutes($request->duration_minutes);
                return $endDateTime->lt($now);
            }
            
            return false;
        });

        return Inertia::render('Streaming/MyBookings', [
            'pendingRequests' => $pendingRequests->values(),
            'upcomingRequests' => $upcomingRequests->values(),
            'pastRequests' => $pastRequests->values(),
            'totalRequests' => $allRequests->count(),
        ]);
    }

    /**
     * Get user's requests for a specific streamer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $streamerId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserRequestsForStreamer(Request $request, $streamerId)
    {
        $user = Auth::user();

        $requests = PrivateStreamRequest::where('user_id', $user->id)
            ->where('streamer_id', $streamerId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'requests' => $requests
        ]);
    }

    /**
     * Get pending requests for a streamer as JSON.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPendingRequestsJson(Request $request)
    {
        $user = Auth::user();
        $requests = PrivateStreamRequest::where('streamer_id', $user->id)
            ->with(['user'])
            ->where('status', 'pending')
            ->orderBy('requested_date')
            ->orderBy('requested_time')
            ->get();

        return response()->json([
            'status' => true,
            'requests' => $requests
        ]);
    }

    /**
     * Get upcoming streams for a streamer as JSON.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUpcomingStreamsJson(Request $request)
    {
        $user = Auth::user();
        $now = Carbon::now();

        $requests = PrivateStreamRequest::where('streamer_id', $user->id)
            ->with(['user'])
            ->where('status', 'accepted')
            ->where(function ($query) use ($now) {
                // Get only streams that haven't started yet
                $query->where('requested_date', '>', $now->toDateString())
                    ->orWhere(function ($subQuery) use ($now) {
                        $subQuery->where('requested_date', '=', $now->toDateString())
                            ->whereRaw("CONCAT(requested_date, ' ', requested_time) > ?", [$now->toDateTimeString()]);
                    });
            })
            ->orderBy('requested_date')
            ->orderBy('requested_time')
            ->get();

        return response()->json([
            'status' => true,
            'requests' => $requests
        ]);
    }

    /**
     * Get all requests for a streamer as JSON (for history view).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMyRequestsJson(Request $request)
    {
        $user = Auth::user();
        $requests = PrivateStreamRequest::where('streamer_id', $user->id)
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'requests' => $requests
        ]);
    }

    /**
     * Access the streaming session page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Inertia\Response
     */
    public function streamingSession(Request $request, $id)
    {
        $streamRequest = PrivateStreamRequest::with(['user', 'streamer'])->findOrFail($id);
        $user = Auth::user();

        // Check access permissions
        $isOwner = $streamRequest->user_id === $user->id;
        $isStreamer = $streamRequest->streamer_id === $user->id;
        $isAdmin = $user->hasRole('admin') || $user->is_admin; // Adjust based on your admin check

        // Only allow access to authorized users
        if (!$isOwner && !$isStreamer && !$isAdmin) {
            abort(403, 'You are not authorized to access this streaming session.');
        }

        // Ensure stream_key exists (for existing records that might not have one)
        if (empty($streamRequest->stream_key)) {
            $streamRequest->stream_key = PrivateStreamRequest::generateSecureStreamKey();
            $streamRequest->save();
        }

        // Calculate stream timing information for the frontend
        $streamDateTime = $this->parseRequestedDateTime($streamRequest->requested_date, $streamRequest->requested_time);
        $streamEndTime = clone $streamDateTime;
        $streamEndTime->addMinutes($streamRequest->duration_minutes);
        $now = Carbon::now();

        // Determine stream timing status
        $canStartSoon = true; // Streamers can start anytime for preparation
        $canStartNow = $now->gte($streamDateTime) && $now->lt($streamEndTime);
        $isExpired = $now->gte($streamEndTime); // Changed from gt to gte for exact end time
        $timeUntilStart = $now->lt($streamDateTime) ? $now->diffInMinutes($streamDateTime) : 0;

        // Get HLS URL from environment variable
        $hls_url = env('HLS_URL', 'https://live.dg4e.com/hls');

        return Inertia::render('Streaming/StreamingSession', [
            'streamRequest' => $streamRequest,
            'isStreamer' => $isStreamer,
            'isOwner' => $isOwner,
            'isAdmin' => $isAdmin,
            'hls_url' => $hls_url,
            'rtmp_url' => 'rtmp://live.dg4e.com/live',
            'streamTiming' => [
                'canStartSoon' => $canStartSoon,
                'canStartNow' => $canStartNow,
                'canStreamerStart' => $streamRequest->canStreamerStart(),
                'canStartActualStream' => $streamRequest->canStartActualStream(),
                'canUserJoin' => $streamRequest->canUserJoin(),
                'isInPreparationPeriod' => $streamRequest->isInPreparationPeriod(),
                'timeUntilUserCanJoin' => $streamRequest->getTimeUntilUserCanJoin(),
                'isExpired' => $isExpired,
                'timeUntilStart' => $timeUntilStart,
                'streamDateTime' => $streamDateTime->toISOString(),
                'streamEndTime' => $streamEndTime->toISOString(),
            ],
        ]);
    }

    // ============================================================================
    // CHAT & INTERACTION METHODS
    // ============================================================================

    /**
     * Send a chat message for a private stream session
     */
    public function sendChatMessage(Request $request, $id)
    {
        try {
            $request->validate([
                'message' => 'required|string|max:500'
            ]);

            $streamRequest = PrivateStreamRequest::findOrFail($id);
            $user = Auth::user();

            // Check if user has access to this stream (owner, streamer, or admin)
            if (!$this->hasAccessToStream($streamRequest, $user)) {
                return response()->json([
                    'status' => false,
                    'message' => __('You do not have access to this stream')
                ], 403);
            }

            // Prevent sending messages when stream is pending
            if ($streamRequest->status === 'pending') {
                return response()->json([
                    'status' => false,
                    'message' => __('You cannot send messages when the stream request is pending approval')
                ], 400);
            }

            // Ensure stream_key exists and use it for the room name
            if (empty($streamRequest->stream_key)) {
                $streamRequest->stream_key = PrivateStreamRequest::generateSecureStreamKey();
                $streamRequest->save();
            }
            $roomName = 'stream-session-' . $streamRequest->stream_key;

            // Create chat message using existing Chat model
            $chat = Chat::create([
                'roomName' => $roomName,
                'chat_type' => 'private_stream',
                'user_id' => $user->id,
                'streamer_id' => $streamRequest->streamer_id,
                'message' => $request->message,
                'tip' => 0, // No tips in private stream chat for now
            ]);

            // Broadcast the chat message
            broadcast(new PrivateStreamChatEvent($chat));

            return response()->json([
                'status' => true,
                'message' => __('Message sent successfully'),
                'chat' => $chat
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending private stream chat message: ' . $e->getMessage(), [
                'stream_id' => $id,
                'user_id' => auth()->id()
            ]);
            return response()->json([
                'status' => false,
                'message' => __('Failed to send message')
            ], 500);
        }
    }

    /**
     * Get latest chat messages for a private stream session
     */
    public function getChatMessages($id)
    {
        try {
            $streamRequest = PrivateStreamRequest::findOrFail($id);
            $user = Auth::user();

            // Check if user has access to this stream
            if (!$this->hasAccessToStream($streamRequest, $user)) {
                return response()->json([
                    'status' => false,
                    'message' => __('You do not have access to this stream')
                ], 403);
            }

            // Ensure stream_key exists and use it for the room name
            if (empty($streamRequest->stream_key)) {
                $streamRequest->stream_key = PrivateStreamRequest::generateSecureStreamKey();
                $streamRequest->save();
            }
            $roomName = 'stream-session-' . $streamRequest->stream_key;

            // Get latest 50 messages for this private stream
            $messages = Chat::where('roomName', $roomName)
                ->where('chat_type', 'private_stream')
                ->with(['user:id,username,name,profile_picture'])
                ->latest()
                ->take(50)
                ->get()
                ->reverse()
                ->values();

            return response()->json([
                'status' => true,
                'messages' => $messages
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching private stream chat messages: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => __('Failed to load messages')
            ], 500);
        }
    }

    /**
     * Send a tip in a private stream session
     */
    public function sendTip(Request $request, $id)
    {
        try {
            $request->validate([
                'tip' => 'required|numeric|min:1',
                'message' => 'required|string|max:500',
            ]);

            $streamRequest = PrivateStreamRequest::findOrFail($id);
            $user = Auth::user();

            // Check if user has access to this stream
            if (!$this->hasAccessToStream($streamRequest, $user)) {
                return response()->json([
                    'status' => false,
                    'message' => __('You do not have access to this stream')
                ], 403);
            }

            // Prevent sending tips when stream is pending
            if ($streamRequest->status === 'pending') {
                return response()->json([
                    'status' => false,
                    'message' => __('You cannot send tips when the stream request is pending approval')
                ], 400);
            }

            // Prevent tipping yourself
            if ($user->id == $streamRequest->streamer_id) {
                return response()->json([
                    'status' => false,
                    'message' => __("Do not tip yourself!")
                ]);
            }

            // Validate balance is enough
            if ($request->tip > $user->tokens) {
                return response()->json([
                    'status' => false,
                    'message' => __("Your balance of :tokens tokens is not enough for a tip of :tip", [
                        'tokens' => $user->tokens,
                        'tip' => $request->tip
                    ])
                ]);
            }

            // Get streamer
            $streamer = User::findOrFail($streamRequest->streamer_id);

            // Record tip in Tips table
            $tip = new Tips();
            $tip->user_id = $user->id;
            $tip->streamer_id = $streamer->id;
            $tip->tokens = $request->tip;
            $tip->save();

            // Subtract tipper balance
            $user->decrement('tokens', $request->tip);

            // Increment streamer balance
            $streamer->increment('tokens', $request->tip);

            // Record transaction for tipper (outgoing)
            $this->recordTransaction(
                $user->id,
                'tip_sent',
                $tip->id,
                'tip',
                -$request->tip, // Negative amount for sender
                'tokens',
                null,
                'completed',
                __('Tip sent to :streamer in private stream', ['streamer' => $streamer->username]),
                [
                    'private_stream_id' => $streamRequest->id,
                    'streamer_id' => $streamer->id,
                    'tip_amount' => $request->tip
                ]
            );

            // Record transaction for streamer (incoming)
            $this->recordTransaction(
                $streamer->id,
                'tip_received',
                $tip->id,
                'tip',
                $request->tip, // Positive amount for receiver
                'tokens',
                null,
                'completed',
                __('Tip received from :user in private stream', ['user' => $user->username]),
                [
                    'private_stream_id' => $streamRequest->id,
                    'user_id' => $user->id,
                    'tip_amount' => $request->tip
                ]
            );

            // Create chat message with tip
            // Ensure stream_key exists and use it for the room name
            if (empty($streamRequest->stream_key)) {
                $streamRequest->stream_key = PrivateStreamRequest::generateSecureStreamKey();
                $streamRequest->save();
            }
            $roomName = 'stream-session-' . $streamRequest->stream_key;
            $message = Chat::create([
                'roomName' => $roomName,
                'chat_type' => 'private_stream',
                'user_id' => $user->id,
                'streamer_id' => $streamer->id,
                'message' => $request->message,
                'tip' => $request->tip,
            ]);

            // Broadcast the tip message
            broadcast(new PrivateStreamChatEvent($message));

            return response()->json([
                'status' => true,
                'message' => __('Thanks, your tip has been sent!'),
                'chat' => $message
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending private stream tip: ' . $e->getMessage(), [
                'stream_id' => $id,
                'user_id' => auth()->id(),
                'tip_amount' => $request->tip ?? 0
            ]);
            return response()->json([
                'status' => false,
                'message' => __('Failed to send tip')
            ], 500);
        }
    }

    // ============================================================================
    // PAYMENT PROCESSING METHODS
    // ============================================================================

    /**
     * Process Stripe payment with escrow for private stream.
     *
     * @param  \App\Models\PrivateStreamRequest  $streamRequest
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $streamer
     * @param  float  $streamerFee
     * @return \Stripe\PaymentIntent
     */
    private function processStripePayment(PrivateStreamRequest $streamRequest, User $user, User $streamer, $streamerFee)
    {
        Stripe::setApiKey(opt('STRIPE_SECRET_KEY'));

        $paymentIntent = PaymentIntent::create([
            'amount' => $streamerFee * 100, // Convert to cents
            'currency' => 'usd',
            'description' => 'Private stream with ' . $streamer->name,
            'metadata' => [
                'user_id' => $user->id,
                'streamer_id' => $streamer->id,
                'stream_request_id' => $streamRequest->id,
                'type' => 'private_stream',
            ],
            'capture_method' => 'manual', // For holding the payment in escrow
        ]);

        // Update the request with the payment info
        $streamRequest->update([
            'payment_id' => $paymentIntent->id,
            'payment_status' => 'requires_confirmation',
            'payment_method' => 'stripe',
        ]);

        return $paymentIntent;
    }



    /**
     * Process Mercado Pago payment for private stream with admin escrow.
     *
     * @param  array  $requestData
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $streamer
     * @param  float  $streamerFee
     * @param  string  $paymentReference
     * @param  string  $email
     * @return array
     */
    private function processMercadoPagoPayment($requestData, User $user, User $streamer, $streamerFee, $paymentReference, $email)
    {
        // Get Mercado Pago API key from configuration
        $apiKey = opt('MERCADO_SECRET_KEY');
        if (!$apiKey) {
            throw new \Exception('Mercado Pago API key not configured. Payment cannot be processed.');
        }

        // Create preference data - payment goes to admin account for escrow
        $data = [
            "auto_return" => "approved",
            "back_urls" => [
                "success" => route('mercado.private-stream.success'),
                "failure" => route('mercado.private-stream.failure'),
            ],
            "items" => [
                [
                    "title" => "Private stream with " . $streamer->name,
                    "description" => "Private stream session payment (held in escrow)",
                    "currency_id" => "BRL",
                    "quantity" => 1,
                    "unit_price" => $streamerFee
                ]
            ],
            "payer" => [
                    "entity_type" => "individual",
                    "type" => "customer",
                    "email" => $email
                ],
            "external_reference" => $paymentReference,
            // No marketplace_fee since payment goes to admin account for escrow
        ];

        // Test mode handling
        $isTestMode = env('APP_ENV') !== 'production';
        if ($isTestMode) {
            $data['test_mode'] = true;
        }

        // Convert to JSON
        $json_data = json_encode($data);

        // Initialize cURL
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.mercadopago.com/checkout/preferences',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $json_data,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'X-Idempotency-Key: ' . $this->generateIdempotencyKey(),
                'Authorization: Bearer ' . $apiKey // Use configured API key
            ),
        ));

        // Execute cURL request
        $response = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlErrorNumber = curl_errno($curl);
        $curlError = curl_error($curl);
        curl_close($curl);

        // Check for curl errors
        if ($curlErrorNumber) {
            Log::error('Mercado Pago cURL error: ' . $curlError, [
                'payment_reference' => $paymentReference,
                'user_id' => $user->id,
                'streamer_id' => $streamer->id
            ]);
            throw new \Exception('Connection error: ' . $curlError);
        }

        $preferenceData = json_decode($response, true);



        // Check for API errors
        if ($httpStatus >= 400 || isset($preferenceData['error'])) {
            $errorMessage = $preferenceData['message'] ?? $preferenceData['error'] ?? 'Unknown API error';
            Log::error('Mercado Pago API error: ' . $errorMessage, [
                'payment_reference' => $paymentReference,
                'http_status' => $httpStatus,
                'response' => $preferenceData
            ]);
            throw new \Exception('MercadoPago API error: ' . $errorMessage);
        }

        // Check for successful response with init_point
        if (isset($preferenceData['id']) && isset($preferenceData['init_point'])) {
            // Return data for main controller to use
            return [
                'success' => true,
                'payment_url' => $preferenceData['init_point'],
                'preference_url' => $preferenceData['init_point'],
                'preference_id' => $preferenceData['id']
            ];
        } else {
            Log::error('Invalid Mercado Pago API response: Missing preference ID or init_point', [
                'payment_reference' => $paymentReference,
                'response' => $preferenceData
            ]);
            throw new \Exception('Invalid API response: Missing preference ID or init_point');
        }
    }

    /**
     * Get available payment options for a user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPaymentOptions(Request $request)
    {
        $request->validate([
            'streamer_fee' => 'required|numeric|min:1'
        ]);

        $options = [
            'stripe' => [
                'available' => true,
                'has_escrow' => true,
                'description' => 'Credit card payment with escrow protection'
            ],
            'mercado_pago' => [
                'available' => true,
                'has_escrow' => true,
                'description' => 'Mercado Pago payment with escrow protection'
            ]
        ];

        $recommended = 'stripe'; // Default recommendation

        return response()->json([
            'status' => true,
            'payment_options' => $options,
            'recommended' => $recommended
        ]);
    }

    /**
     * Record platform commission for completed private stream.
     *
     * @param  \App\Models\PrivateStreamRequest  $streamRequest
     * @return void
     */
    private function recordPlatformCommission(PrivateStreamRequest $streamRequest)
    {
        // Get commission percentage from configuration
        $adminCommissionPercent = opt('admin_commission_private_room', 50);
        
        // Calculate commission on streamer fee only (room rental is separate platform revenue)
        $streamerFee = $streamRequest->streamer_fee;
        $adminCommission = ($streamerFee * $adminCommissionPercent) / 100;
        
        // Find admin user (assuming admin has ID 1 or is_admin = 'yes')
        $admin = User::where('is_admin', 'yes')->first();
        if (!$admin) {
            $admin = User::find(1); // Fallback to user ID 1
        }
        
        if ($admin && $adminCommission > 0) {
            // Create commission record
            \App\Models\Commission::create([
                'type' => 'Private Streaming',
                'video_id' => $streamRequest->id, // Using video_id field for reference (legacy schema)
                'streamer_id' => $streamRequest->streamer_id,
                'tokens' => $adminCommission, // Note: This stores currency amount, not tokens
                'admin_id' => $admin->id,
            ]);


        }
    }

    /**
     * Handle successful Mercado Pago payment for private streams.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function mercadoPaymentSuccess(Request $request)
    {
        try {
            // Get payment info from MercadoPago callback
            $paymentId = $request->payment_id ?? $request->collection_id ?? null;
            $externalReference = $request->external_reference ?? null;

            if (!$paymentId || !$externalReference) {
                return redirect()->route('private-stream.my-bookings')->with('error', 'Payment information not found. Please contact support.');
            }

            // Get request data from session using the external reference
            $requestData = session('pending_stream_requests.' . $externalReference);
            
            if (!$requestData) {
                return redirect()->route('private-stream.my-bookings')->with('error', 'Request data not found. Payment may have already been processed.');
            }

            // Check if request was already created (prevent duplicate processing)
            $existingRequest = PrivateStreamRequest::where('payment_id', $paymentId)->first();
            if ($existingRequest) {
                return redirect()->route('private-stream.my-bookings')->with('message', 'Your payment has been processed and your request is with the streamer.');
            }

            DB::beginTransaction();
            try {
                // Get user and streamer
                $user = User::find($requestData['user_id']);
                $streamer = User::find($requestData['streamer_id']);
                
                if (!$user || !$streamer) {
                    throw new \Exception('User or streamer not found');
                }

                // Final check for duplicates (race condition protection)
                $duplicateCheck = PrivateStreamRequest::where('user_id', $requestData['user_id'])
                    ->where('streamer_id', $requestData['streamer_id'])
                    ->where('requested_date', $requestData['requested_date'])
                    ->where('requested_time', $requestData['requested_time'])
                    ->whereIn('status', ['pending', 'accepted'])
                    ->lockForUpdate()
                    ->first();

                if ($duplicateCheck) {
                    // Clean up session data and redirect
                    session()->forget('pending_stream_requests.' . $externalReference);
                    DB::rollBack();
                    return redirect()->route('private-stream.my-bookings')->with('message', 'A request for this time slot already exists.');
                }

                // Check if user still has enough tokens
                if ($user->tokens < $requestData['room_rental_tokens']) {
                    throw new \Exception('Insufficient tokens for room rental');
                }

                // Create the private stream request
                $streamRequest = PrivateStreamRequest::create([
                    'user_id' => $requestData['user_id'],
                    'streamer_id' => $requestData['streamer_id'],
                    'availability_id' => $requestData['availability_id'],
                    'requested_date' => $requestData['requested_date'],
                    'requested_time' => $requestData['requested_time'],
                    'duration_minutes' => $requestData['duration_minutes'],
                    'room_rental_tokens' => $requestData['room_rental_tokens'],
                    'streamer_fee' => $requestData['streamer_fee'],
                    'currency' => $requestData['currency'],
                    'message' => $requestData['message'],
                    'payment_method' => 'mercado_pago',
                    'payment_id' => $paymentId,
                    'payment_status' => 'escrow_held',
                    'status' => 'pending',
                    'expires_at' => $requestData['expires_at'],
                ]);

                // Deduct tokens for room rental
                $user->tokens -= $requestData['room_rental_tokens'];
                $user->save();

                // Record token deduction transaction
                $this->recordTransaction(
                    $user->id,
                    'room_rental',
                    $streamRequest->id,
                    PrivateStreamRequest::class,
                    $requestData['room_rental_tokens'],
                    'tokens',
                    null,
                    'completed',
                    'Room rental fee for private stream with ' . $streamer->name,
                    [
                        'streamer_id' => $streamer->id,
                        'streamer_name' => $streamer->name,
                        'requested_date' => $requestData['requested_date'],
                        'requested_time' => $requestData['requested_time'],
                        'duration_minutes' => $requestData['duration_minutes']
                    ]
                );

                // Record the payment transaction
                $this->recordTransaction(
                    $user->id,
                    'private_stream_fee',
                    $streamRequest->id,
                    PrivateStreamRequest::class,
                    $requestData['streamer_fee'],
                    'mercado_pago',
                    $paymentId,
                    'escrow_held',
                    'Streamer fee for private stream with ' . $streamer->name,
                    [
                        'streamer_id' => $streamer->id,
                        'streamer_name' => $streamer->name,
                        'payment_method' => 'mercado_pago',
                        'requested_date' => $requestData['requested_date'],
                        'requested_time' => $requestData['requested_time'],
                        'duration_minutes' => $requestData['duration_minutes'],
                        'external_reference' => $externalReference
                    ]
                );

                // Create admin escrow record for tracking
                $admin = User::where('is_supper_admin', 'yes')->first();
                if ($admin) {
                    $this->recordTransaction(
                        $admin->id,
                        'private_stream_escrow',
                        $streamRequest->id,
                        PrivateStreamRequest::class,
                        $requestData['streamer_fee'],
                        'mercadopago',
                        $paymentId,
                        'escrow_held',
                        'Private stream payment held in escrow for ' . $streamer->name,
                        [
                            'user_id' => $user->id,
                            'streamer_id' => $streamer->id,
                            'external_reference' => $externalReference,
                            'can_be_released' => false // Will be true when stream is completed
                        ]
                    );
                }

                // Clean up session data
                session()->forget('pending_stream_requests.' . $externalReference);

                DB::commit();

                return redirect()->route('private-stream.my-bookings')->with('message', 'Payment successful! Your private stream request has been sent to the streamer. Payment is held securely until stream completion.');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Mercado Pago private stream payment processing failed: ' . $e->getMessage(), [
                    'payment_id' => $paymentId,
                    'external_reference' => $externalReference
                ]);
                return redirect()->route('private-stream.my-bookings')->with('error', 'Error processing payment confirmation: ' . $e->getMessage());
            }

        } catch (\Exception $e) {
            Log::error('Mercado Pago private stream success handler error: ' . $e->getMessage());
            return redirect()->route('private-stream.my-bookings')->with('error', 'Error processing payment.');
        }
    }

    /**
     * Handle failed Mercado Pago payment for private streams.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function mercadoPaymentFailure(Request $request)
    {
        // Clean up session data if external reference is provided
        $externalReference = $request->external_reference ?? null;
        if ($externalReference) {
            session()->forget('pending_stream_requests.' . $externalReference);
        }
        
        return redirect()->route('private-stream.my-bookings')->with('error', 'Payment failed. Please try again or contact support.');
    }

    /**
     * Handle webhook notifications from Mercado Pago for private streams.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function mercadoWebhook(Request $request)
    {
        try {
            // Get the notification type
            $type = $request->input('type');

            // Only process payment notifications
            if ($type == 'payment') {
                $paymentId = $request->input('data.id');
                $externalReference = $request->input('external_reference');

                if (!$externalReference) {
                    return response()->json(['status' => 'error', 'message' => 'Missing external reference'], 400);
                }

                // Check if request already exists (already processed)
                $existingRequest = PrivateStreamRequest::where('payment_id', $paymentId)->first();
                if ($existingRequest) {
                    return response()->json(['status' => 'success', 'message' => 'Already processed']);
                }

                // Get request data from session
                $requestData = session('pending_stream_requests.' . $externalReference);
                
                if (!$requestData) {
                    Log::error('Webhook: Request data not found in session', [
                        'external_reference' => $externalReference,
                        'payment_id' => $paymentId
                    ]);
                    return response()->json(['status' => 'error', 'message' => 'Request data not found'], 404);
                }

                // Process webhook payment confirmation with request data
                $this->processMercadoWebhookPayment($requestData, $paymentId, $externalReference);
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Mercado Pago private stream webhook error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Process Mercado Pago webhook payment confirmation.
     *
     * @param  array  $requestData
     * @param  string  $paymentId
     * @param  string  $externalReference
     * @return void
     */
    private function processMercadoWebhookPayment($requestData, $paymentId, $externalReference)
    {
        DB::beginTransaction();
        try {
            // Get user and streamer
            $user = User::find($requestData['user_id']);
            $streamer = User::find($requestData['streamer_id']);
            
            if (!$user || !$streamer) {
                throw new \Exception('User or streamer not found');
            }

            // Check if user still has enough tokens
            if ($user->tokens < $requestData['room_rental_tokens']) {
                throw new \Exception('Insufficient tokens for room rental');
            }

            // Check for duplicate pending requests before creating
            $existingRequest = PrivateStreamRequest::where('user_id', $requestData['user_id'])
                ->where('streamer_id', $requestData['streamer_id'])
                ->where('requested_date', $requestData['requested_date'])
                ->where('requested_time', $requestData['requested_time'])
                ->whereIn('status', ['pending', 'accepted'])
                ->lockForUpdate() // Lock to prevent race conditions
                ->first();

            if ($existingRequest) {
                // Clean up session data and return success (request already exists)
                session()->forget('pending_stream_requests.' . $externalReference);
                Log::info('Mercado webhook: Duplicate request prevented', [
                    'existing_request_id' => $existingRequest->id,
                    'payment_id' => $paymentId
                ]);
                return;
            }

            // Create the private stream request
            $streamRequest = PrivateStreamRequest::create([
                'user_id' => $requestData['user_id'],
                'streamer_id' => $requestData['streamer_id'],
                'availability_id' => $requestData['availability_id'],
                'requested_date' => $requestData['requested_date'],
                'requested_time' => $requestData['requested_time'],
                'duration_minutes' => $requestData['duration_minutes'],
                'room_rental_tokens' => $requestData['room_rental_tokens'],
                'streamer_fee' => $requestData['streamer_fee'],
                'currency' => $requestData['currency'],
                'message' => $requestData['message'],
                'payment_method' => 'mercado_pago',
                'payment_id' => $paymentId,
                'payment_status' => 'escrow_held',
                'status' => 'pending',
                'expires_at' => $requestData['expires_at'],
            ]);

            // Deduct tokens for room rental
            $user->tokens -= $requestData['room_rental_tokens'];
            $user->save();

            // Record token deduction transaction
            $this->recordTransaction(
                $user->id,
                'room_rental',
                $streamRequest->id,
                PrivateStreamRequest::class,
                $requestData['room_rental_tokens'],
                'tokens',
                null,
                'completed',
                'Room rental fee for private stream with ' . $streamer->name,
                [
                    'streamer_id' => $streamer->id,
                    'streamer_name' => $streamer->name,
                    'requested_date' => $requestData['requested_date'],
                    'requested_time' => $requestData['requested_time'],
                    'duration_minutes' => $requestData['duration_minutes']
                ]
            );

            // Record the payment transaction
            $this->recordTransaction(
                $user->id,
                'private_stream_fee',
                $streamRequest->id,
                PrivateStreamRequest::class,
                $requestData['streamer_fee'],
                'mercado_pago',
                $paymentId,
                'escrow_held',
                'Streamer fee for private stream with ' . $streamer->name,
                [
                    'streamer_id' => $streamer->id,
                    'streamer_name' => $streamer->name,
                    'payment_method' => 'mercado_pago',
                    'requested_date' => $requestData['requested_date'],
                    'requested_time' => $requestData['requested_time'],
                    'duration_minutes' => $requestData['duration_minutes'],
                    'external_reference' => $externalReference
                ]
            );

            // Create admin escrow record
            $admin = User::where('is_supper_admin', 'yes')->first();
            if ($admin) {
                $this->recordTransaction(
                    $admin->id,
                    'private_stream_escrow',
                    $streamRequest->id,
                    PrivateStreamRequest::class,
                    $requestData['streamer_fee'],
                    'mercadopago',
                    $paymentId,
                    'escrow_held',
                    'Private stream payment held in escrow for ' . $streamer->name,
                    [
                        'user_id' => $user->id,
                        'streamer_id' => $streamer->id,
                        'external_reference' => $externalReference,
                        'can_be_released' => false
                    ]
                );
            }

            // Clean up session data
            session()->forget('pending_stream_requests.' . $externalReference);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Mercado Pago webhook payment processing failed: ' . $e->getMessage(), [
                'payment_id' => $paymentId,
                'external_reference' => $externalReference
            ]);
            throw $e;
        }
    }

    /**
     * Process Mercado Pago refund for a payment
     *
     * @param string $paymentId
     * @param float $amount
     * @return array
     */
    private function processMercadoPagoRefund($paymentId, $amount)
    {
        try {
            // Get API token
            $apiToken = opt('MERCADO_SECRET_KEY');
            
            if (!$apiToken) {
                throw new \Exception('Mercado Pago API token not configured');
            }

            // Prepare refund data
            $refundData = [
                'amount' => floatval($amount)
            ];

            // Initialize cURL for refund request
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.mercadopago.com/v1/payments/{$paymentId}/refunds",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($refundData),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $apiToken
                ),
            ));

            $response = curl_exec($curl);
            $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curlErrorNumber = curl_errno($curl);
            $curlError = curl_error($curl);
            curl_close($curl);

            // Check for cURL errors
            if ($curlErrorNumber) {
                throw new \Exception('cURL error: ' . $curlError);
            }

            $refundResponse = json_decode($response, true);

            // Check for API errors
            if ($httpStatus >= 400) {
                $errorMessage = $refundResponse['message'] ?? $refundResponse['error'] ?? 'Unknown API error';
                throw new \Exception("API error (HTTP {$httpStatus}): {$errorMessage}");
            }

            // Check if refund was successful
            if (isset($refundResponse['id']) && isset($refundResponse['status'])) {
                if ($refundResponse['status'] === 'approved') {
                    return [
                        'success' => true,
                        'refund_id' => $refundResponse['id'],
                        'status' => $refundResponse['status'],
                        'amount' => $refundResponse['amount']
                    ];
                } else {
                    throw new \Exception("Refund not approved. Status: " . $refundResponse['status']);
                }
            } else {
                throw new \Exception('Invalid refund response: Missing ID or status');
            }

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Convert payment amount to tokens and give to streamer after platform commission
     *
     * @param PrivateStreamRequest $streamRequest
     * @return void
     */
    private function convertPaymentToTokens(PrivateStreamRequest $streamRequest)
    {
        try {
            // Check if tokens have already been awarded to prevent duplicates
            if ($streamRequest->tokens_awarded > 0) {
                \Log::warning("Attempted duplicate token award for stream request {$streamRequest->id}. Tokens already awarded: {$streamRequest->tokens_awarded}");
                return;
            }

            // Get admin commission percentage from configuration
            $adminCommissionPercent = opt('admin_commission_private_room', 50);
            
            // Calculate streamer's share after commission
            $streamerFee = $streamRequest->streamer_fee;
            $adminCommission = ($streamerFee * $adminCommissionPercent) / 100;
            $streamerEarnings = $streamerFee - $adminCommission;
            
            // Get token conversion rate from admin settings
            $tokenValue = opt('token_value', 0.01); // Default $0.01 per token if not set
            
            if ($tokenValue <= 0) {
                throw new \Exception('Token value not configured properly. Cannot convert payment to tokens.');
            }
            
            // Convert USD amount to tokens
            $tokensToAdd = floor($streamerEarnings / $tokenValue);
            
            if ($tokensToAdd > 0) {
                // Add tokens to streamer account
                $streamer = User::findOrFail($streamRequest->streamer_id);
                $streamer->tokens += $tokensToAdd;
                $streamer->save();
                
                // Update stream request with tokens awarded for refund tracking
                $streamRequest->update([
                    'tokens_awarded' => $tokensToAdd
                ]);
                
                // Record token earning transaction
                $this->recordTransaction(
                    $streamer->id,
                    'private_stream_earnings',
                    $streamRequest->id,
                    PrivateStreamRequest::class,
                    $tokensToAdd,
                    'tokens',
                    null,
                    'completed',
                    'Tokens earned from accepted private stream',
                    [
                        'user_id' => $streamRequest->user_id,
                        'user_name' => $streamRequest->user->name,
                        'original_payment_amount' => $streamerFee,
                        'admin_commission_percent' => $adminCommissionPercent,
                        'admin_commission_amount' => $adminCommission,
                        'streamer_earnings_usd' => $streamerEarnings,
                        'token_value' => $tokenValue,
                        'tokens_earned' => $tokensToAdd,
                        'conversion_date' => Carbon::now()->toISOString()
                    ]
                );
                
                \Log::info("Awarded {$tokensToAdd} tokens to streamer {$streamer->id} for stream request {$streamRequest->id}");
            }
            
        } catch (\Exception $e) {
            \Log::error("Failed to convert private stream payment to tokens: " . $e->getMessage(), [
                'stream_request_id' => $streamRequest->id,
                'streamer_id' => $streamRequest->streamer_id
            ]);
            // Don't throw the exception to avoid failing the completion process
        }
    }

    // ============================================================================
    // STREAM SESSION LIFECYCLE
    // ============================================================================

    /**
     * Start countdown for a private stream session.
     */
    public function startCountdown(Request $request, $id)
    {
        $streamRequest = PrivateStreamRequest::findOrFail($id);
        $user = auth()->user();

        // Check if user has access and is the streamer
        if (!$this->hasAccessToStream($streamRequest, $user) || $streamRequest->streamer_id !== $user->id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        // Check if countdown can be started (anytime before scheduled end time)
        if (!$streamRequest->shouldStartCountdown()) {
            $scheduledTime = Carbon::createFromFormat('Y-m-d H:i:s', 
                $streamRequest->requested_date->format('Y-m-d') . ' ' . $streamRequest->requested_time);
            $scheduledEndTime = $scheduledTime->copy()->addMinutes($streamRequest->duration_minutes);
            
            if (Carbon::now()->gte($scheduledEndTime)) {
                return response()->json([
                    'status' => false, 
                    'message' => 'Stream time has already ended and cannot be started'
                ]);
            }
            
            return response()->json([
                'status' => false, 
                'message' => 'Stream cannot be started at this time'
            ]);
        }

        $streamRequest->startCountdown();

        // Broadcast the state change
        broadcast(new PrivateStreamStateChanged(
            $streamRequest->fresh(),
            'countdown_started',
            ['message' => 'Stream preparation has begun']
        ));

        return response()->json([
            'status' => true,
            'message' => 'Countdown started successfully',
            'stream_request' => $streamRequest->fresh()
        ]);
    }

    /**
     * Mark user as joined the stream.
     */
    public function markUserJoined(Request $request, $id)
    {
        $streamRequest = PrivateStreamRequest::findOrFail($id);
        $user = auth()->user();

        // Check if user has access and is the user (not streamer)
        if (!$this->hasAccessToStream($streamRequest, $user) || $streamRequest->user_id !== $user->id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        // Check if user can join yet (only at scheduled time)
        if (!$streamRequest->canUserJoin()) {
            $scheduledTime = Carbon::createFromFormat('Y-m-d H:i:s', 
                $streamRequest->requested_date->format('Y-m-d') . ' ' . $streamRequest->requested_time);
            $scheduledEndTime = $scheduledTime->copy()->addMinutes($streamRequest->duration_minutes);
            
            if (Carbon::now()->gte($scheduledEndTime)) {
                return response()->json([
                    'status' => false, 
                    'message' => 'Stream time has already ended and cannot be joined'
                ]);
            }
            
            $timeUntil = $streamRequest->getTimeUntilUserCanJoin();
            $minutes = floor($timeUntil / 60);
            $seconds = $timeUntil % 60;
            
            return response()->json([
                'status' => false, 
                'message' => "Stream has not started yet. Please wait {$minutes}m {$seconds}s",
                'time_until_start' => $timeUntil
            ]);
        }

        $streamRequest->markUserJoined();

        // Broadcast the state change
        broadcast(new PrivateStreamStateChanged(
            $streamRequest->fresh(),
            'user_joined',
            ['message' => 'User has joined the stream']
        ));

        return response()->json([
            'status' => true,
            'message' => 'User marked as joined',
            'stream_request' => $streamRequest->fresh()
        ]);
    }

    /**
     * Start the actual stream session.
     */
    public function startStream(Request $request, $id)
    {
        $streamRequest = PrivateStreamRequest::findOrFail($id);
        $user = auth()->user();

        // Check if user has access and is the streamer
        if (!$this->hasAccessToStream($streamRequest, $user) || $streamRequest->streamer_id !== $user->id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        // Check if stream can be started (only at actual scheduled time)
        if (!$streamRequest->canStartActualStream()) {
            $scheduledTime = Carbon::createFromFormat('Y-m-d H:i:s', 
                $streamRequest->requested_date->format('Y-m-d') . ' ' . $streamRequest->requested_time);
            $scheduledEndTime = $scheduledTime->copy()->addMinutes($streamRequest->duration_minutes);
            
            if (Carbon::now()->gte($scheduledEndTime)) {
                return response()->json([
                    'status' => false, 
                    'message' => 'Stream time has already ended and cannot be started'
                ]);
            }
            
            $timeUntil = $streamRequest->getTimeUntilUserCanJoin();
            $minutes = floor($timeUntil / 60);
            $seconds = $timeUntil % 60;
            
            return response()->json([
                'status' => false, 
                'message' => "Stream can only be started at the scheduled time. Please wait {$minutes}m {$seconds}s"
            ]);
        }

        $streamRequest->startStream();

        // Broadcast the state change
        broadcast(new PrivateStreamStateChanged(
            $streamRequest->fresh(),
            'stream_started',
            ['message' => 'Stream is now live']
        ));

        return response()->json([
            'status' => true,
            'message' => 'Stream started successfully',
            'stream_request' => $streamRequest->fresh()
        ]);
    }

    /**
     * End the stream session.
     */
    public function endStream(Request $request, $id)
    {
        $streamRequest = PrivateStreamRequest::findOrFail($id);
        $user = auth()->user();

        // Check if user has access and is the streamer
        if (!$this->hasAccessToStream($streamRequest, $user) || $streamRequest->streamer_id !== $user->id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        DB::beginTransaction();
        try {
            $actualDuration = null;
            if ($streamRequest->actual_start_time) {
                $actualDuration = Carbon::now()->diffInMinutes($streamRequest->actual_start_time);
            }

            // Determine if both parties participated
            $streamerStarted = !is_null($streamRequest->actual_start_time);
            $userJoined = $streamRequest->user_joined;

            if ($streamerStarted && $userJoined) {
                // Both participated - go to feedback phase
                $streamRequest->update([
                    'stream_ended_at' => Carbon::now(),
                    'actual_duration_minutes' => $actualDuration,
                    'status' => 'awaiting_feedback',
                    'requires_feedback' => true
                ]);

                $message = 'Stream has ended. Please provide feedback.';
            } else {
                // Not both participated - handle refunds and mark as completed
                $this->processRefundBasedOnParticipation($streamRequest, $streamerStarted, $userJoined);
                
                $streamRequest->update([
                    'stream_ended_at' => Carbon::now(),
                    'actual_duration_minutes' => $actualDuration,
                    'status' => 'completed_with_issues',
                    'requires_feedback' => false
                ]);

                if (!$userJoined) {
                    $message = 'Stream ended. User did not join - partial refund processed.';
                } else {
                    $message = 'Stream ended with issues.';
                }
            }

            // Broadcast the state change
            broadcast(new PrivateStreamStateChanged(
                $streamRequest->fresh(),
                'stream_ended',
                ['message' => $message]
            ));

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => $message,
                'stream_request' => $streamRequest->fresh()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error ending stream: ' . $e->getMessage(), [
                'stream_id' => $id,
                'user_id' => $user->id
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Error ending stream'
            ], 500);
        }
    }

    // ============================================================================
    // FEEDBACK & DISPUTE SYSTEM
    // ============================================================================

    /**
     * Submit feedback for a completed stream.
     */
    public function submitFeedback(Request $request, $id)
    {
        $request->validate([
            'rating' => 'nullable|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'user_showed_up' => 'nullable|boolean',
            'streamer_showed_up' => 'nullable|boolean',
            'technical_issues' => 'boolean',
            'technical_issues_description' => 'nullable|string|max:500',
            'inappropriate_behavior' => 'boolean',
            'inappropriate_behavior_description' => 'nullable|string|max:500',
            'overall_experience' => 'nullable|in:excellent,good,average,poor,terrible',
            'would_recommend' => 'nullable|boolean'
        ]);

        $streamRequest = PrivateStreamRequest::findOrFail($id);
        $user = auth()->user();

        // Check if user has access
        if (!$this->hasAccessToStream($streamRequest, $user)) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        // Check if stream is in awaiting feedback state
        if ($streamRequest->status !== 'awaiting_feedback') {
            return response()->json([
                'status' => false, 
                'message' => 'Feedback can only be given for completed streams'
            ]);
        }

        // Determine feedback type
        $feedbackType = ($streamRequest->user_id === $user->id) ? 'user' : 'streamer';

        // Check if feedback already exists
        $existingFeedback = PrivateStreamFeedback::where('private_stream_request_id', $streamRequest->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingFeedback) {
            return response()->json([
                'status' => false, 
                'message' => 'You have already provided feedback for this stream'
            ]);
        }

        DB::beginTransaction();
        try {
            // Create feedback
            $feedback = PrivateStreamFeedback::create([
                'private_stream_request_id' => $streamRequest->id,
                'user_id' => $user->id,
                'feedback_type' => $feedbackType,
                'rating' => $request->rating,
                'comment' => $request->comment,
                'user_showed_up' => $request->user_showed_up,
                'streamer_showed_up' => $request->streamer_showed_up,
                'technical_issues' => $request->technical_issues ?? false,
                'technical_issues_description' => $request->technical_issues_description,
                'inappropriate_behavior' => $request->inappropriate_behavior ?? false,
                'inappropriate_behavior_description' => $request->inappropriate_behavior_description,
                'overall_experience' => $request->overall_experience,
                'would_recommend' => $request->would_recommend,
            ]);

            // Update stream request feedback status
            if ($feedbackType === 'user') {
                $streamRequest->user_feedback_given = true;
            } else {
                $streamRequest->streamer_feedback_given = true;
            }

            // Check if both feedbacks are given
            if ($streamRequest->hasBothFeedbacks()) {
                // Check for conflicts
                if ($streamRequest->hasConflictingFeedback()) {
                    $streamRequest->createDispute();
                } else {
                    // No conflicts, can proceed with automatic payment release
                    if ($streamRequest->canReleasePaymentAutomatically()) {
                        $this->releasePaymentAfterFeedback($streamRequest);
                        $streamRequest->status = 'completed';
                    }
                }
            }

            $streamRequest->save();

            DB::commit();

            // Broadcast feedback submission
            broadcast(new PrivateStreamStateChanged(
                $streamRequest->fresh(),
                'feedback_submitted',
                [
                    'feedback_type' => $feedbackType,
                    'requires_admin_review' => $streamRequest->has_dispute,
                    'message' => $streamRequest->has_dispute 
                        ? 'Feedback submitted - admin review required'
                        : 'Feedback submitted successfully'
                ]
            ));

            return response()->json([
                'status' => true,
                'message' => 'Feedback submitted successfully',
                'requires_admin_review' => $streamRequest->has_dispute,
                'stream_request' => $streamRequest->fresh(),
                'feedback' => $feedback
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error submitting feedback: ' . $e->getMessage(), [
                'stream_id' => $id,
                'user_id' => $user->id
            ]);
            
            return response()->json([
                'status' => false,
                'message' => 'Error submitting feedback'
            ], 500);
        }
    }

    /**
     * Check if user has already submitted feedback for a stream.
     */
    public function checkExistingFeedback(Request $request, $id)
    {
        $streamRequest = PrivateStreamRequest::findOrFail($id);
        $user = auth()->user();

        // Check if user has access
        if (!$this->hasAccessToStream($streamRequest, $user)) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        // Check for existing feedback
        $existingFeedback = PrivateStreamFeedback::where('private_stream_request_id', $streamRequest->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingFeedback) {
            return response()->json([
                'status' => true,
                'has_feedback' => true,
                'feedback' => $existingFeedback
            ]);
        }

        return response()->json([
            'status' => true,
            'has_feedback' => false,
            'feedback' => null
        ]);
    }

    /**
     * Create a dispute for a stream.
     */
    public function createDispute(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:1000'
        ]);

        $streamRequest = PrivateStreamRequest::findOrFail($id);
        $user = auth()->user();

        // Check if user has access
        if (!$this->hasAccessToStream($streamRequest, $user)) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        // Check if dispute can be created
        if ($streamRequest->has_dispute) {
            return response()->json([
                'status' => false, 
                'message' => 'A dispute already exists for this stream'
            ]);
        }

        $streamRequest->createDispute();

        return response()->json([
            'status' => true,
            'message' => 'Dispute created successfully. An admin will review this case.',
            'stream_request' => $streamRequest->fresh()
        ]);
    }

    /**
     * Get feedback details for a stream (admin only).
     */
    public function getFeedbackDetails(Request $request, $id)
    {
        $user = auth()->user();
        
        // Check if user is admin
        if (!($user->hasRole('admin') || $user->is_admin ?? false)) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $streamRequest = PrivateStreamRequest::with(['feedbacks.user', 'user', 'streamer'])->findOrFail($id);

        return response()->json([
            'status' => true,
            'stream_request' => $streamRequest,
            'feedbacks' => $streamRequest->feedbacks,
            'has_conflict' => $streamRequest->hasConflictingFeedback()
        ]);
    }

    /**
     * Release payment after successful feedback (internal method).
     */
    private function releasePaymentAfterFeedback(PrivateStreamRequest $streamRequest)
    {
        try {
            // Convert payment to tokens for streamer
            $this->convertPaymentToTokens($streamRequest);

            // Update payment status
            $streamRequest->update([
                'payment_status' => 'released_to_streamer',
                'released_at' => Carbon::now(),
                'released_by' => null // Automatic release
            ]);

            // Update escrow transaction
            Transaction::where('reference_id', $streamRequest->id)
                ->where('reference_type', PrivateStreamRequest::class)
                ->where('transaction_type', 'private_stream_escrow')
                ->update([
                    'status' => 'released',
                    'updated_at' => Carbon::now()
                ]);

        } catch (\Exception $e) {
            Log::error('Error releasing payment after feedback: ' . $e->getMessage(), [
                'stream_request_id' => $streamRequest->id
            ]);
            throw $e;
        }
    }

    /**
     * Process expired feedback periods (for scheduled job).
     */
    public function processExpiredFeedback()
    {
        $expiredStreams = PrivateStreamRequest::where('status', 'awaiting_feedback')
            ->where('requires_feedback', true)
            ->whereNotNull('stream_ended_at')
            ->where('stream_ended_at', '<', Carbon::now()->subHours(24))
            ->get();

        foreach ($expiredStreams as $stream) {
            try {
                DB::beginTransaction();
                
                // If only one party gave feedback or no feedback at all
                if (!$stream->hasBothFeedbacks()) {
                    // Create automatic dispute for admin review
                    $stream->createDispute();
                } else {
                    // Both gave feedback but within time limit, process normally
                    if ($stream->hasConflictingFeedback()) {
                        $stream->createDispute();
                    } else if ($stream->canReleasePaymentAutomatically()) {
                        $this->releasePaymentAfterFeedback($stream);
                        $stream->status = 'completed';
                        $stream->save();
                    }
                }
                
                DB::commit();
                
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error processing expired feedback: ' . $e->getMessage(), [
                    'stream_request_id' => $stream->id
                ]);
            }
        }
        
        return ['processed' => $expiredStreams->count()];
    }

    // ============================================================================
    // REFUND & FINANCIAL METHODS
    // ============================================================================

    /**
     * Process refunds based on participation.
     */
    private function processRefundBasedOnParticipation(PrivateStreamRequest $stream, $streamerStarted, $userJoined)
    {
        if (!$streamerStarted && !$userJoined) {
            // Neither party showed up - full refund (both rental tokens + payment methods)
            $this->processFullRefund($stream, 'Neither streamer nor user participated in stream');
        } elseif (!$streamerStarted) {
            // Streamer never started but user was ready - full refund
            $this->processFullRefund($stream, 'Streamer did not start stream');
        } elseif (!$userJoined) {
            // User never joined but streamer started - partial refund (only payment methods, not rental tokens)
            $this->processPartialRefund($stream, 'User did not join stream');
        }
        // If both participated, no automatic refund (goes to feedback)
    }

    /**
     * Process full refund (rental tokens + payment methods).
     */
    private function processFullRefund(PrivateStreamRequest $stream, $reason)
    {
        $user = $stream->user;

        // Refund rental tokens
        if ($stream->room_rental_tokens > 0) {
            $user->increment('tokens', $stream->room_rental_tokens);
            
            $this->recordTransaction(
                $user->id,
                'room_rental_refund',
                $stream->id,
                PrivateStreamRequest::class,
                $stream->room_rental_tokens,
                'tokens',
                null,
                'completed',
                $reason,
                ['refund_type' => 'full_refund', 'reason' => $reason]
            );
        }

        // Refund payment methods
        $this->processPaymentRefund($stream, $reason);
    }

    /**
     * Process partial refund (only payment methods, not rental tokens).
     */
    private function processPartialRefund(PrivateStreamRequest $stream, $reason)
    {
        // Only refund payment methods, rental tokens are kept by platform
        $this->processPaymentRefund($stream, $reason);
        
        // Note: Rental tokens are not refunded in this case
        Log::info("Rental tokens retained for stream ID {$stream->id} - user no-show");
    }

    /**
     * Process payment method refunds (Stripe, Mercado Pago).
     */
    private function processPaymentRefund(PrivateStreamRequest $stream, $reason)
    {
        if (!$stream->payment_id) {
            return;
        }

        $user = $stream->user;

        try {
            if ($stream->payment_method === 'stripe') {
                $this->processStripeRefundInternal($stream, $user, $reason);
            } elseif ($stream->payment_method === 'mercado_pago') {
                $this->processMercadoPagoRefundInternal($stream, $user, $reason);
            }
        } catch (\Exception $e) {
            Log::error('Payment refund failed: ' . $e->getMessage(), [
                'stream_id' => $stream->id,
                'payment_method' => $stream->payment_method,
                'payment_id' => $stream->payment_id
            ]);
        }
    }

    /**
     * Process Stripe refund internally.
     */
    private function processStripeRefundInternal(PrivateStreamRequest $stream, User $user, $reason)
    {
        try {
            Stripe::setApiKey(opt('STRIPE_SECRET_KEY'));
            $paymentIntent = PaymentIntent::retrieve($stream->payment_id);
            
            if (in_array($paymentIntent->status, ['requires_payment_method', 'requires_confirmation', 'requires_action', 'processing'])) {
                $paymentIntent->cancel();
            } elseif ($paymentIntent->status === 'succeeded') {
                \Stripe\Refund::create(['payment_intent' => $stream->payment_id]);
            }
            
            $this->recordTransaction(
                $user->id,
                'stripe_refund',
                $stream->id,
                PrivateStreamRequest::class,
                $stream->streamer_fee,
                'stripe',
                $stream->payment_id,
                'completed',
                $reason,
                ['payment_id' => $stream->payment_id, 'reason' => $reason]
            );

        } catch (\Exception $e) {
            Log::error('Stripe refund failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Process Mercado Pago refund internally.
     */
    private function processMercadoPagoRefundInternal(PrivateStreamRequest $stream, User $user, $reason)
    {
        $refundResult = $this->processMercadoPagoRefund($stream->payment_id, $stream->streamer_fee);
        
        if ($refundResult['success']) {
            $this->recordTransaction(
                $user->id,
                'mercado_pago_refund',
                $stream->id,
                PrivateStreamRequest::class,
                $stream->streamer_fee,
                'mercado_pago',
                $stream->payment_id,
                'completed',
                $reason,
                ['refund_id' => $refundResult['refund_id'], 'reason' => $reason]
            );
        } else {
            Log::error('Mercado Pago refund failed: ' . $refundResult['error']);
            throw new \Exception($refundResult['error']);
        }
    }

    // ============================================================================
    // CALENDAR INTEGRATION METHODS
    // ============================================================================

    /**
     * Generate ICS calendar file for the stream session
     */
    public function generateCalendarICS(PrivateStreamRequest $streamRequest)
    {
        // Verify user can access this stream
        if (!$this->hasAccessToStream($streamRequest, auth()->user())) {
            abort(403, 'Unauthorized');
        }

        // Only generate calendar for accepted or pending streams
        if (!in_array($streamRequest->status, ['accepted', 'pending'])) {
            abort(400, 'Calendar not available for this stream status');
        }

        try {
            $startDateTime = new \DateTime($streamRequest->requested_date . ' ' . $streamRequest->requested_time);
            $endDateTime = clone $startDateTime;
            $endDateTime->add(new \DateInterval('PT' . $streamRequest->duration_minutes . 'M'));

            $isStreamer = auth()->id() === $streamRequest->streamer_id;
            $otherParty = $isStreamer ? $streamRequest->user : $streamRequest->streamer;

            $title = 'Private Stream Session with ' . ($otherParty->name ?? 'Unknown');
            $description = $this->generateCalendarDescription($streamRequest, $isStreamer);
            $location = route('private-stream.session', $streamRequest->id);

            // Generate ICS content
            $icsContent = $this->generateICSContent([
                'uid' => 'private-stream-' . $streamRequest->id . '@' . request()->getHost(),
                'title' => $title,
                'description' => $description,
                'location' => $location,
                'start' => $startDateTime,
                'end' => $endDateTime,
                'created' => $streamRequest->created_at,
                'modified' => $streamRequest->updated_at,
            ]);

            $filename = 'private-stream-' . $streamRequest->id . '.ics';

            return response($icsContent)
                ->header('Content-Type', 'text/calendar; charset=utf-8')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->header('Cache-Control', 'no-cache, must-revalidate');

        } catch (\Exception $e) {
            Log::error('Failed to generate calendar ICS', [
                'stream_request_id' => $streamRequest->id,
                'error' => $e->getMessage()
            ]);
            
            abort(500, 'Failed to generate calendar file');
        }
    }

    /**
     * Generate calendar event description
     */
    private function generateCalendarDescription(PrivateStreamRequest $streamRequest, bool $isStreamer): string
    {
        $otherParty = $isStreamer ? $streamRequest->user : $streamRequest->streamer;
        
        $description = "Private streaming session details:\n\n";
        $description .= "• Duration: {$streamRequest->duration_minutes} minutes\n";
        $description .= "• " . ($isStreamer ? "User" : "Streamer") . ": " . ($otherParty->name ?? 'Unknown') . "\n";
        $description .= "• Fee: \${$streamRequest->streamer_fee}\n";
        $description .= "• Status: " . ucfirst(str_replace('_', ' ', $streamRequest->status)) . "\n";
        
        if ($streamRequest->message) {
            $description .= "\nMessage: {$streamRequest->message}\n";
        }
        
        $description .= "\nJoin the session at:\n";
        $description .= route('private-stream.session', $streamRequest->id);
        
        $description .= "\n\nImportant Notes:\n";
        if ($isStreamer) {
            $description .= "• You can start streaming anytime for preparation\n";
            $description .= "• The session will automatically go live at the scheduled time\n";
            $description .= "• Billing starts from the scheduled time, not preparation time\n";
        } else {
            $description .= "• You can only join at the scheduled time\n";
            $description .= "• The streamer may start preparing before the scheduled time\n";
            $description .= "• Please be ready to join at the scheduled time\n";
        }
        
        return $description;
    }

    /**
     * Generate ICS file content
     */
    private function generateICSContent(array $event): string
    {
        $formatDateTime = function($dateTime) {
            if ($dateTime instanceof \DateTime) {
                return $dateTime->format('Ymd\THis\Z');
            }
            return (new \DateTime($dateTime))->format('Ymd\THis\Z');
        };

        $escapeText = function($text) {
            return str_replace(["\n", "\r", ",", ";", "\\"], ["\\n", "", "\\,", "\\;", "\\\\"], $text);
        };

        $icsContent = "BEGIN:VCALENDAR\r\n";
        $icsContent .= "VERSION:2.0\r\n";
        $icsContent .= "PRODID:-//Private Stream Platform//Calendar Event//EN\r\n";
        $icsContent .= "CALSCALE:GREGORIAN\r\n";
        $icsContent .= "METHOD:PUBLISH\r\n";
        $icsContent .= "BEGIN:VEVENT\r\n";
        $icsContent .= "UID:{$event['uid']}\r\n";
        $icsContent .= "DTSTART:" . $formatDateTime($event['start']) . "\r\n";
        $icsContent .= "DTEND:" . $formatDateTime($event['end']) . "\r\n";
        $icsContent .= "DTSTAMP:" . $formatDateTime(new \DateTime()) . "\r\n";
        $icsContent .= "CREATED:" . $formatDateTime($event['created']) . "\r\n";
        $icsContent .= "LAST-MODIFIED:" . $formatDateTime($event['modified']) . "\r\n";
        $icsContent .= "SUMMARY:" . $escapeText($event['title']) . "\r\n";
        $icsContent .= "DESCRIPTION:" . $escapeText($event['description']) . "\r\n";
        $icsContent .= "URL:" . $event['location'] . "\r\n";
        $icsContent .= "LOCATION:" . $event['location'] . "\r\n";
        $icsContent .= "STATUS:CONFIRMED\r\n";
        $icsContent .= "SEQUENCE:0\r\n";
        
        // Add reminder 15 minutes before
        $icsContent .= "BEGIN:VALARM\r\n";
        $icsContent .= "TRIGGER:-PT15M\r\n";
        $icsContent .= "ACTION:DISPLAY\r\n";
        $icsContent .= "DESCRIPTION:Private stream session starts in 15 minutes\r\n";
        $icsContent .= "END:VALARM\r\n";
        
        $icsContent .= "END:VEVENT\r\n";
        $icsContent .= "END:VCALENDAR\r\n";

        return $icsContent;
    }
}
