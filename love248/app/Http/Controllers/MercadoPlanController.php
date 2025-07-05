<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlanSell;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\User;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class MercadoPlanController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    // Function for recording transactions
    private function recordTransaction($userId, $type, $referenceId, $referenceType, $amount, $paymentMethod, $paymentId, $status, $description, $metadata = [])
    {
        return Transaction::create([
            'user_id' => $userId,
            'transaction_type' => $type,
            'reference_id' => $referenceId,
            'reference_type' => $referenceType,
            'amount' => $amount,
            'currency' => opt('payment-settings.currency_code'),
            'payment_method' => $paymentMethod,
            'payment_id' => $paymentId,
            'status' => $status,
            'description' => $description,
            'metadata' => !empty($metadata) ? json_encode($metadata) : null,
        ]);
    }

    // Function for recording failed transactions
    private function recordFailedTransaction($transaction, $paymentId, $errorMessage)
    {
        if (!$transaction) {
            return null;
        }

        // Update existing transaction
        $transaction->status = 'failed';
        $transaction->payment_id = $paymentId;

        // Add error details to metadata
        $metadata = $transaction->metadata ? json_decode($transaction->metadata, true) : [];
        $metadata['error_message'] = $errorMessage;
        $metadata['failed_at'] = Carbon::now()->toDateTimeString();

        $transaction->metadata = json_encode($metadata);
        $transaction->save();

        return $transaction;
    }

    // Function to check if user has an active subscription
    private function getActiveSubscription($userId)
    {
        return SubscriptionPlanSell::where('user_id', $userId)
            ->where('status', 'active')
            ->where('expire_date', '>=', Carbon::now()->toDateString())
            ->orderBy('created_at', 'desc')
            ->first();
    }

    // Calculate remaining value of existing subscription
    private function calculateRemainingValue($subscription)
    {
        if (!$subscription) {
            return 0;
        }

        // Get total days of subscription
        $startDate = Carbon::parse($subscription->created_at);
        $endDate = Carbon::parse($subscription->expire_date);
        $totalDays = $startDate->diffInDays($endDate);

        if ($totalDays <= 0) {
            return 0;
        }

        // Calculate days remaining
        $today = Carbon::now();
        $daysRemaining = $today->diffInDays($endDate);

        // If expired, no value remains
        if ($daysRemaining <= 0) {
            return 0;
        }

        // Calculate prorated value based on days remaining
        $pricePerDay = $subscription->price / $totalDays;
        return round($pricePerDay * $daysRemaining, 2);
    }

    // Handle subscription upgrade/downgrade
    private function handleSubscriptionChange(SubscriptionPlan $newPlan, User $user)
    {
        // Check if user has active subscription
        $activeSubscription = $this->getActiveSubscription($user->id);

        // Result object to return
        $result = [
            'has_active_subscription' => false,
            'is_same_plan' => false,
            'is_upgrade' => false,
            'remaining_value' => 0,
            'adjusted_price' => $newPlan->subscription_price,
            'original_price' => $newPlan->subscription_price,
            'active_subscription' => null
        ];

        if (!$activeSubscription) {
            return $result;
        }

        // User has active subscription
        $result['has_active_subscription'] = true;
        $result['active_subscription'] = $activeSubscription;

        // Calculate remaining value of current subscription
        $remainingValue = $this->calculateRemainingValue($activeSubscription);
        $result['remaining_value'] = $remainingValue;

        // Check if it's the same plan
        if ($activeSubscription->subscription_plan === $newPlan->subscription_name) {
            $result['is_same_plan'] = true;
            // For same plan, we'll just extend the subscription
            // No need to adjust price since it's just a renewal
            return $result;
        }

        // Determine if it's an upgrade or downgrade based on price
        $result['is_upgrade'] = $newPlan->subscription_price > $activeSubscription->price;

        // Adjust price based on remaining value
        $adjustedPrice = max(0, $newPlan->subscription_price - $remainingValue);
        $result['adjusted_price'] = $adjustedPrice;

        return $result;
    }

    // Update subscription status when upgrading/downgrading
    private function updatePreviousSubscription($subscription, $newSubscriptionId)
    {
        if (!$subscription) {
            return;
        }

        // Update status
        $subscription->status = 'upgraded';

        // Add metadata about upgrade
        $metadata = [
            'upgraded_at' => Carbon::now()->toDateTimeString(),
            'upgraded_to' => $newSubscriptionId,
        ];

        // Save metadata to subscription
        $subscription->upgrade_data = json_encode($metadata);
        $subscription->save();

        // Update any related transactions
        Transaction::where('reference_id', $subscription->id)
            ->where('reference_type', SubscriptionPlanSell::class)
            ->where('transaction_type', 'subscription')
            ->get()
            ->each(function ($transaction) use ($newSubscriptionId) {
                $metadata = $transaction->metadata ? json_decode($transaction->metadata, true) : [];
                $metadata['upgraded_to'] = $newSubscriptionId;
                $transaction->metadata = json_encode($metadata);
                $transaction->status = 'upgraded';
                $transaction->save();
            });
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
     * Show the subscription purchase form
     */
    public function purchase(SubscriptionPlan $plan, Request $request)
    {
        return Inertia::render('Subscriptionplan/MercadoForm', [
            'tokenPack' => $plan
        ]);
    }

    /**
     * Process a subscription payment
     */
    public function processPayment(Request $request)
    {
        $request->validate([
            'token' => 'sometimes',
            'payment_method_id' => 'required',
            'transaction_amount' => 'required|numeric',
            'payer' => 'required',
            'email' => 'required|email',
            'tokPack' => 'required|exists:subscription_plans,id'
        ]);

        // Use database transaction to ensure data consistency
        DB::beginTransaction();

        try {
            $plan = SubscriptionPlan::find($request->tokPack);
            $user = $request->user();

            // Handle subscription upgrades/downgrades
            $subscriptionChange = $this->handleSubscriptionChange($plan, $user);

            // Set expiration date based on plan days
            $currentDate = Carbon::now();
            $expireDate = $currentDate->addDays($plan->days)->toDateString();

            // If same plan, add days to existing expiration
            if ($subscriptionChange['has_active_subscription'] && $subscriptionChange['is_same_plan']) {
                $expireDate = Carbon::parse($subscriptionChange['active_subscription']->expire_date)
                    ->addDays($plan->days)
                    ->toDateString();
            }

            // Create subscription record
            $sale = SubscriptionPlanSell::create([
                'user_id' => $user->id,
                'subscription_plan' => $plan->subscription_name,
                'price' => $subscriptionChange['adjusted_price'],
                'expire_date' => $expireDate,
                'status' => 'pending',
                'gateway' => 'Mercado',
            ]);

            // Store metadata 
            $metadataArray = [
                'plan_id' => $plan->id,
                'plan_name' => $plan->subscription_name,
                'plan_days' => $plan->days,
                'expire_date' => $expireDate,
                'subscription_id' => $sale->id,
                'original_price' => $plan->subscription_price
            ];

            // If upgrading/downgrading, add related info
            if ($subscriptionChange['has_active_subscription'] && !$subscriptionChange['is_same_plan']) {
                $metadataArray['is_upgrade'] = $subscriptionChange['is_upgrade'];
                $metadataArray['previous_subscription_id'] = $subscriptionChange['active_subscription']->id;
                $metadataArray['remaining_value'] = $subscriptionChange['remaining_value'];
            }

            // Record pending transaction
            $transaction = $this->recordTransaction(
                $user->id,
                'subscription',
                $sale->id,
                SubscriptionPlanSell::class,
                $subscriptionChange['adjusted_price'],
                'Mercado',
                null, // payment ID not yet available
                'pending',
                'Subscription purchase: ' . $plan->subscription_name,
                $metadataArray
            );

            // Prepare the Mercado Pago API request
            $requestBody = [
                "additional_info" => [
                    "items" => [
                        [
                            "title" => $plan->subscription_name,
                            "description" => $plan->details ?? 'Subscription plan',
                            "quantity" => 1,
                            "unit_price" => $subscriptionChange['adjusted_price']
                        ]
                    ]
                ],
                "capture" => true,
                "description" => $plan->details ?? 'Subscription plan',
                "installments" => 1,
                "payer" => [
                    "entity_type" => "individual",
                    "type" => "customer",
                    "email" => $request->email
                ],
                "payment_method_id" => $request->payment_method_id,
                "transaction_amount" => floatval($request->transaction_amount),
                "test_mode" => true
            ];

            // Test mode handling
            $isTestMode = env('APP_ENV') !== 'production';
            if ($isTestMode) {
                $requestBody['test_mode'] = true;
            }

            // Add token if payment method requires it
            if ($request->payment_method_id !== 'pix' && $request->has('token')) {
                $requestBody['token'] = $request->token;
            }

            // Get appropriate API key based on environment
            $apiKey = opt('MERCADO_SECRET_KEY');        // Production - use app access token

            if (empty($apiKey)) {
                return response()->json(['error' => 'API key not configured for ' . (env('APP_ENV') !== 'production' ? 'test' : 'production') . ' environment'], 500);
            }

            // Make API request
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://api.mercadopago.com/v1/payments',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($requestBody),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'X-Idempotency-Key: ' . $this->generateIdempotencyKey(),
                    'Authorization: Bearer ' . $apiKey
                ],
            ]);

            $response = curl_exec($curl);
            $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curlError = curl_error($curl);

            curl_close($curl);

            // Check for connection errors
            if ($curlError) {
                DB::rollBack();
                Log::error('Mercado API Connection Error: ' . $curlError);
                return response()->json(['error' => 'Connection error: ' . $curlError], 500);
            }

            $responseData = json_decode($response);

            // Handle API errors
            if ($httpStatus >= 400 || isset($responseData->error)) {
                DB::rollBack();
                Log::error('Mercado API Error: ' . ($responseData->message ?? $responseData->error ?? 'Unknown error'));
                return response()->json([
                    'error' => $responseData->message ?? $responseData->error ?? 'API error',
                    'details' => $responseData
                ], $httpStatus >= 400 ? $httpStatus : 500);
            }

            // Check payment status
            if ($responseData->status === 'approved') {
                // Update transaction with payment ID
                $transaction->payment_id = $responseData->id;
                $transaction->status = 'completed';
                $transaction->save();

                // If this is an upgrade/downgrade, update previous subscription
                if ($subscriptionChange['has_active_subscription'] && !$subscriptionChange['is_same_plan']) {
                    $this->updatePreviousSubscription(
                        $subscriptionChange['active_subscription'],
                        $sale->id
                    );
                }

                // Activate the subscription
                $sale->status = 'active';
                $sale->save();

                DB::commit();

                // Return success response
                return response()->json([
                    'success' => true,
                    'message' => 'Payment processed successfully',
                    'payment' => $responseData,
                    'redirect' => '/profile',
                    'type' => 'payment'
                ]);
            } elseif ($responseData->status === 'pending' || $responseData->status === 'in_process') {
                // Handle pending payment (like PIX)
                // Update transaction with payment ID
                $transaction->payment_id = $responseData->id;
                $transaction->save();

                // Update subscription status
                $sale->status = 'pending';
                $sale->save();

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Payment is being processed',
                    'payment' => $responseData,
                    'status' => 'pending',
                    'redirect' => '/my-plan'
                ]);
            } else {
                // Payment failed or rejected
                DB::rollBack();
                $this->recordFailedTransaction(
                    $transaction,
                    $responseData->id ?? null,
                    'Payment failed with status: ' . $responseData->status
                );

                return response()->json([
                    'success' => false,
                    'message' => 'Payment was not approved: ' . $responseData->status_detail,
                    'payment' => $responseData
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Subscription Payment Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Create a test user for Mercado Pago
     */
    public function createTestUser(Request $request)
    {
        try {
            // For test user creation, we need to use the test access token
            $apiKey = opt('MERCADO_SECRET_KEY');
            if (empty($apiKey)) {
                return response()->json(['error' => 'Test API key not configured. Please set MERCADO_SECRET_KEY in your environment.'], 500);
            }

            // Prepare the test user creation request
            $requestBody = [
                "site_id" => "MLB" // Brazil
            ];

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://api.mercadopago.com/users/test_user',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($requestBody),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $apiKey
                ],
            ]);

            $response = curl_exec($curl);
            $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curlError = curl_error($curl);

            curl_close($curl);

            if ($curlError) {
                Log::error('Mercado API Connection Error: ' . $curlError);
                return response()->json(['error' => 'Connection error: ' . $curlError], 500);
            }

            $responseData = json_decode($response);

            if ($httpStatus >= 400 || isset($responseData->error)) {
                Log::error('Mercado API Error: ' . ($responseData->message ?? $responseData->error ?? 'Unknown error'));
                return response()->json([
                    'error' => $responseData->message ?? $responseData->error ?? 'API error',
                    'details' => $responseData
                ], $httpStatus >= 400 ? $httpStatus : 500);
            }

            return response()->json($responseData);
        } catch (\Exception $e) {
            Log::error('Test User Creation Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle recurring subscription creation via preapproval
     */
    public function createRecurringSubscription(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'tokPack' => 'required|exists:subscription_plans,id',
            'is_recurring' => 'sometimes|boolean'
        ]);

        // Log the input for debugging
        Log::info('Received subscription request', [
            'plan_id' => $request->tokPack,
            'email' => $request->email,
            'is_recurring' => $request->is_recurring ?? true
        ]);

        try {
            $plan = SubscriptionPlan::find($request->tokPack);
            if (!$plan) {
                Log::error('Plan not found', ['tokPack' => $request->tokPack]);
                return response()->json(['error' => 'Plan not found'], 404);
            }

            // Get user info to use as the collector
            $user = $request->user();

            // Get appropriate API key based on environment
            $apiKey = opt('MERCADO_SECRET_KEY');        // Production - use app access token

            if (empty($apiKey)) {
                Log::error('Mercado Pago API key not set for ' . (env('APP_ENV') !== 'production' ? 'test' : 'production') . ' environment');
                return response()->json(['error' => 'API key not configured for ' . (env('APP_ENV') !== 'production' ? 'test' : 'production') . ' environment'], 500);
            }

            // Prepare the Mercado Pago preapproval request
            $requestBody = [
                "reason" => $plan->subscription_name,
                "external_reference" => $plan->subscription_name,
                "payer_email" => $request->email,
                "notification_url" => route('mercado.webhook'),
                "auto_recurring" => [
                    "frequency" => 1,
                    "frequency_type" => "months",
                    "transaction_amount" => floatval($plan->subscription_price),
                    "currency_id" => "BRL" // Fixed currency code for Mercado Pago Brazil
                ],
                "back_url" => route('mercado.subscriptionSuccess'),
                "status" => "pending"
            ];

            // Test mode handling
            $isTestMode = env('APP_ENV') !== 'production';
            if ($isTestMode) {
                $requestBody['test_mode'] = true;
            }

            Log::info('Making Mercado Pago API request', [
                'url' => 'https://api.mercadopago.com/preapproval',
                'request_body' => $requestBody
            ]);

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://api.mercadopago.com/preapproval',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($requestBody),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'X-Idempotency-Key: ' . $this->generateIdempotencyKey(),
                    'Authorization: Bearer ' . $apiKey
                ],
                CURLOPT_VERBOSE => true,
            ]);

            $response = curl_exec($curl);
            $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curlError = curl_error($curl);
            curl_close($curl);

            Log::info('Mercado Pago API response', [
                'status' => $httpStatus,
                'response' => $response,
                'curl_error' => $curlError
            ]);

            if ($curlError) {
                Log::error('cURL error', [
                    'message' => $curlError
                ]);
                return response()->json(['error' => 'Connection error: ' . $curlError], 500);
            }

            $data = json_decode($response);

            if ($httpStatus >= 400 || isset($data->error)) {
                Log::error('Mercado Pago API error', [
                    'status' => $httpStatus,
                    'response' => $data,
                    'error' => $data->message ?? $data->error ?? 'Unknown error'
                ]);

                // Handle the common test user error more clearly
                if (isset($data->error) && $data->error == 'Both payer and collector must be real or test users') {
                    return response()->json([
                        'error' => 'Both payer and collector must be test users in test mode. Please use the Create Test User button to create a valid test user.'
                    ], 400);
                }

                return response()->json([
                    'error' => $data->message ?? $data->error ?? 'API error',
                    'details' => $data
                ], $httpStatus >= 400 ? $httpStatus : 500);
            }

            if (isset($data->id) && isset($data->init_point)) {
                Log::info('Successfully created subscription', [
                    'id' => $data->id,
                    'init_point' => $data->init_point
                ]);

                // Create a pending subscription record in our database
                try {
                    DB::beginTransaction();

                    // Set expiration date based on plan days
                    $currentDate = Carbon::now();
                    $expireDate = $currentDate->addDays($plan->days)->toDateString();

                    // Create subscription record with pending status
                    $sale = SubscriptionPlanSell::create([
                        'user_id' => $user->id,
                        'subscription_plan' => $plan->subscription_name,
                        'price' => $plan->subscription_price,
                        'expire_date' => $expireDate,
                        'status' => 'pending',
                        'gateway' => 'Mercado',
                    ]);

                    // Store metadata for reference
                    $metadataArray = [
                        'plan_id' => $plan->id,
                        'plan_name' => $plan->subscription_name,
                        'plan_days' => $plan->days,
                        'expire_date' => $expireDate,
                        'subscription_id' => $sale->id,
                        'preapproval_id' => $data->id,
                        'init_point' => $data->init_point,
                    ];

                    // Record pending transaction
                    $transaction = $this->recordTransaction(
                        $user->id,
                        'subscription',
                        $sale->id,
                        SubscriptionPlanSell::class,
                        $plan->subscription_price,
                        'Mercado',
                        $data->id,
                        'pending',
                        'Subscription purchase: ' . $plan->subscription_name,
                        $metadataArray
                    );

                    DB::commit();

                    // Return the init_point URL for redirection
                    return response()->json($data->init_point);
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Failed to create pending subscription record: ' . $e->getMessage());
                    // Still return the init_point URL to not block the payment flow
                    return response()->json($data->init_point);
                }
            } else {
                Log::error('Unexpected API response format', [
                    'response' => $data
                ]);
                return response()->json(['error' => 'Invalid API response format'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Subscription Creation Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle webhook callbacks from Mercado Pago
     */
    public function handleWebhook(Request $request)
    {
        Log::info('Received Mercado Pago webhook', $request->all());

        try {
            $data = $request->all();

            // Validate webhook data
            if (!isset($data['data']) || !isset($data['type'])) {
                Log::error('Invalid webhook data', $data);
                return response()->json(['error' => 'Invalid webhook data'], 400);
            }

            // Process based on notification type
            if ($data['type'] === 'payment') {
                $paymentId = $data['data']['id'];

                // Verify payment with Mercado Pago API
                $apiKey = opt('MERCADO_SECRET_KEY');        // Production - use app access token

                if (empty($apiKey)) {
                    return response()->json(['error' => 'API key not configured'], 500);
                }

                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => "https://api.mercadopago.com/v1/payments/$paymentId",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => [
                        'Authorization: Bearer ' . $apiKey
                    ],
                ]);

                $response = curl_exec($curl);
                curl_close($curl);

                if (!$response) {
                    Log::error('Failed to retrieve payment information', ['payment_id' => $paymentId]);
                    return response()->json(['error' => 'Failed to retrieve payment information'], 500);
                }

                $paymentData = json_decode($response, true);

                // Find the transaction by payment ID
                $transaction = Transaction::where('payment_id', $paymentId)->first();

                if (!$transaction) {
                    Log::error('Transaction not found for payment', ['payment_id' => $paymentId]);
                    return response()->json(['error' => 'Transaction not found'], 404);
                }

                // Update the transaction status based on payment status
                if ($paymentData['status'] === 'approved') {
                    $transaction->status = 'completed';
                    $transaction->save();

                    // Update the subscription if it exists
                    $subscription = SubscriptionPlanSell::find($transaction->reference_id);
                    if ($subscription && $subscription->status !== 'active') {
                        $subscription->status = 'active';
                        $subscription->save();
                    }
                } elseif (in_array($paymentData['status'], ['rejected', 'cancelled', 'refunded'])) {
                    $transaction->status = 'failed';
                    $transaction->save();

                    // Update subscription status if needed
                    $subscription = SubscriptionPlanSell::find($transaction->reference_id);
                    if ($subscription && $subscription->status === 'pending') {
                        $subscription->status = 'cancelled';
                        $subscription->save();
                    }
                }

                return response()->json(['success' => true]);
            } elseif ($data['type'] === 'preapproval') {
                // Handle subscription preapproval notifications
                $preapprovalId = $data['data']['id'];

                // Verify preapproval with Mercado Pago API
                $apiKey = opt('MERCADO_SECRET_KEY');        // Production - use app access token

                if (empty($apiKey)) {
                    return response()->json(['error' => 'API key not configured'], 500);
                }

                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => "https://api.mercadopago.com/preapproval/$preapprovalId",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => [
                        'Authorization: Bearer ' . $apiKey
                    ],
                ]);

                $response = curl_exec($curl);
                curl_close($curl);

                if (!$response) {
                    Log::error('Failed to retrieve preapproval information', ['preapproval_id' => $preapprovalId]);
                    return response()->json(['error' => 'Failed to retrieve preapproval information'], 500);
                }

                $preapprovalData = json_decode($response, true);

                // Handle subscription based on preapproval status
                if ($preapprovalData['status'] === 'authorized') {
                    // Find the plan 
                    $planName = $preapprovalData['reason'];
                    $plan = SubscriptionPlan::where('subscription_name', $planName)->first();

                    if (!$plan) {
                        Log::error('Plan not found for preapproval', ['plan_name' => $planName]);
                        return response()->json(['error' => 'Plan not found'], 404);
                    }

                    // Find the user by payer email
                    $user = User::where('email', $preapprovalData['payer_email'])->first();

                    if (!$user) {
                        Log::error('User not found for preapproval', ['email' => $preapprovalData['payer_email']]);
                        return response()->json(['error' => 'User not found'], 404);
                    }

                    DB::beginTransaction();

                    try {
                        // Create a new subscription
                        $currentDate = Carbon::now();
                        $expireDate = $currentDate->addDays($plan->days)->toDateString();

                        $sale = SubscriptionPlanSell::create([
                            'user_id' => $user->id,
                            'subscription_plan' => $plan->subscription_name,
                            'price' => $preapprovalData['auto_recurring']['transaction_amount'],
                            'expire_date' => $expireDate,
                            'status' => 'active',
                            'gateway' => 'Mercado',
                        ]);

                        // Record transaction
                        $this->recordTransaction(
                            $user->id,
                            'subscription',
                            $sale->id,
                            SubscriptionPlanSell::class,
                            $preapprovalData['auto_recurring']['transaction_amount'],
                            'Mercado',
                            $preapprovalData['id'],
                            'completed',
                            'Subscription purchase: ' . $plan->subscription_name,
                            [
                                'plan_id' => $plan->id,
                                'plan_name' => $plan->subscription_name,
                                'plan_days' => $plan->days,
                                'expire_date' => $expireDate,
                                'subscription_id' => $sale->id,
                                'preapproval_id' => $preapprovalData['id'],
                            ]
                        );

                        DB::commit();
                    } catch (\Exception $e) {
                        DB::rollBack();
                        Log::error('Error processing preapproval: ' . $e->getMessage());
                        return response()->json(['error' => 'Error processing preapproval'], 500);
                    }
                }

                return response()->json(['success' => true]);
            }

            return response()->json(['message' => 'Webhook received but not processed']);
        } catch (\Exception $e) {
            Log::error('Webhook Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display successful purchase page
     */
    public function purchaseSuccess(Request $request)
    {
        // Log incoming parameters
        Log::info('Received success callback from Mercado Pago', $request->all());

        $verified = false;

        // Check if we have a preapproval_id in the request (for recurring subscriptions)
        if ($request->has('preapproval_id')) {
            $verified = $this->verifyAndUpdatePreapproval($request->preapproval_id);
        }
        // Check for payment_id in the request (for one-time payments)
        else if ($request->has('payment_id')) {
            $verified = $this->verifyAndUpdatePayment($request->payment_id);
        }

        // If we processed a direct payment/preapproval from the URL, redirect to myPlan
        if ($request->has('preapproval_id') || $request->has('payment_id')) {
            $message = $verified
                ? 'Your subscription has been activated successfully.'
                : 'Your payment is being processed. It may take a few moments to activate your subscription.';

            return redirect()->route('myPlan')->with('message', __($message));
        }

        // Check for any pending subscriptions
        $pendingSale = SubscriptionPlanSell::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->where('gateway', 'Mercado')
            ->orderBy('created_at', 'desc')
            ->first();

        // If we have a pending subscription, verify it with Mercado Pago
        if ($pendingSale) {
            // Find the associated transaction
            $transaction = Transaction::where('reference_id', $pendingSale->id)
                ->where('reference_type', SubscriptionPlanSell::class)
                ->where('transaction_type', 'subscription')
                ->first();

            if ($transaction && $transaction->payment_id) {
                // Check if it's a preapproval ID (starts with a letter)
                if (preg_match('/^[a-zA-Z]/', $transaction->payment_id)) {
                    // It's a preapproval ID
                    $this->verifyAndUpdatePreapproval($transaction->payment_id);
                } else {
                    // It's a regular payment ID
                    $this->verifyAndUpdatePayment($transaction->payment_id);
                }
            }
        }

        // Get the active subscription
        $sale = SubscriptionPlanSell::where('user_id', Auth::id())
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->first();

        return Inertia::render('Subscriptionplan/Success', compact('sale'));
    }

    /**
     * Verify preapproval status with Mercado Pago and update subscription
     */
    public function verifyAndUpdatePreapproval($preapprovalId)
    {
        Log::info('Verifying preapproval status', ['preapproval_id' => $preapprovalId]);

        try {
            // Get appropriate API key based on environment
            $apiKey = opt('MERCADO_SECRET_KEY');        // Production - use app access token

            if (empty($apiKey)) {
                Log::error('API key not configured for verification');
                return false;
            }

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => "https://api.mercadopago.com/preapproval/$preapprovalId",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $apiKey
                ],
            ]);

            $response = curl_exec($curl);
            curl_close($curl);

            if (!$response) {
                Log::error('Failed to retrieve preapproval information', ['preapproval_id' => $preapprovalId]);
                return false;
            }

            $preapprovalData = json_decode($response, true);
            Log::info('Preapproval data', $preapprovalData);

            // Find the transaction by payment ID (preapproval_id in this case)
            $transaction = Transaction::where('payment_id', $preapprovalId)->first();

            if (!$transaction) {
                Log::error('Transaction not found for preapproval', ['preapproval_id' => $preapprovalId]);
                return false;
            }

            // Process based on status
            if ($preapprovalData['status'] === 'authorized') {
                DB::beginTransaction();

                try {
                    // Update transaction status
                    $transaction->status = 'completed';
                    $transaction->save();

                    // Update the subscription
                    $subscription = SubscriptionPlanSell::find($transaction->reference_id);
                    if ($subscription && $subscription->status !== 'active') {
                        // If this is an upgrade/downgrade, update previous subscription
                        if ($transaction->metadata) {
                            $metadata = json_decode($transaction->metadata, true);
                            if (isset($metadata['previous_subscription_id'])) {
                                $previousSubscription = SubscriptionPlanSell::find($metadata['previous_subscription_id']);
                                if ($previousSubscription) {
                                    $this->updatePreviousSubscription(
                                        $previousSubscription,
                                        $subscription->id
                                    );
                                }
                            }
                        }

                        $subscription->status = 'active';
                        $subscription->save();

                        DB::commit();
                        Log::info('Successfully activated subscription via preapproval', [
                            'subscription_id' => $subscription->id,
                            'preapproval_id' => $preapprovalId
                        ]);
                        return true;
                    }

                    DB::commit();
                    return true;
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Failed to update subscription: ' . $e->getMessage());
                    return false;
                }
            } elseif (in_array($preapprovalData['status'], ['cancelled', 'paused'])) {
                // Update transaction status
                $transaction->status = 'failed';
                $transaction->save();

                // Update subscription status
                $subscription = SubscriptionPlanSell::find($transaction->reference_id);
                if ($subscription && $subscription->status === 'pending') {
                    $subscription->status = 'cancelled';
                    $subscription->save();
                }

                Log::info('Preapproval was not authorized', [
                    'preapproval_id' => $preapprovalId,
                    'status' => $preapprovalData['status']
                ]);
                return false;
            } else {
                Log::info('Preapproval still pending', [
                    'preapproval_id' => $preapprovalId,
                    'status' => $preapprovalData['status']
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Preapproval verification error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify payment status with Mercado Pago and update subscription
     */
    public function verifyAndUpdatePayment($paymentId)
    {
        Log::info('Manually verifying payment', ['payment_id' => $paymentId]);

        try {
            // Get appropriate API key based on environment
            $apiKey = opt('MERCADO_SECRET_KEY');        // Production - use app access token

            if (empty($apiKey)) {
                Log::error('API key not configured for verification');
                return false;
            }

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => "https://api.mercadopago.com/v1/payments/$paymentId",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $apiKey
                ],
            ]);

            $response = curl_exec($curl);
            curl_close($curl);

            if (!$response) {
                Log::error('Failed to retrieve payment information', ['payment_id' => $paymentId]);
                return false;
            }

            $paymentData = json_decode($response, true);

            // Find the transaction by payment ID
            $transaction = Transaction::where('payment_id', $paymentId)->first();

            if (!$transaction) {
                Log::error('Transaction not found for payment verification', ['payment_id' => $paymentId]);
                return false;
            }

            // Process payment based on status
            if ($paymentData['status'] === 'approved') {
                DB::beginTransaction();

                try {
                    // Update transaction status
                    $transaction->status = 'completed';
                    $transaction->save();

                    // Update the subscription
                    $subscription = SubscriptionPlanSell::find($transaction->reference_id);
                    if ($subscription && $subscription->status !== 'active') {
                        // If this is an upgrade/downgrade, update previous subscription
                        if ($transaction->metadata) {
                            $metadata = json_decode($transaction->metadata, true);
                            if (isset($metadata['previous_subscription_id'])) {
                                $previousSubscription = SubscriptionPlanSell::find($metadata['previous_subscription_id']);
                                if ($previousSubscription) {
                                    $this->updatePreviousSubscription(
                                        $previousSubscription,
                                        $subscription->id
                                    );
                                }
                            }
                        }

                        $subscription->status = 'active';
                        $subscription->save();

                        DB::commit();
                        Log::info('Successfully activated subscription', [
                            'subscription_id' => $subscription->id,
                            'payment_id' => $paymentId
                        ]);
                        return true;
                    }

                    DB::commit();
                    return true;
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Failed to update subscription: ' . $e->getMessage());
                    return false;
                }
            } elseif (in_array($paymentData['status'], ['rejected', 'cancelled', 'refunded'])) {
                // Update transaction status
                $transaction->status = 'failed';
                $transaction->save();

                // Update subscription status
                $subscription = SubscriptionPlanSell::find($transaction->reference_id);
                if ($subscription && $subscription->status === 'pending') {
                    $subscription->status = 'cancelled';
                    $subscription->save();
                }

                Log::info('Payment was not approved', [
                    'payment_id' => $paymentId,
                    'status' => $paymentData['status']
                ]);
                return false;
            } else {
                Log::info('Payment still pending', [
                    'payment_id' => $paymentId,
                    'status' => $paymentData['status']
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Payment verification error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Route to manually verify a payment by ID
     */
    public function manualVerification(Request $request, $paymentId)
    {
        // Only allow admins to manually verify payments
        if (!Auth::user()->isAdmin()) {
            return redirect()->back()->with('error', 'Unauthorized access');
        }

        $success = $this->verifyAndUpdatePayment($paymentId);

        if ($success) {
            return redirect()->back()->with('success', 'Payment verified and subscription activated')
                ->with('verified', true);
        } else {
            return redirect()->back()->with('error', 'Unable to verify payment or payment not approved')
                ->with('verified', false);
        }
    }

    /**
     * Route to manually verify a preapproval by ID
     */
    public function manualPreapprovalVerification(Request $request, $preapprovalId)
    {
        $success = $this->verifyAndUpdatePreapproval($preapprovalId);

        $message = $success
            ? 'Your subscription has been activated successfully.'
            : 'Unable to verify or activate subscription. Please try again later or contact support.';

        return redirect()->route('myPlan', ['verified' => $success ? 'true' : 'false'])
            ->with($success ? 'message' : 'error', __($message));
    }

    /**
     * Cancel a subscription with Mercado Pago
     */
    public function cancelSubscription(Request $request)
    {
        $subscription = SubscriptionPlanSell::where('user_id', Auth::id())
            ->where('status', 'active')
            ->where('expire_date', '>=', now())
            ->where('gateway', 'Mercado')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$subscription) {
            return redirect()->route('myPlan')->with('error', __('No active subscription found'));
        }

        // Get the transaction record to find the payment/preapproval ID
        $transaction = Transaction::where('reference_id', $subscription->id)
            ->where('reference_type', SubscriptionPlanSell::class)
            ->where('transaction_type', 'subscription')
            ->first();

        if (!$transaction || !$transaction->payment_id) {
            // If no transaction or payment ID found, we'll just update the local status
            $subscription->status = 'cancelled';
            $subscription->save();

            // Record the cancellation in transactions
            $this->recordTransaction(
                Auth::id(),
                'subscription_cancellation',
                $subscription->id,
                SubscriptionPlanSell::class,
                0,
                'Mercado',
                null,
                'completed',
                'Subscription cancelled: ' . $subscription->subscription_plan,
                [
                    'cancelled_at' => now()->toDateTimeString(),
                    'original_expire_date' => $subscription->expire_date,
                    'plan_name' => $subscription->subscription_plan,
                ]
            );

            return redirect()->route('myPlan')->with('message', __('Your subscription has been cancelled. It will remain active until it expires on :date', ['date' => Carbon::parse($subscription->expire_date)->format('M d, Y')]));
        }

        // Check if payment ID is a preapproval ID (starts with a letter)
        $isPreapproval = preg_match('/^[a-zA-Z]/', $transaction->payment_id);

        try {
            // Get appropriate API key
            $apiKey = opt('MERCADO_SECRET_KEY');

            if (empty($apiKey)) {
                throw new \Exception('Mercado Pago API key is not configured');
            }

            $curl = curl_init();

            if ($isPreapproval) {
                // For recurring subscriptions, we need to cancel the preapproval
                curl_setopt_array($curl, [
                    CURLOPT_URL => "https://api.mercadopago.com/preapproval/{$transaction->payment_id}",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CUSTOMREQUEST => 'PUT',
                    CURLOPT_POSTFIELDS => json_encode(['status' => 'cancelled']),
                    CURLOPT_HTTPHEADER => [
                        'Content-Type: application/json',
                        'Authorization: Bearer ' . $apiKey
                    ],
                ]);
            } else {
                // For one-time payments, we don't need to cancel anything on Mercado Pago's side
                // as it's already been processed
                $apiCallNeeded = false;
            }

            // Only make the API call if needed
            if (isset($apiCallNeeded) && !$apiCallNeeded) {
                $apiSuccess = true;
            } else {
                $response = curl_exec($curl);
                $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                curl_close($curl);

                $apiSuccess = $httpStatus >= 200 && $httpStatus < 300;

                // Log the response
                Log::info('Mercado Pago cancellation response', [
                    'payment_id' => $transaction->payment_id,
                    'http_status' => $httpStatus,
                    'response' => $response
                ]);
            }

            // Update local subscription status regardless of API response
            // This way subscription stays active on our site until expiry
            $subscription->status = 'cancelled';
            $subscription->save();

            // Record the cancellation in transactions
            $this->recordTransaction(
                Auth::id(),
                'subscription_cancellation',
                $subscription->id,
                SubscriptionPlanSell::class,
                0,
                'Mercado',
                $transaction->payment_id,
                'completed',
                'Subscription cancelled: ' . $subscription->subscription_plan,
                [
                    'cancelled_at' => now()->toDateTimeString(),
                    'original_expire_date' => $subscription->expire_date,
                    'plan_name' => $subscription->subscription_plan,
                    'api_success' => $apiSuccess
                ]
            );

            return redirect()->route('myPlan')->with('message', __('Your subscription has been cancelled. It will remain active until it expires on :date', ['date' => Carbon::parse($subscription->expire_date)->format('M d, Y')]));
        } catch (\Exception $e) {
            Log::error('Error cancelling Mercado Pago subscription: ' . $e->getMessage());

            // Even if the API call fails, still mark as cancelled in our system
            $subscription->status = 'cancelled';
            $subscription->save();

            return redirect()->route('myPlan')->with('message', __('Your subscription has been cancelled locally but there was an issue with Mercado Pago. It will remain active until it expires on :date', ['date' => Carbon::parse($subscription->expire_date)->format('M d, Y')]));
        }
    }

    /**
     * API endpoint to verify payment without redirect
     * This is used by the frontend to verify payments asynchronously
     */
    public function apiVerifyPayment(Request $request)
    {
        // Validate request
        $request->validate([
            'payment_id' => 'required|string',
            'is_preapproval' => 'required|boolean'
        ]);

        $paymentId = $request->payment_id;
        $isPreapproval = $request->is_preapproval;

        try {
            // Call appropriate verification method based on payment type
            $success = $isPreapproval
                ? $this->verifyAndUpdatePreapproval($paymentId)
                : $this->verifyAndUpdatePayment($paymentId);

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => __('Payment verified successfully! Your subscription is now active.')
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => __('Unable to verify payment. The payment may not be approved yet or has been cancelled.')
                ]);
            }
        } catch (\Exception $e) {
            Log::error('API Payment verification error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => __('An error occurred during verification. Please try again later or contact support.'),
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
