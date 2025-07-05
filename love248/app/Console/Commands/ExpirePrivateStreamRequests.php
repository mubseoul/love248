<?php

namespace App\Console\Commands;

use App\Events\PrivateStreamStateChanged;
use App\Models\PrivateStreamRequest;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class ExpirePrivateStreamRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'private-stream:expire-requests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire old private stream requests and auto-end streams that exceeded duration';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $expiredCount = 0;
        $autoEndedCount = 0;
        $refundedCount = 0;

        // First, auto-end streams that have exceeded their scheduled duration (based on scheduled time, not actual start time)
        $activeStreams = PrivateStreamRequest::whereIn('status', ['accepted', 'in_progress'])
            ->whereNull('stream_ended_at')
            ->get();

        foreach ($activeStreams as $stream) {
            $scheduledTime = Carbon::createFromFormat('Y-m-d H:i:s', 
                $stream->requested_date->format('Y-m-d') . ' ' . $stream->requested_time);
            $scheduledEndTime = $scheduledTime->copy()->addMinutes($stream->duration_minutes);
            
            // Auto-end if we're past the scheduled end time
            if (Carbon::now()->gt($scheduledEndTime)) {
                $this->autoEndStream($stream);
                $autoEndedCount++;
            }
        }

        // Handle streams that never started (streamer no-show)
        $streamerNoShowStreams = $this->handleStreamerNoShow();
        $refundedCount += $streamerNoShowStreams;

        // Handle streams where user never joined (user no-show)
        $userNoShowStreams = $this->handleUserNoShow();
        $refundedCount += $userNoShowStreams;

        // Expire old pending requests (older than 24 hours)
        $cutoffTime = Carbon::now()->subHours(24);
        
        $expiredRequests = PrivateStreamRequest::where('status', 'pending')
            ->where('created_at', '<', $cutoffTime)
            ->get();

        foreach ($expiredRequests as $request) {
            $request->update(['status' => 'expired']);
            $expiredCount++;
        }

        // Also expire accepted streams that never started and are past their scheduled time + buffer
        $scheduledCutoff = Carbon::now()->subHours(2); // 2 hour buffer after scheduled time
        
        $overdueAccepted = PrivateStreamRequest::where('status', 'accepted')
            ->whereNull('countdown_started_at')
            ->whereNull('actual_start_time')
            ->get()
            ->filter(function ($stream) use ($scheduledCutoff) {
                $scheduledTime = Carbon::createFromFormat('Y-m-d H:i:s', 
                    $stream->requested_date->format('Y-m-d') . ' ' . $stream->requested_time);
                return $scheduledTime->lt($scheduledCutoff);
            });

        foreach ($overdueAccepted as $request) {
            $request->update(['status' => 'expired']);
            $expiredCount++;
        }

        $this->info("Automatically ended {$autoEndedCount} active streams");
        $this->info("Processed {$refundedCount} refund cases");
        $this->info("Expired {$expiredCount} old/overdue requests");
        
        return 0;
    }

    /**
     * Auto-end a stream and determine appropriate status based on participation.
     */
    private function autoEndStream(PrivateStreamRequest $stream)
    {
        try {
            DB::beginTransaction();

            $actualDuration = $stream->actual_start_time 
                ? Carbon::now()->diffInMinutes($stream->actual_start_time) 
                : null;
            
            // Determine if both parties participated
            $streamerStarted = !is_null($stream->actual_start_time);
            $userJoined = $stream->user_joined;

            if ($streamerStarted && $userJoined) {
                // Both participated - go to feedback phase
                $stream->update([
                    'stream_ended_at' => Carbon::now(),
                    'actual_duration_minutes' => $actualDuration,
                    'status' => 'awaiting_feedback',
                    'requires_feedback' => true
                ]);

                $message = 'Stream completed - both parties participated';
            } else {
                // Not both participated - handle refunds and mark as completed
                $this->processRefundBasedOnParticipation($stream, $streamerStarted, $userJoined);
                
                $stream->update([
                    'stream_ended_at' => Carbon::now(),
                    'actual_duration_minutes' => $actualDuration,
                    'status' => 'completed_with_issues',
                    'requires_feedback' => false
                ]);

                if (!$userJoined) {
                    $message = 'Stream ended - user did not join (partial refund processed)';
                } else {
                    $message = 'Stream ended with issues';
                }
            }

            // Broadcast the state change
            broadcast(new PrivateStreamStateChanged(
                $stream->fresh(),
                'stream_ended',
                ['message' => $message]
            ));

            DB::commit();
            $this->info("Auto-ended stream ID {$stream->id} after {$actualDuration} minutes");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error auto-ending stream: ' . $e->getMessage(), [
                'stream_id' => $stream->id
            ]);
        }
    }

    /**
     * Handle streams where streamer never started (full refund scenario).
     */
    private function handleStreamerNoShow()
    {
        $count = 0;
        $cutoffTime = Carbon::now()->subHours(2); // 2 hours after scheduled time

        $streamerNoShows = PrivateStreamRequest::where('status', 'accepted')
            ->whereNull('actual_start_time')
            ->whereNull('countdown_started_at')
            ->get()
            ->filter(function ($stream) use ($cutoffTime) {
                $scheduledTime = Carbon::createFromFormat('Y-m-d H:i:s', 
                    $stream->requested_date->format('Y-m-d') . ' ' . $stream->requested_time);
                return $scheduledTime->lt($cutoffTime);
            });

        foreach ($streamerNoShows as $stream) {
            try {
                DB::beginTransaction();

                // Full refund - both rental tokens and payment
                $this->processFullRefund($stream, 'Streamer no-show - full refund');

                $stream->update([
                    'status' => 'streamer_no_show',
                    'stream_ended_at' => Carbon::now(),
                    'requires_feedback' => false
                ]);

                DB::commit();
                $count++;
                $this->info("Processed streamer no-show for stream ID {$stream->id}");

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error processing streamer no-show: ' . $e->getMessage(), [
                    'stream_id' => $stream->id
                ]);
            }
        }

        return $count;
    }

    /**
     * Handle streams where user never joined (partial refund scenario).
     */
    private function handleUserNoShow()
    {
        $count = 0;
        $cutoffTime = Carbon::now()->subMinutes(30); // 30 minutes after stream should have started

        $userNoShows = PrivateStreamRequest::where('status', 'in_progress')
            ->whereNotNull('actual_start_time')
            ->where('user_joined', false)
            ->where('actual_start_time', '<', $cutoffTime)
            ->get();

        foreach ($userNoShows as $stream) {
            try {
                DB::beginTransaction();

                // Partial refund - only payment methods, not rental tokens
                $this->processPartialRefund($stream, 'User did not join stream');

                $actualDuration = Carbon::now()->diffInMinutes($stream->actual_start_time);
                $stream->update([
                    'status' => 'user_no_show',
                    'stream_ended_at' => Carbon::now(),
                    'actual_duration_minutes' => $actualDuration,
                    'requires_feedback' => false
                ]);

                DB::commit();
                $count++;
                $this->info("Processed user no-show for stream ID {$stream->id}");

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error processing user no-show: ' . $e->getMessage(), [
                    'stream_id' => $stream->id
                ]);
            }
        }

        return $count;
    }

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
        $this->info("Rental tokens retained for stream ID {$stream->id} - user no-show");
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
                $this->processStripeRefund($stream, $user, $reason);
            } elseif ($stream->payment_method === 'mercado_pago') {
                $this->processMercadoPagoRefund($stream, $user, $reason);
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
     * Process Stripe refund.
     */
    private function processStripeRefund(PrivateStreamRequest $stream, User $user, $reason)
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
        }
    }

    /**
     * Process Mercado Pago refund.
     */
    private function processMercadoPagoRefund(PrivateStreamRequest $stream, User $user, $reason)
    {
        try {
            $apiToken = opt('MERCADO_SECRET_KEY');
            
            if (!$apiToken) {
                throw new \Exception('Mercado Pago API token not configured');
            }

            $refundData = ['amount' => floatval($stream->streamer_fee)];
            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => "https://api.mercadopago.com/v1/payments/{$stream->payment_id}/refunds",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($refundData),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $apiToken
                ],
            ]);

            $response = curl_exec($curl);
            $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            if ($httpStatus < 400) {
                $refundResponse = json_decode($response, true);
                if (isset($refundResponse['id'])) {
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
                        ['refund_id' => $refundResponse['id'], 'reason' => $reason]
                    );
                }
            }

        } catch (\Exception $e) {
            Log::error('Mercado Pago refund failed: ' . $e->getMessage());
        }
    }

    /**
     * Record transaction.
     */
    private function recordTransaction($userId, $type, $referenceId, $referenceType, $amount, $paymentMethod, $paymentId, $status, $description, $metadata = [])
    {
        Transaction::create([
            'user_id' => $userId,
            'transaction_type' => $type,
            'reference_id' => $referenceId,
            'reference_type' => $referenceType,
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'payment_id' => $paymentId,
            'status' => $status,
            'description' => $description,
            'metadata' => json_encode($metadata),
        ]);
    }
} 