<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrivateStreamRequest;
use App\Models\User;
use App\Models\Transaction;
use App\Models\PrivateStreamFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Refund;
use Stripe\Exception\ApiErrorException;

class StreamsController extends Controller
{
    public function index(Request $request)
    {
        $query = PrivateStreamRequest::with(['streamer', 'user', 'transactions', 'feedbacks'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('streamer')) {
            $query->whereHas('streamer', function($q) use ($request) {
                $q->where('username', 'like', '%' . $request->streamer . '%')
                  ->orWhere('name', 'like', '%' . $request->streamer . '%');
            });
        }

        if ($request->filled('user')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('username', 'like', '%' . $request->user . '%')
                  ->orWhere('name', 'like', '%' . $request->user . '%');
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('amount_min')) {
            $query->where('streamer_fee', '>=', $request->amount_min);
        }

        if ($request->filled('amount_max')) {
            $query->where('streamer_fee', '<=', $request->amount_max);
        }

        if ($request->filled('has_disputes')) {
            if ($request->has_disputes == '1') {
                $query->where('has_dispute', true);
            } else {
                $query->where('has_dispute', false);
            }
        }

        $streams = $query->paginate(20);

        // Get statistics for dashboard cards
        $stats = $this->getStreamStats();

        // Get live streams for quick access
        $liveStreams = PrivateStreamRequest::with(['user', 'streamer'])
            ->where('status', 'in_progress')
            ->orderBy('actual_start_time', 'desc')
            ->get();

        return view('admin.streams.index', compact('streams', 'stats', 'liveStreams'));
    }

    public function show($id)
    {
        $stream = PrivateStreamRequest::with([
            'streamer', 
            'user', 
            'transactions', 
            'feedbacks',
            'feedbacks.user'
        ])->findOrFail($id);

        return view('admin.streams.show', compact('stream'));
    }

    public function cancel(Request $request, $id)
    {
        $stream = PrivateStreamRequest::findOrFail($id);
        
        if (!in_array($stream->status, ['pending', 'accepted', 'in_progress'])) {
            return back()->with('error', 'Stream cannot be cancelled in current status.');
        }

        try {
            DB::beginTransaction();

            // Update the request status to cancelled
            $stream->update([
                'status' => 'cancelled',
                'admin_cancelled_at' => now(),
                'cancellation_reason' => $request->reason ?? 'Cancelled by admin'
            ]);

            // Refund the room rental tokens to the user
            $user = User::findOrFail($stream->user_id);
            $user->tokens += $stream->room_rental_tokens;
            $user->save();

            // Record token refund transaction
            $this->recordTransaction(
                $user->id,
                'room_rental_refund',
                $stream->id,
                PrivateStreamRequest::class,
                $stream->room_rental_tokens,
                'tokens',
                null,
                'completed',
                'Refund for admin cancelled private stream request',
                [
                    'streamer_id' => $stream->streamer_id,
                    'streamer_name' => $stream->streamer->name,
                    'reason' => 'Cancelled by admin',
                    'admin_id' => auth()->id()
                ]
            );

            // Update fee transaction status to cancelled
            Transaction::where('reference_id', $stream->id)
                ->where('reference_type', PrivateStreamRequest::class)
                ->where('transaction_type', 'private_stream_fee')
                ->update(['status' => 'cancelled']);

            // Refund tokens from streamer if they were already awarded
            if ($stream->payment_status === 'captured' && $stream->tokens_awarded > 0) {
                $streamer = User::findOrFail($stream->streamer_id);
                
                // Check if streamer has enough tokens to refund
                if ($streamer->tokens >= $stream->tokens_awarded) {
                    $streamer->tokens -= $stream->tokens_awarded;
                    $streamer->save();
                    
                    // Record token refund transaction
                    $this->recordTransaction(
                        $streamer->id,
                        'private_stream_token_refund',
                        $stream->id,
                        PrivateStreamRequest::class,
                        $stream->tokens_awarded,
                        'tokens',
                        null,
                        'completed',
                        'Token refund due to admin cancelled stream',
                        [
                            'user_id' => $stream->user_id,
                            'user_name' => $stream->user->name,
                            'reason' => 'Stream cancelled by admin',
                            'tokens_refunded' => $stream->tokens_awarded,
                            'admin_id' => auth()->id(),
                            'cancelled_at' => Carbon::now()->toISOString()
                        ]
                    );
                    
                    Log::info("Refunded {$stream->tokens_awarded} tokens from streamer for admin cancelled stream {$stream->id}");
                } else {
                    Log::warning("Streamer {$streamer->id} doesn't have enough tokens to refund for admin cancelled stream {$stream->id}");
                }
            }

            // Process refund based on payment method
            if ($stream->payment_id) {
                if ($stream->payment_method === 'stripe') {
                    // Handle Stripe refund based on payment status
                    try {
                        Stripe::setApiKey(opt('STRIPE_SECRET_KEY'));
                        $paymentIntent = PaymentIntent::retrieve($stream->payment_id);
                        
                        if ($paymentIntent->status === 'requires_capture') {
                            // Payment not captured yet, can cancel
                            $paymentIntent->cancel();
                        } elseif ($paymentIntent->status === 'succeeded') {
                            // Payment already captured, need to refund
                            Refund::create(['payment_intent' => $stream->payment_id]);
                        }
                        
                        // Record successful Stripe refund transaction
                        $this->recordTransaction(
                            $user->id,
                            'stripe_refund',
                            $stream->id,
                            PrivateStreamRequest::class,
                            $stream->streamer_fee,
                            'stripe',
                            $stream->payment_id,
                            'completed',
                            'Stripe refund for admin cancelled private stream request',
                            [
                                'payment_id' => $stream->payment_id,
                                'payment_status' => $paymentIntent->status,
                                'reason' => 'Cancelled by admin',
                                'admin_id' => auth()->id(),
                                'cancelled_at' => Carbon::now()->toISOString()
                            ]
                        );
                    } catch (\Exception $e) {
                        // Log the error but don't fail the cancellation
                        Log::error("Stripe refund failed for admin stream cancellation: " . $e->getMessage(), [
                            'request_id' => $stream->id,
                            'payment_id' => $stream->payment_id
                        ]);
                    }
                } elseif ($stream->payment_method === 'mercado_pago') {
                    // Process Mercado Pago refund
                    try {
                        $refundResult = $this->processMercadoPagoRefund($stream->payment_id, $stream->streamer_fee);
                        
                        if ($refundResult['success']) {
                            // Record successful Mercado Pago refund transaction
                            $this->recordTransaction(
                                $user->id,
                                'mercado_pago_refund',
                                $stream->id,
                                PrivateStreamRequest::class,
                                $stream->streamer_fee,
                                'mercado_pago',
                                $stream->payment_id,
                                'completed',
                                'Mercado Pago refund for admin cancelled private stream request',
                                [
                                    'payment_id' => $stream->payment_id,
                                    'refund_id' => $refundResult['refund_id'],
                                    'reason' => 'Cancelled by admin',
                                    'admin_id' => auth()->id(),
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
                            $stream->id,
                            PrivateStreamRequest::class,
                            $stream->streamer_fee,
                            'mercado_pago',
                            $stream->payment_id,
                            'pending',
                            'Mercado Pago refund failed - requires manual processing',
                            [
                                'payment_id' => $stream->payment_id,
                                'error' => $e->getMessage(),
                                'reason' => 'Cancelled by admin',
                                'admin_id' => auth()->id(),
                                'cancelled_at' => Carbon::now()->toISOString(),
                                'requires_manual_review' => true
                            ]
                        );
                        
                        // Log the error but don't fail the cancellation
                        Log::error("Mercado Pago refund failed for admin stream cancellation: " . $e->getMessage(), [
                            'request_id' => $stream->id,
                            'payment_id' => $stream->payment_id
                        ]);
                    }
                }
            }

            DB::commit();

            return back()->with('success', 'Stream cancelled successfully. Full refund has been processed.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'An error occurred while cancelling the stream: ' . $e->getMessage());
        }
    }

    public function interrupt(Request $request, $id)
    {
        $stream = PrivateStreamRequest::findOrFail($id);

        if ($stream->status !== 'in_progress') {
            return back()->with('error', 'Only active streams can be interrupted.');
        }

        try {
            DB::beginTransaction();

            // Calculate actual duration
            $actualDuration = $stream->actual_start_time ? 
                now()->diffInMinutes($stream->actual_start_time) : 0;

            // Update stream status
            $stream->update([
                'status' => 'interrupted',
                'stream_ended_at' => now(),
                'actual_duration_minutes' => $actualDuration,
                'interruption_reason' => $request->reason ?? 'Interrupted by admin',
                'requires_feedback' => false // Admin interruption doesn't require feedback
            ]);

            // Refund the room rental tokens to the user
            $user = User::findOrFail($stream->user_id);
            $user->tokens += $stream->room_rental_tokens;
            $user->save();

            // Record token refund transaction
            $this->recordTransaction(
                $user->id,
                'room_rental_refund',
                $stream->id,
                PrivateStreamRequest::class,
                $stream->room_rental_tokens,
                'tokens',
                null,
                'completed',
                'Refund for admin interrupted private stream request',
                [
                    'streamer_id' => $stream->streamer_id,
                    'streamer_name' => $stream->streamer->name,
                    'reason' => 'Interrupted by admin',
                    'admin_id' => auth()->id(),
                    'actual_duration_minutes' => $actualDuration
                ]
            );

            // Update fee transaction status to interrupted
            Transaction::where('reference_id', $stream->id)
                ->where('reference_type', PrivateStreamRequest::class)
                ->where('transaction_type', 'private_stream_fee')
                ->update(['status' => 'interrupted']);

            // Refund tokens from streamer if they were already awarded
            if ($stream->payment_status === 'captured' && $stream->tokens_awarded > 0) {
                $streamer = User::findOrFail($stream->streamer_id);
                
                // Check if streamer has enough tokens to refund
                if ($streamer->tokens >= $stream->tokens_awarded) {
                    $streamer->tokens -= $stream->tokens_awarded;
                    $streamer->save();
                    
                    // Record token refund transaction
                    $this->recordTransaction(
                        $streamer->id,
                        'private_stream_token_refund',
                        $stream->id,
                        PrivateStreamRequest::class,
                        $stream->tokens_awarded,
                        'tokens',
                        null,
                        'completed',
                        'Token refund due to admin interrupted stream',
                        [
                            'user_id' => $stream->user_id,
                            'user_name' => $stream->user->name,
                            'reason' => 'Stream interrupted by admin',
                            'tokens_refunded' => $stream->tokens_awarded,
                            'admin_id' => auth()->id(),
                            'actual_duration_minutes' => $actualDuration,
                            'interrupted_at' => Carbon::now()->toISOString()
                        ]
                    );
                    
                    Log::info("Refunded {$stream->tokens_awarded} tokens from streamer for admin interrupted stream {$stream->id}");
                } else {
                    Log::warning("Streamer {$streamer->id} doesn't have enough tokens to refund for admin interrupted stream {$stream->id}");
                }
            }

            // Process refund based on payment method
            if ($stream->payment_id) {
                if ($stream->payment_method === 'stripe') {
                    // Handle Stripe refund based on payment status
                    try {
                        Stripe::setApiKey(opt('STRIPE_SECRET_KEY'));
                        $paymentIntent = PaymentIntent::retrieve($stream->payment_id);
                        
                        if ($paymentIntent->status === 'requires_capture') {
                            // Payment not captured yet, can cancel
                            $paymentIntent->cancel();
                        } elseif ($paymentIntent->status === 'succeeded') {
                            // Payment already captured, need to refund
                            Refund::create(['payment_intent' => $stream->payment_id]);
                        }
                        
                        // Record successful Stripe refund transaction
                        $this->recordTransaction(
                            $user->id,
                            'stripe_refund',
                            $stream->id,
                            PrivateStreamRequest::class,
                            $stream->streamer_fee,
                            'stripe',
                            $stream->payment_id,
                            'completed',
                            'Stripe refund for admin interrupted private stream request',
                            [
                                'payment_id' => $stream->payment_id,
                                'payment_status' => $paymentIntent->status,
                                'reason' => 'Interrupted by admin',
                                'admin_id' => auth()->id(),
                                'actual_duration_minutes' => $actualDuration,
                                'interrupted_at' => Carbon::now()->toISOString()
                            ]
                        );
                    } catch (\Exception $e) {
                        // Log the error but don't fail the interruption
                        Log::error("Stripe refund failed for admin stream interruption: " . $e->getMessage(), [
                            'request_id' => $stream->id,
                            'payment_id' => $stream->payment_id
                        ]);
                    }
                } elseif ($stream->payment_method === 'mercado_pago') {
                    // Process Mercado Pago refund
                    try {
                        $refundResult = $this->processMercadoPagoRefund($stream->payment_id, $stream->streamer_fee);
                        
                        if ($refundResult['success']) {
                            // Record successful Mercado Pago refund transaction
                            $this->recordTransaction(
                                $user->id,
                                'mercado_pago_refund',
                                $stream->id,
                                PrivateStreamRequest::class,
                                $stream->streamer_fee,
                                'mercado_pago',
                                $stream->payment_id,
                                'completed',
                                'Mercado Pago refund for admin interrupted private stream request',
                                [
                                    'payment_id' => $stream->payment_id,
                                    'refund_id' => $refundResult['refund_id'],
                                    'reason' => 'Interrupted by admin',
                                    'admin_id' => auth()->id(),
                                    'actual_duration_minutes' => $actualDuration,
                                    'interrupted_at' => Carbon::now()->toISOString()
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
                            $stream->id,
                            PrivateStreamRequest::class,
                            $stream->streamer_fee,
                            'mercado_pago',
                            $stream->payment_id,
                            'pending',
                            'Mercado Pago refund failed - requires manual processing',
                            [
                                'payment_id' => $stream->payment_id,
                                'error' => $e->getMessage(),
                                'reason' => 'Interrupted by admin',
                                'admin_id' => auth()->id(),
                                'actual_duration_minutes' => $actualDuration,
                                'interrupted_at' => Carbon::now()->toISOString(),
                                'requires_manual_review' => true
                            ]
                        );
                        
                        // Log the error but don't fail the interruption
                        Log::error("Mercado Pago refund failed for admin stream interruption: " . $e->getMessage(), [
                            'request_id' => $stream->id,
                            'payment_id' => $stream->payment_id
                        ]);
                    }
                }
            }

            DB::commit();

            return back()->with('success', "Stream has been interrupted and full refund processed to {$user->username}.");
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'An error occurred while interrupting the stream: ' . $e->getMessage());
        }
    }

    public function releasePayment($id)
    {
        $stream = PrivateStreamRequest::findOrFail($id);
        
        if ($stream->status !== 'completed' || $stream->released_at) {
            return back()->with('error', 'Payment cannot be released for this stream.');
        }

        // Check if tokens have already been awarded to prevent duplicates
        if ($stream->tokens_awarded > 0) {
            return back()->with('error', 'Payment has already been released for this stream. Tokens awarded: ' . $stream->tokens_awarded);
        }

        // Calculate tokens using the same logic as convertPaymentToTokens
        $adminCommissionPercent = opt('admin_commission_private_room', 50);
        $streamerEarnings = $stream->streamer_fee - (($stream->streamer_fee * $adminCommissionPercent) / 100);
        $tokenValue = opt('token_value', 0.01);
        $tokensToAdd = floor($streamerEarnings / $tokenValue);

        if ($tokensToAdd <= 0) {
            return back()->with('error', 'Invalid token calculation. Please check token value configuration.');
        }

        // Release payment to streamer
        $stream->update([
            'released_at' => now(),
            'released_by' => auth()->id(),
            'tokens_awarded' => $tokensToAdd
        ]);

        // Add tokens to streamer account
        $streamer = $stream->streamer;
        $streamer->increment('tokens', $tokensToAdd);

        // Create transaction record
        Transaction::create([
            'user_id' => $streamer->id,
            'transaction_type' => 'private_stream_earning',
            'amount' => $tokensToAdd,
            'currency' => 'tokens',
            'status' => 'completed',
            'description' => "Admin payment release - Private stream earning from {$stream->user->username}",
            'reference_id' => $stream->id,
            'reference_type' => PrivateStreamRequest::class,
            'metadata' => json_encode([
                'original_payment_amount' => $stream->streamer_fee,
                'admin_commission_percent' => $adminCommissionPercent,
                'streamer_earnings_usd' => $streamerEarnings,
                'token_value' => $tokenValue,
                'released_by_admin' => auth()->id()
            ])
        ]);

        return back()->with('success', "Payment has been released to the streamer. {$tokensToAdd} tokens awarded.");
    }

    public function resolveDispute(Request $request, $id)
    {
        $request->validate([
            'resolution' => 'required|in:favor_user,favor_streamer',
            'resolution_reason' => 'required|string|max:500'
        ]);

        $stream = PrivateStreamRequest::findOrFail($id);
        
        if (!$stream->has_dispute) {
            return back()->with('error', 'No active dispute found for this stream.');
        }

        if ($request->resolution === 'favor_user') {
            // Resolve in favor of user - process refund like cancellation
            $refundAmount = $stream->streamer_fee;
            
            // Add refund amount to user's token balance
            $user = $stream->user;
            $user->increment('tokens', $refundAmount);

            // Create refund transaction
            Transaction::create([
                'user_id' => $user->id,
                'transaction_type' => 'dispute_refund',
                'amount' => $refundAmount,
                'currency' => $stream->currency ?? 'USD',
                'payment_method' => 'admin_dispute_resolution',
                'status' => 'completed',
                'description' => "Dispute resolved in favor of user - Stream #{$stream->id}: {$request->resolution_reason}",
                'reference_id' => $stream->id,
                'reference_type' => PrivateStreamRequest::class
            ]);

            // Update stream with refund info
            $stream->update([
                'has_dispute' => false,
                'dispute_resolved_at' => now(),
                'dispute_resolved_by' => auth()->id(),
                'refund_amount' => $refundAmount,
                'refund_reason' => "Dispute resolved in favor of user: {$request->resolution_reason}",
                'refunded_at' => now(),
                'refunded_by' => auth()->id()
            ]);

            return back()->with('success', "Dispute resolved in favor of user. \${$refundAmount} refunded to {$user->username}.");

        } else {
            // Resolve in favor of streamer - release payment like completion
            
            // Check if tokens have already been awarded to prevent duplicates
            if ($stream->tokens_awarded > 0) {
                return back()->with('error', 'Payment has already been released for this stream. Tokens awarded: ' . $stream->tokens_awarded);
            }

            // Calculate tokens using the same logic as convertPaymentToTokens
            $adminCommissionPercent = opt('admin_commission_private_room', 50);
            $streamerEarnings = $stream->streamer_fee - (($stream->streamer_fee * $adminCommissionPercent) / 100);
            $tokenValue = opt('token_value', 0.01);
            $tokensToAdd = floor($streamerEarnings / $tokenValue);

            if ($tokensToAdd <= 0) {
                return back()->with('error', 'Invalid token calculation. Please check token value configuration.');
            }

            // Add tokens to streamer account
            $streamer = $stream->streamer;
            $streamer->increment('tokens', $tokensToAdd);

            // Create payout transaction
            Transaction::create([
                'user_id' => $streamer->id,
                'transaction_type' => 'dispute_payout',
                'amount' => $tokensToAdd,
                'currency' => 'tokens',
                'payment_method' => 'admin_dispute_resolution',
                'status' => 'completed',
                'description' => "Dispute resolved in favor of streamer - Stream #{$stream->id}: {$request->resolution_reason}",
                'reference_id' => $stream->id,
                'reference_type' => PrivateStreamRequest::class,
                'metadata' => json_encode([
                    'original_payment_amount' => $stream->streamer_fee,
                    'admin_commission_percent' => $adminCommissionPercent,
                    'streamer_earnings_usd' => $streamerEarnings,
                    'token_value' => $tokenValue,
                    'dispute_resolved_by' => auth()->id()
                ])
            ]);

            // Update stream with release info
            $stream->update([
                'has_dispute' => false,
                'dispute_resolved_at' => now(),
                'dispute_resolved_by' => auth()->id(),
                'released_at' => now(),
                'released_by' => auth()->id(),
                'tokens_awarded' => $tokensToAdd,
                'release_reason' => "Dispute resolved in favor of streamer: {$request->resolution_reason}"
            ]);

            return back()->with('success', "Dispute resolved in favor of streamer. {$tokensToAdd} tokens awarded to {$streamer->username}.");
        }
    }

    public function refundUser(Request $request, $id)
    {
        $request->validate([
            'refund_reason' => 'required|string|max:500'
        ]);

        $stream = PrivateStreamRequest::findOrFail($id);
        
        if ($stream->status === 'pending') {
            return back()->with('error', 'Cannot refund a pending stream. Cancel it instead.');
        }

        if ($stream->refunded_at) {
            return back()->with('error', 'This stream has already been refunded.');
        }

        // Refund both room rental tokens and streamer fee (full cancellation logic)
        $roomRentalRefund = $stream->room_rental_tokens;
        $streamerFeeRefund = $stream->streamer_fee;
        $totalRefund = $roomRentalRefund + $streamerFeeRefund;
        
        $user = $stream->user;

        // 1. Refund room rental tokens to user's token balance
        if ($roomRentalRefund > 0) {
            $user->increment('tokens', $roomRentalRefund);
            
            Transaction::create([
                'user_id' => $user->id,
                'transaction_type' => 'room_rental_refund',
                'amount' => $roomRentalRefund,
                'currency' => 'tokens',
                'payment_method' => 'admin_refund',
                'status' => 'completed',
                'description' => "Room rental refund for stream #{$stream->id}: {$request->refund_reason}",
                'reference_id' => $stream->id,
                'reference_type' => PrivateStreamRequest::class
            ]);
        }

        // 2. Refund streamer fee (this would cancel external payment like Stripe/Mercado)
        if ($streamerFeeRefund > 0) {
            // Here you would integrate with Stripe/Mercado Pago to cancel the payment
            // For now, we'll add it as tokens but in production you'd:
            // - Cancel the Stripe payment intent
            // - Refund the Mercado Pago transaction
            // - Or reverse the external payment method used
            
            $user->increment('tokens', $streamerFeeRefund);
            
            Transaction::create([
                'user_id' => $user->id,
                'transaction_type' => 'streamer_fee_refund',
                'amount' => $streamerFeeRefund,
                'currency' => $stream->currency ?? 'USD',
                'payment_method' => 'admin_refund',
                'status' => 'completed',
                'description' => "Streamer fee refund for stream #{$stream->id}: {$request->refund_reason}",
                'reference_id' => $stream->id,
                'reference_type' => PrivateStreamRequest::class
            ]);
        }

        // Update stream with refund information
        $stream->update([
            'refund_amount' => $totalRefund,
            'refund_reason' => $request->refund_reason,
            'refunded_at' => now(),
            'refunded_by' => auth()->id()
        ]);

        return back()->with('success', "Successfully refunded {$roomRentalRefund} tokens + \${$streamerFeeRefund} (Total: \${$totalRefund}) to {$user->username}. External payment cancellation may take 3-5 business days.");
    }

    public function forceReleasePayment(Request $request, $id)
    {
        $request->validate([
            'release_reason' => 'required|string|max:500'
        ]);

        $stream = PrivateStreamRequest::findOrFail($id);
        
        if ($stream->released_at) {
            return back()->with('error', 'Payment has already been released for this stream.');
        }

        // Check if tokens have already been awarded to prevent duplicates
        if ($stream->tokens_awarded > 0) {
            return back()->with('error', 'Payment has already been released for this stream. Tokens awarded: ' . $stream->tokens_awarded);
        }

        // Calculate tokens using the same logic as convertPaymentToTokens
        $adminCommissionPercent = opt('admin_commission_private_room', 50);
        $streamerEarnings = $stream->streamer_fee - (($stream->streamer_fee * $adminCommissionPercent) / 100);
        $tokenValue = opt('token_value', 0.01);
        $tokensToAdd = floor($streamerEarnings / $tokenValue);

        if ($tokensToAdd <= 0) {
            return back()->with('error', 'Invalid token calculation. Please check token value configuration.');
        }

        // Add tokens to streamer account
        $streamer = $stream->streamer;
        $streamer->increment('tokens', $tokensToAdd);

        // Create transaction record
        Transaction::create([
            'user_id' => $streamer->id,
            'transaction_type' => 'private_stream_earning',
            'amount' => $tokensToAdd,
            'currency' => 'tokens',
            'payment_method' => 'admin_force_release',
            'status' => 'completed',
            'description' => "Admin forced payment release for stream #{$stream->id}: {$request->release_reason}",
            'reference_id' => $stream->id,
            'reference_type' => PrivateStreamRequest::class,
            'metadata' => json_encode([
                'original_payment_amount' => $stream->streamer_fee,
                'admin_commission_percent' => $adminCommissionPercent,
                'streamer_earnings_usd' => $streamerEarnings,
                'token_value' => $tokenValue,
                'force_released_by' => auth()->id(),
                'release_reason' => $request->release_reason
            ])
        ]);

        // Update stream with release information
        $stream->update([
            'released_at' => now(),
            'released_by' => auth()->id(),
            'tokens_awarded' => $tokensToAdd,
            'release_reason' => $request->release_reason
        ]);

        return back()->with('success', "Successfully released {$tokensToAdd} tokens to {$streamer->username}.");
    }

    private function getStreamStats()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        return [
            'total_streams' => PrivateStreamRequest::count(),
            'active_streams' => PrivateStreamRequest::where('status', 'in_progress')->count(),
            'completed_today' => PrivateStreamRequest::where('status', 'completed')
                ->whereDate('created_at', $today)->count(),
            'revenue_today' => PrivateStreamRequest::where('status', 'completed')
                ->whereDate('created_at', $today)->sum('streamer_fee'),
            'revenue_month' => PrivateStreamRequest::where('status', 'completed')
                ->where('created_at', '>=', $thisMonth)->sum('streamer_fee'),
            'pending_disputes' => PrivateStreamRequest::where('has_dispute', true)
                ->whereNull('dispute_resolved_at')->count(),
            'pending_payments' => PrivateStreamRequest::where('status', 'completed')
                ->whereNull('released_at')->count(),
        ];
    }

    public function export(Request $request)
    {
        $fileName = 'streams-' . date('Y-m-d') . '.csv';
        
        $query = PrivateStreamRequest::with(['streamer', 'user', 'transactions', 'feedbacks'])
            ->orderBy('created_at', 'desc');

        // Apply same filters as index page
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('streamer')) {
            $query->whereHas('streamer', function($q) use ($request) {
                $q->where('username', 'like', '%' . $request->streamer . '%')
                  ->orWhere('name', 'like', '%' . $request->streamer . '%');
            });
        }

        if ($request->filled('user')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('username', 'like', '%' . $request->user . '%')
                  ->orWhere('name', 'like', '%' . $request->user . '%');
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('has_disputes')) {
            if ($request->has_disputes == '1') {
                $query->where('has_dispute', true);
            } else {
                $query->where('has_dispute', false);
            }
        }

        $streams = $query->get();

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array(
            'ID',
            'Streamer Name',
            'Streamer Username',
            'Customer Name', 
            'Customer Username',
            'Status',
            'Amount',
            'Duration (minutes)',
            'Scheduled Date',
            'Scheduled Time',
            'Actual Start Time',
            'End Time',
            'Payment Status',
            'Has Dispute',
            'Special Requests',
            'Created Date',
            'Transactions Count',
            'Feedback Count'
        );

        $callback = function () use ($streams, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($streams as $stream) {
                $row = array(
                    $stream->id,
                    $stream->streamer ? $stream->streamer->name : 'Deleted User',
                    $stream->streamer ? $stream->streamer->username : 'N/A',
                    $stream->user ? $stream->user->name : 'Deleted User',
                    $stream->user ? $stream->user->username : 'N/A',
                    ucfirst(str_replace('_', ' ', $stream->status)),
                    '$' . number_format($stream->streamer_fee, 2),
                    $stream->duration_minutes,
                    Carbon::parse($stream->requested_date)->format('Y-m-d'),
                    $stream->requested_time,
                    $stream->actual_start_time ? Carbon::parse($stream->actual_start_time)->format('Y-m-d H:i:s') : 'Not started',
                    $stream->stream_ended_at ? Carbon::parse($stream->stream_ended_at)->format('Y-m-d H:i:s') : 'Not ended',
                    $stream->released_at ? 'Released' : ($stream->status === 'completed' ? 'Pending' : 'N/A'),
                    $stream->has_dispute ? 'Yes' : 'No',
                    $stream->special_requests ?: 'None',
                    $stream->created_at->format('Y-m-d H:i:s'),
                    $stream->transactions->count(),
                    $stream->feedbacks->count()
                );

                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

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
} 