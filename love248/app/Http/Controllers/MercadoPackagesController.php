<?php

namespace App\Http\Controllers;

use App\Models\TokenPack;
use App\Models\TokenSale;
use App\Models\Transaction;
use App\Models\User;
use App\Models\MercadopagoTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class MercadoPackagesController extends Controller
{
    /**
     * API Base URL for MercadoPago
     */
    protected $apiBaseUrl = 'https://api.mercadopago.com';



    /**
     * Constructor to initialize controller
     */
    public function __construct()
    {
        $this->middleware(['auth'])->except(['success', 'failure', 'pending', 'webhook']);
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
     * Get payment details via API
     */
    private function getPaymentDetails($paymentId)
    {
        $apiToken = opt('MERCADO_SECRET_KEY');
        
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.mercadopago.com/v1/payments/' . $paymentId,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $apiToken
            ),
        ));
        
        $response = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlErrorNumber = curl_errno($curl);
        $curlError = curl_error($curl);
        curl_close($curl);
        
        if ($curlErrorNumber) {
            throw new \Exception('cURL error: ' . $curlError);
        }
        
        if ($httpStatus >= 400) {
            throw new \Exception('API error: HTTP ' . $httpStatus);
        }
        
        return json_decode($response, true);
    }

    /**
     * Process purchase of token pack
     */
    public function purchase(TokenPack $tokenPack, Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'email' => 'required|email'
            ]);

            $user = Auth::user();

            // Validate token pack price
            if (!isset($tokenPack->price) || empty($tokenPack->price) || !is_numeric($tokenPack->price)) {
                throw new \Exception('Invalid token pack price: ' . $tokenPack->price);
            }

            // Make sure price is properly formatted for Mercado Pago
            $price = floatval($tokenPack->price);
            if ($price <= 0) {
                throw new \Exception('Token pack price must be greater than zero');
            }

            // Create a token sale record
            $tokenSale = TokenSale::create([
                'user_id' => auth()->id(),
                'tokens' => $tokenPack->tokens,
                'amount' => $price,
                'gateway' => 'mercado',
                'status' => 'pending'
            ]);

            // Create transaction record
            $transaction = Transaction::create([
                'user_id' => auth()->id(),
                'transaction_type' => 'token_purchase',
                'reference_id' => $tokenSale->id,
                'reference_type' => 'App\Models\TokenSale',
                'amount' => $price,
                'currency' => 'BRL',
                'payment_method' => 'mercadopago',
                'payment_id' => null,
                'status' => 'pending',
                'description' => 'Token purchase: ' . $tokenPack->tokens . ' tokens',
                'metadata' => json_encode([
                    'token_pack' => [
                        'id' => $tokenPack->id,
                        'tokens' => $tokenPack->tokens
                    ]
                ])
            ]);

            // Define base URL for callbacks
            $baseUrl = request()->getScheme() . '://' . request()->getHost();
            if (request()->getPort() && request()->getPort() != 80 && request()->getPort() != 443) {
                $baseUrl .= ':' . request()->getPort();
            }

            // Set up payer information
            if (env('APP_ENV') !== 'production') {
                $payer = [
                    "name" => 'Test',
                    "surname" => 'test',
                    "email" => $validated['email'],
                ];
            } else {
                $payer = [
                    "name" => $user->name,
                    "surname" => $user->name,
                    "email" => $validated['email'],
                ];
            }

            // Create preference data
            $data = [
                "auto_return" => "approved",
                "back_urls" => [
                    "success" => route('token.mercado.success'),
                    "failure" => route('token.mercado.failure'),
                ],
                "items" => [
                    [
                        "title" => $tokenPack->tokens . ' Tokens',
                        "description" => "Purchase of " . $tokenPack->tokens . " tokens",
                        "currency_id" => "BRL",
                        "quantity" => 1,
                        "unit_price" => $price
                    ]
                ],
                "payer" => $payer,
                "external_reference" => 'token_sale_' . $tokenSale->id
            ];

            // Add notification URL for production environments only
            if (env('APP_ENV') === 'production') {
                $notificationUrl = $baseUrl . '/webhook/mercadopago';
                $data["notification_url"] = $notificationUrl;
            }

            // Convert to JSON
            $json_data = json_encode($data);

            // Get API token (using platform/admin token for token purchases)
            $apiToken = opt('MERCADO_SECRET_KEY');

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
                    'Authorization: Bearer ' . $apiToken
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
                throw new \Exception('cURL error: ' . $curlError);
            }

            $preferenceData = json_decode($response);

            // Check for API errors
            if ($httpStatus >= 400 || isset($preferenceData->error)) {
                $errorMessage = $preferenceData->message ?? $preferenceData->error ?? 'Unknown API error';
                throw new \Exception('MercadoPago API error: ' . $errorMessage);
            }

            // Check for successful response with init_point
            if (isset($preferenceData->id) && isset($preferenceData->init_point)) {
                // Update transaction with payment ID
                $transaction->payment_id = $preferenceData->id;
                $transaction->save();

                // Return the payment URL for frontend to handle the redirect (same as content controller)
                return response()->json([
                    'success' => true,
                    'init_point' => $preferenceData->init_point
                ]);
            } else {
                throw new \Exception('Invalid API response: Missing preference ID or init_point');
            }
        } catch (\Exception $e) {
            Log::error('Mercado Pago token purchase error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error processing payment. Please try again or contact support.'
            ], 500);
        }
    }

    /**
     * Handle successful payment
     */
    public function success(Request $request)
    {
        try {
            // Get payment info from MercadoPago callback
            $paymentId = $request->payment_id ?? $request->collection_id ?? null;
            $externalReference = $request->external_reference ?? null;



            if (!$paymentId) {
                return redirect()->route('tokens.packages')->with('error', 'Payment information not found. Please contact support.');
            }

            // Extract token sale ID from external reference
            $tokenSaleId = null;
            if ($externalReference) {
                $tokenSaleId = str_replace('token_sale_', '', $externalReference);
            }

            // If no external reference, try to find transaction by payment ID
            if (!$tokenSaleId) {
                $transaction = Transaction::where('payment_id', $paymentId)->first();
                if ($transaction && $transaction->reference_type == 'App\Models\TokenSale') {
                    $tokenSaleId = $transaction->reference_id;
                }
            }

            if (!$tokenSaleId) {
                return redirect()->route('tokens.packages')->with('error', 'Payment verification failed. Please contact support.');
            }

            // Get token sale
            $tokenSale = TokenSale::find($tokenSaleId);
            if (!$tokenSale) {
                return redirect()->route('tokens.packages')->with('error', 'Token purchase information not found. Please contact support.');
            }

            // If this sale is already processed and paid, just redirect to prevent double-crediting on refresh
            if ($tokenSale->status === 'paid') {
                return redirect()->route('tokens.history')->with('success', 'Your token purchase was successful. ' . $tokenSale->tokens . ' tokens were added to your account.');
            }

            // Verify payment status with Mercado Pago
            try {
                $payment = $this->getPaymentDetails($paymentId);

                // Check if payment is approved
                if ($payment['status'] == 'approved') {
                    // Use a database transaction to prevent race conditions
                    DB::beginTransaction();
                    try {
                        // Reload the token sale record with a lock to prevent concurrent updates
                        $tokenSale = TokenSale::lockForUpdate()->find($tokenSaleId);

                        // Double-check if already paid to prevent race conditions
                        if ($tokenSale->status !== 'paid') {
                            // Update token sale status
                            $tokenSale->status = 'paid';
                            $tokenSale->save();

                            // Get user
                            $user = User::findOrFail($tokenSale->user_id);

                            // Add tokens to user's balance
                            $user->tokens += $tokenSale->tokens;
                            $user->save();

                            // Update transaction
                            Transaction::where('reference_id', $tokenSale->id)
                                ->where('reference_type', 'App\Models\TokenSale')
                                ->update([
                                    'status' => 'completed',
                                    'payment_id' => $paymentId
                                ]);
                        }

                        DB::commit();
                    } catch (\Exception $dbException) {
                        DB::rollBack();
                        throw $dbException;
                    }

                    // Redirect with success message
                    return redirect()->route('tokens.history')->with('success', 'Token purchase successful! ' . $tokenSale->tokens . ' tokens have been added to your account.');
                } else if ($payment['status'] == 'pending') {
                    // Generic pending message
                    return redirect()->route('tokens.history')->with('info', 'Your payment is pending. Tokens will be added to your account once the payment is confirmed.');
                } else {
                    // Payment not approved or pending
                    return redirect()->route('tokens.packages')->with('error', 'Payment was not approved. Please try again.');
                }
            } catch (\Exception $e) {
                Log::error('Token payment verification failed: ' . $e->getMessage());
                return redirect()->route('tokens.packages')->with('error', 'Payment verification failed. Please contact support.');
            }
        } catch (\Exception $e) {
            Log::error('Mercado Pago token success error: ' . $e->getMessage());
            return redirect()->route('tokens.packages')->with('error', 'Error processing payment. Please contact support.');
        }
    }

    /**
     * Handle failed payment
     */
    public function failure(Request $request)
    {
        return redirect()->route('tokens.packages')->with('error', 'Payment failed. Please try again.');
    }

    /**
     * Handle pending payment
     */
    public function pending(Request $request)
    {
        // Check if we have payment data from session
        $paymentData = $request->session()->get('pix_data');

        if ($paymentData) {
            // If we have PIX data, show a special view with the QR code
            if (isset($paymentData['qr_code']) || isset($paymentData['qr_code_base64'])) {
                return Inertia::render('Tokens/PixPayment', [
                    'pixData' => $paymentData,
                    'message' => 'Please scan the QR code below with your banking app to complete the payment. Your tokens will be added to your account once the payment is confirmed.'
                ]);
            }

            // If we have a ticket URL but no QR code (boleto/ticket payment)
            if (isset($paymentData['ticket_url'])) {
                // Redirect to the ticket URL
                return redirect()->away($paymentData['ticket_url']);
            }
        }

        // Otherwise show generic pending message
        return redirect()->route('tokens.history')->with('info', 'Your payment is pending. Tokens will be added to your account once the payment is confirmed.');
    }

    /**
     * Handle webhook notifications
     */
    public function webhook(Request $request)
    {
        try {
            // Get the notification type
            $type = $request->input('type');

            // Only process payment notifications
            if ($type == 'payment') {
                $paymentId = $request->input('data.id');

                $apiToken = opt('MERCADO_SECRET_KEY');
                if (empty($apiToken)) {
                    return response()->json(['status' => 'error', 'message' => 'Mercado Pago API key is not configured. Please contact the administrator.'], 500);
                }

                // Verify payment with Mercado Pago API
                $payment = $this->getPaymentDetails($paymentId);

                // Get external reference to find our token sale
                $externalReference = $payment['external_reference'] ?? null;

                if (!$externalReference) {
                    return response()->json(['status' => 'error', 'message' => 'No external reference found'], 400);
                }

                $tokenSaleId = str_replace('token_sale_', '', $externalReference);

                // Get token sale
                $tokenSale = TokenSale::find($tokenSaleId);

                if (!$tokenSale) {
                    return response()->json(['status' => 'error', 'message' => 'Token sale not found'], 404);
                }

                // Update transaction status regardless of payment status
                Transaction::where('reference_id', $tokenSale->id)
                    ->where('reference_type', 'App\Models\TokenSale')
                    ->update([
                        'status' => $payment['status'] == 'approved' ? 'completed' : $payment['status'],
                        'payment_id' => $paymentId
                    ]);

                // Process based on payment status
                if ($payment['status'] == 'approved' && $tokenSale->status != 'paid') {
                    // Update token sale status
                    $tokenSale->status = 'paid';
                    $tokenSale->save();

                    // Get user
                    $user = User::find($tokenSale->user_id);

                    if ($user) {
                        // Add tokens to user's balance
                        $user->tokens += $tokenSale->tokens;
                        $user->save();
                    }
                } else if ($payment['status'] == 'cancelled' || $payment['status'] == 'rejected') {
                    // Payment failed or was cancelled
                    $tokenSale->status = 'failed';
                    $tokenSale->save();
                } else if ($payment['status'] == 'pending' || $payment['status'] == 'in_process') {
                    // Payment is pending or in process
                    $tokenSale->status = 'pending';
                    $tokenSale->save();
                }
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Mercado Pago token webhook error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
