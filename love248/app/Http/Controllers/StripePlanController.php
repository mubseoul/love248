<?php

namespace App\Http\Controllers;

use Stripe\StripeClient as StripeClient;
use App\Models\SubscriptionPlanSell;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use App\Models\TokenSale;
use App\Models\TokenPack;
use App\Models\Transaction;
use App\Models\User;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StripePlanController extends Controller
{
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

    //    function for purchase subscription plan

    public function purchasesss(SubscriptionPlan $tokPack, Request $request)
    {
        $stripeImg = asset('images/stripe-cards.png');
        $publicKey = opt('STRIPE_PUBLIC_KEY');

        // Use database transaction to ensure data consistency
        DB::beginTransaction();

        try {
            $currentDate = Carbon::now();
            $user = $request->user();

            // Handle subscription upgrades/downgrades
            $subscriptionChange = $this->handleSubscriptionChange($tokPack, $user);

            // Set expiration date based on plan days
            $expireDate = $currentDate->addDays($tokPack->days)->toDateString();

            // If same plan, add days to existing expiration
            if ($subscriptionChange['has_active_subscription'] && $subscriptionChange['is_same_plan']) {
                $expireDate = Carbon::parse($subscriptionChange['active_subscription']->expire_date)
                    ->addDays($tokPack->days)
                    ->toDateString();
            }

            // Create subscription record
            $sale = SubscriptionPlanSell::create([
                'user_id' => $user->id,
                'subscription_plan' => $tokPack->subscription_name,
                'price' => $subscriptionChange['adjusted_price'],
                'expire_date' => $expireDate,
                'status' => 'pending',
                'gateway' => 'Stripe',
            ]);

            // Store original price in the metadata
            $metadataArray = [
                'plan_id' => $tokPack->id,
                'plan_name' => $tokPack->subscription_name,
                'plan_days' => $tokPack->days,
                'expire_date' => $expireDate,
                'subscription_id' => $sale->id,
                'original_price' => $tokPack->subscription_price
            ];

            // If upgrading/downgrading, add related info
            if ($subscriptionChange['has_active_subscription'] && !$subscriptionChange['is_same_plan']) {
                $metadataArray['is_upgrade'] = $subscriptionChange['is_upgrade'];
                $metadataArray['previous_subscription_id'] = $subscriptionChange['active_subscription']->id;
                $metadataArray['remaining_value'] = $subscriptionChange['remaining_value'];
            }

            // Record the pending transaction using the helper method
            $this->recordTransaction(
                $user->id,
                'subscription',
                $sale->id,
                SubscriptionPlanSell::class,
                $subscriptionChange['adjusted_price'],
                'Stripe',
                null, // payment ID not yet available
                'pending',
                'Subscription purchase: ' . $tokPack->subscription_name,
                $metadataArray
            );

            DB::commit();

            $saleId = $sale->id;

            // Pass original/adjusted prices to the payment intent method
            $cs = $this->paymentIntent($tokPack, $sale->id, $subscriptionChange['adjusted_price']);

            return Inertia::render('Subscriptionplan/StripeForm', compact('tokPack', 'stripeImg', 'publicKey', 'cs', 'saleId'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Subscription Creation Error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while processing your request. Please try again.');
        }
    }

    // get client secret
    public function paymentIntent(SubscriptionPlan $tokenPack, $saleId, $adjustedAmount = null)
    {
        try {
            $stripe = new StripeClient(opt('STRIPE_SECRET_KEY'));

            // Use adjusted amount if provided, otherwise use regular price
            $amount = $adjustedAmount ?? $tokenPack->subscription_price;
            $stripeAmount = $amount * 100;

            // Create customer
            $customer = $stripe->customers->create([
                'description' => 'Customer for ' . Auth::user()->email,
            ]);

            // Save the customer ID to the user
            $user = User::find(Auth::id());
            $user->stripe_customer_id = $customer->id;
            $user->save();

            // Create the payment intent
            $pi = $stripe->paymentIntents->create([
                'amount' => (int)$stripeAmount, // Convert to integer to avoid decimal issues
                'currency' => opt('payment-settings.currency_code'),
                'customer' => $customer->id,
                'setup_future_usage' => 'off_session',
                'payment_method_types' => ['card'],
                'metadata' => [
                    'sale_id' => $saleId,
                    'adjusted_amount' => $adjustedAmount ? 'yes' : 'no'
                ],
            ]);

            return $pi->client_secret;
        } catch (\Exception $e) {
            Log::error('Payment Intent Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function processOrders(Request $request)
    {
        $request->validate([
            'payment_intent' => 'required',
            'payment_intent_client_secret' => 'required'
        ]);

        DB::beginTransaction();

        try {
            $stripe = new StripeClient(opt('STRIPE_SECRET_KEY'));
            $intent = $stripe->paymentIntents->retrieve($request->payment_intent, []);

            if ($intent->status == 'succeeded') {
                $meta = $intent->metadata;

                // Determine transaction type
                if (!isset($meta->token)) {
                    // Handle subscription payment
                    $sale = SubscriptionPlanSell::find($meta->sale_id);

                    if ($sale) {
                        // Avoid duplicate processing
                        if ($sale->status == 'active') {
                            DB::rollBack();
                            return Inertia::render('Subscriptionplan/CardSuccess', compact('sale'));
                        }

                        // Update user payment method
                        $user = User::find(Auth::id());
                        $user->stripe_payment_method_id = $intent->payment_method;
                        $user->save();

                        // Get the transaction to check for upgrade/downgrade
                        $transaction = Transaction::where('reference_id', $sale->id)
                            ->where('reference_type', SubscriptionPlanSell::class)
                            ->where('transaction_type', 'subscription')
                            ->first();

                        // If this is an upgrade/downgrade, update the previous subscription
                        if ($transaction && $transaction->metadata) {
                            $metadata = json_decode($transaction->metadata, true);

                            if (isset($metadata['previous_subscription_id'])) {
                                $previousSubscription = SubscriptionPlanSell::find($metadata['previous_subscription_id']);
                                if ($previousSubscription) {
                                    $this->updatePreviousSubscription($previousSubscription, $sale->id);
                                }
                            }
                        }

                        // Update transaction status
                        Transaction::where('reference_id', $sale->id)
                            ->where('reference_type', SubscriptionPlanSell::class)
                            ->where('transaction_type', 'subscription')
                            ->update([
                                'status' => 'completed',
                                'payment_id' => $intent->id,
                            ]);

                        // Update subscription status
                        $sale->status = 'active';
                        $sale->save();

                        DB::commit();
                        return Inertia::render('Subscriptionplan/CardSuccess', compact('sale'));
                    }
                } else {
                    // Handle token purchase
                    $sale = TokenSale::find($meta->sale_id);

                    if ($sale) {
                        // Avoid duplicate processing
                        if ($sale->status == 'paid') {
                            DB::rollBack();
                            abort(403);
                        }

                        // Add tokens to user account
                        $request->user()->increment('tokens', $sale->tokens);

                        // Record token purchase transaction using helper method
                        $this->recordTransaction(
                            $request->user()->id,
                            'token_purchase',
                            $sale->id,
                            TokenSale::class,
                            $sale->amount,
                            'Stripe',
                            $intent->id,
                            'completed',
                            'Token purchase: ' . $sale->tokens . ' tokens',
                            [
                                'tokens' => $sale->tokens,
                                'sale_id' => $sale->id,
                                'payment_intent_id' => $intent->id,
                                'stripe_customer' => $intent->customer ?? null,
                                'payment_method' => $intent->payment_method ?? null
                            ]
                        );

                        // Update sale status
                        $sale->status = 'paid';
                        $sale->save();

                        $tokens = $sale->tokens;
                        DB::commit();
                        return Inertia::render('Subscriptionplan/CardSuccess', compact('tokens', 'sale'));
                    }
                }
            } else {
                // Update transaction status to failed
                $meta = $intent->metadata;
                if (isset($meta->sale_id)) {
                    $sale = SubscriptionPlanSell::find($meta->sale_id);
                    if ($sale) {
                        $transaction = Transaction::where('reference_id', $sale->id)
                            ->where('reference_type', SubscriptionPlanSell::class)
                            ->first();

                        $this->recordFailedTransaction(
                            $transaction,
                            $intent->id,
                            'Payment failed with status: ' . $intent->status
                        );
                    }
                }

                DB::rollBack();
                session()->flash('message', __("Payment not complete but: :intentStatus", ['intentStatus' => $intent->status]));
                return back();
            }

            DB::rollBack();
            return back()->with('error', 'An error occurred while processing your payment.');
        } catch (\Exception $e) {
            DB::rollBack();

            // Record transaction failure if we can identify the sale
            if (isset($request->payment_intent)) {
                try {
                    $stripe = new StripeClient(opt('STRIPE_SECRET_KEY'));
                    $intent = $stripe->paymentIntents->retrieve($request->payment_intent, []);

                    $meta = $intent->metadata ?? null;
                    if ($meta && isset($meta->sale_id)) {
                        $sale = SubscriptionPlanSell::find($meta->sale_id);
                        if ($sale) {
                            $transaction = Transaction::where('reference_id', $sale->id)
                                ->where('reference_type', SubscriptionPlanSell::class)
                                ->first();

                            if ($transaction) {
                                $this->recordFailedTransaction(
                                    $transaction,
                                    $intent->id,
                                    'Exception during payment processing: ' . $e->getMessage()
                                );
                            }
                        }
                    }
                } catch (\Exception $logException) {
                    // Just log this exception, don't rethrow
                    Log::error('Error while logging payment failure: ' . $logException->getMessage());
                }
            }

            Log::error('Payment Processing Error: ' . $e->getMessage());
            session()->flash('error', 'An error occurred while processing your payment. Please try again.');
            return back();
        }
    }
}
