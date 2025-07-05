<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\Gallery;
use App\Models\VideoSales;
use App\Models\GallerySales;
use App\Models\Transaction;
use App\Models\Commission;
use App\Models\User;
use App\Models\MercadoAccount;
use App\Notifications\NewVideoSale;
use App\Notifications\NewGallerySale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Str;

class MercadoContentController extends Controller
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
     * Process purchase of video
     */
    public function purchaseVideo(Video $video, Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'email' => 'required|email'
            ]);

            $user = Auth::user();

            // Check if user already has access
            if ($video->canBePlayed) {
                return back()->with('message', __('You already have access to this video'));
            }

            // Check if video is free
            if (floatval($video->price) < 1) {
                return $this->grantFreeAccess('video', $video, $user);
            }

            // Check if streamer has Mercado Pago account
            $mercadoAccount = MercadoAccount::where('user', $video->user_id)->first();
            if (!$mercadoAccount) {
                return back()->with('error', __('Streamer has not connected their Mercado Pago account yet.'));
            }

            // Make sure price is properly formatted for Mercado Pago
            $price = floatval($video->price);
            if ($price <= 0) {
                throw new \Exception('Video price must be greater than zero');
            }

            // Calculate admin commission
            $adminCommission = ($price * opt('admin_commission_videos')) / 100;

            // Create main purchase transaction record (no sale record until payment confirmed)
            $transaction = Transaction::create([
                'user_id' => auth()->id(),
                'transaction_type' => 'video_purchase',
                'reference_id' => null, // Will be set to sale ID after payment confirmation
                'reference_type' => VideoSales::class,
                'amount' => $price,
                'currency' => 'BRL',
                'payment_method' => 'mercadopago',
                'payment_id' => null,
                'status' => 'pending',
                'description' => 'Video purchase: ' . $video->title,
                'metadata' => json_encode([
                    'video_id' => $video->id,
                    'video_title' => $video->title,
                    'streamer_id' => $video->user_id,
                    'email' => $validated['email'],
                    'admin_commission' => $adminCommission,
                    'transaction_group' => 'video_purchase_' . time() . '_' . auth()->id()
                ])
            ]);

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

            // Create preference data with application_fee
            $data = [
                "auto_return" => "approved",
                "back_urls" => [
                    "success" => route('mercado.content.success'),
                    "failure" => route('mercado.content.failure'),
                ],
                "items" => [
                    [
                        "title" => $video->title,
                        "description" => "Purchase of video: " . $video->title,
                        "currency_id" => "BRL",
                        "quantity" => 1,
                        "unit_price" => $price
                    ]
                ],
                "payer" => $payer,
                "external_reference" => 'video_transaction_' . $transaction->id,
                "marketplace_fee" => $adminCommission // Admin commission automatically deducted
            ];

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
                    'Authorization: Bearer ' . $mercadoAccount->access_token // Use streamer's token - money goes to them minus commission
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

                // Return the payment URL for frontend to handle the redirect
                return response()->json([
                    'success' => true,
                    'payment_url' => $preferenceData->init_point
                ]);
            } else {
                throw new \Exception('Invalid API response: Missing preference ID or init_point');
            }
        } catch (\Exception $e) {
            Log::error('Mercado Pago video purchase error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error processing payment. Please try again or contact support.'
            ], 500);
        }
    }

    /**
     * Process purchase of gallery
     */
    public function purchaseGallery(Gallery $gallery, Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'email' => 'required|email'
            ]);

            $user = Auth::user();

            // Check if user already has access
            if ($gallery->canBePlayed) {
                return back()->with('message', __('You already have access to this gallery'));
            }

            // Check if gallery is free
            if (floatval($gallery->price) < 1) {
                return $this->grantFreeAccess('gallery', $gallery, $user);
            }

            // Check if streamer has Mercado Pago account
            $mercadoAccount = MercadoAccount::where('user', $gallery->user_id)->first();
            if (!$mercadoAccount) {
                return back()->with('error', __('Streamer has not connected their Mercado Pago account yet.'));
            }

            // Make sure price is properly formatted for Mercado Pago
            $price = floatval($gallery->price);
            if ($price <= 0) {
                throw new \Exception('Gallery price must be greater than zero');
            }

            // Calculate admin commission
            $adminCommission = ($price * opt('admin_commission_photos')) / 100;

            // Create main purchase transaction record (no sale record until payment confirmed)
            $transaction = Transaction::create([
                'user_id' => auth()->id(),
                'transaction_type' => 'gallery_purchase',
                'reference_id' => null, // Will be set to sale ID after payment confirmation
                'reference_type' => GallerySales::class,
                'amount' => $price,
                'currency' => 'BRL',
                'payment_method' => 'mercadopago',
                'payment_id' => null,
                'status' => 'pending',
                'description' => 'Gallery purchase: ' . $gallery->title,
                'metadata' => json_encode([
                    'gallery_id' => $gallery->id,
                    'gallery_title' => $gallery->title,
                    'streamer_id' => $gallery->user_id,
                    'email' => $validated['email'],
                    'admin_commission' => $adminCommission,
                    'transaction_group' => 'gallery_purchase_' . time() . '_' . auth()->id()
                ])
            ]);

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

            // Create preference data with application_fee
            $data = [
                "auto_return" => "approved",
                "back_urls" => [
                    "success" => route('mercado.content.success'),
                    "failure" => route('mercado.content.failure'),
                ],
                "items" => [
                    [
                        "title" => $gallery->title,
                        "description" => "Purchase of gallery: " . $gallery->title,
                        "currency_id" => "BRL",
                        "quantity" => 1,
                        "unit_price" => $price
                    ]
                ],
                "payer" => $payer,
                "external_reference" => 'gallery_transaction_' . $transaction->id,
                "marketplace_fee" => $adminCommission // Admin commission automatically deducted
            ];

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
                    'Authorization: Bearer ' . $mercadoAccount->access_token // Use streamer's token - money goes to them minus commission
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

                // Return the payment URL for frontend to handle the redirect
                return response()->json([
                    'success' => true,
                    'payment_url' => $preferenceData->init_point
                ]);
            } else {
                throw new \Exception('Invalid API response: Missing preference ID or init_point');
            }
        } catch (\Exception $e) {
            Log::error('Mercado Pago gallery purchase error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error processing payment. Please try again or contact support.'
            ], 500);
        }
    }

    /**
     * Grant free access to content
     * Even for free content, we create a transaction first, then complete it immediately
     */
    private function grantFreeAccess($type, $content, $user)
    {
        DB::beginTransaction();
        try {
            $transactionGroup = 'free_' . $type . '_' . time() . '_' . $user->id;
            
            // Create transaction for free content (for consistency and tracking)
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'transaction_type' => $type === 'video' ? 'video_purchase' : 'gallery_purchase',
                'reference_id' => null, // Will be set to sale ID
                'reference_type' => $type === 'video' ? VideoSales::class : GallerySales::class,
                'amount' => 0,
                'currency' => 'BRL',
                'payment_method' => 'free',
                'payment_id' => 'free_' . time(),
                'status' => 'pending',
                'description' => $type === 'video' ? 'Free video access: ' . $content->title : 'Free gallery access: ' . $content->title,
                'metadata' => json_encode([
                    $type . '_id' => $content->id,
                    $type . '_title' => $content->title,
                    'streamer_id' => $content->user_id,
                    'admin_commission' => 0,
                    'transaction_group' => $transactionGroup
                ])
            ]);

            // Now create the sale record and complete the transaction
            if ($type === 'video') {
                $sale = VideoSales::create([
                    'video_id' => $content->id,
                    'streamer_id' => $content->user_id,
                    'user_id' => $user->id,
                    'price' => 0,
                    'status' => 'completed',
                ]);
                $redirectRoute = 'videos.ordered';
            } else {
                $sale = GallerySales::create([
                    'gallery_id' => $content->id,
                    'streamer_id' => $content->user_id,
                    'user_id' => $user->id,
                    'price' => 0,
                    'status' => 'completed',
                ]);
                $redirectRoute = 'gallery.ordered';
            }

            // Complete the transaction
            $transaction->update([
                'reference_id' => $sale->id,
                'status' => 'completed'
            ]);

            DB::commit();
            
            // Return appropriate response based on request type
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'is_free' => true,
                    'message' => __("Thank you, you can now play the " . $type . "!"),
                    'redirect_url' => route($redirectRoute)
                ]);
            }
            
            return redirect()->route($redirectRoute)->with('message', __("Thank you, you can now play the " . $type . "!"));
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Free content access error: ' . $e->getMessage());
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Error granting access to free content.'
                ], 500);
            }
            
            return redirect()->route('dashboard')->with('error', 'Error granting access to free content.');
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
                return redirect()->route('dashboard')->with('error', 'Payment information not found. Please contact support.');
            }

            // Extract transaction ID from external reference
            $transactionId = null;
            $type = null;
            if ($externalReference) {
                if (strpos($externalReference, 'video_transaction_') === 0) {
                    $transactionId = str_replace('video_transaction_', '', $externalReference);
                    $type = 'video';
                } elseif (strpos($externalReference, 'gallery_transaction_') === 0) {
                    $transactionId = str_replace('gallery_transaction_', '', $externalReference);
                    $type = 'gallery';
                }
            }

            if (!$transactionId || !$type) {
                return redirect()->route('dashboard')->with('error', 'Payment verification failed. Please contact support.');
            }

            // Process based on content type
            if ($type === 'video') {
                return $this->processVideoPaymentSuccess($transactionId, $paymentId);
            } elseif ($type === 'gallery') {
                return $this->processGalleryPaymentSuccess($transactionId, $paymentId);
            }

        } catch (\Exception $e) {
            Log::error('Content payment success error: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Error processing payment.');
        }
    }

    /**
     * Process video payment success
     */
    private function processVideoPaymentSuccess($transactionId, $paymentId)
    {
        $transaction = Transaction::find($transactionId);
        if (!$transaction) {
            return redirect()->route('dashboard')->with('error', 'Transaction information not found.');
        }

        // Prevent duplicate processing - check if already completed
        if ($transaction->status === 'completed') {
            if (request()->expectsJson()) {
                return response()->json(['status' => 'already_processed']);
            }
            return redirect()->route('videos.ordered')->with('message', __("Thank you, you can now play the video!"));
        }

        DB::beginTransaction();
        try {
            $metadata = json_decode($transaction->metadata, true);
            $video = Video::find($metadata['video_id']);
            
            if (!$video) {
                throw new \Exception('Video not found');
            }

            // Check if video sale already exists for this user and video
            $existingVideoSale = VideoSales::where('video_id', $video->id)
                ->where('user_id', $transaction->user_id)
                ->first();

            if ($existingVideoSale) {
                // Update transaction status and return
                $transaction->update([
                    'reference_id' => $existingVideoSale->id,
                    'status' => 'completed',
                    'payment_id' => $paymentId
                ]);
                DB::commit();
                
                if (request()->expectsJson()) {
                    return response()->json(['status' => 'success']);
                }
                return redirect()->route('videos.ordered')->with('message', __("Thank you, you can now play the video!"));
            }

            $price = $transaction->amount;
            $adminCommission = $metadata['admin_commission'] ?? (($price * opt('admin_commission_videos')) / 100);

            // Create the video sale record NOW (only after payment confirmed)
            $videoSale = VideoSales::create([
                'video_id' => $video->id,
                'streamer_id' => $video->user_id,
                'user_id' => $transaction->user_id,
                'price' => $price,
                'status' => 'completed',
            ]);

            // Update main transaction with sale reference and mark as completed
            $transaction->update([
                'reference_id' => $videoSale->id,
                'status' => 'completed',
                'payment_id' => $paymentId
            ]);

            $transactionGroup = json_decode($transaction->metadata, true)['transaction_group'];

            // Create admin commission transaction
            $admin = User::where('is_supper_admin', 'yes')->first();
            if ($admin && $adminCommission > 0) {
                Transaction::create([
                    'user_id' => $admin->id,
                    'transaction_type' => 'admin_commission',
                    'reference_id' => $videoSale->id,
                    'reference_type' => VideoSales::class,
                    'amount' => $adminCommission,
                    'currency' => 'BRL',
                    'payment_method' => 'mercadopago',
                    'payment_id' => $paymentId,
                    'status' => 'completed',
                    'description' => 'Admin commission from video: ' . $video->title,
                    'metadata' => json_encode([
                        'video_id' => $video->id,
                        'video_title' => $video->title,
                        'buyer_id' => $transaction->user_id,
                        'streamer_id' => $video->user_id,
                        'commission_rate' => opt('admin_commission_videos'),
                        'original_amount' => $price,
                        'transaction_group' => $transactionGroup
                    ])
                ]);

                // Also create the commission record for backward compatibility
                $existingCommission = Commission::where('type', 'Buy Videos')
                    ->where('video_id', $video->id)
                    ->where('streamer_id', $video->user_id)
                    ->where('admin_id', $admin->id)
                    ->first();

                if (!$existingCommission) {
                    Commission::create([
                        'type' => 'Buy Videos',
                        'video_id' => $video->id,
                        'streamer_id' => $video->user_id,
                        'tokens' => $adminCommission,
                        'admin_id' => $admin->id,
                    ]);
                }
            }

            // Create streamer earning transaction
            $streamerAmount = $price - $adminCommission;
            if ($streamerAmount > 0) {
                Transaction::create([
                    'user_id' => $video->user_id,
                    'transaction_type' => 'streamer_earning',
                    'reference_id' => $videoSale->id,
                    'reference_type' => VideoSales::class,
                    'amount' => $streamerAmount,
                    'currency' => 'BRL',
                    'payment_method' => 'mercadopago',
                    'payment_id' => $paymentId,
                    'status' => 'completed',
                    'description' => 'Earning from video sale: ' . $video->title,
                    'metadata' => json_encode([
                        'video_id' => $video->id,
                        'video_title' => $video->title,
                        'buyer_id' => $transaction->user_id,
                        'original_amount' => $price,
                        'admin_commission' => $adminCommission,
                        'transaction_group' => $transactionGroup
                    ])
                ]);
            }

            DB::commit();

            // Send notification (only once)
            if ($video && $video->streamer) {
                $video->streamer->notify(new NewVideoSale($videoSale));
            }

            if (request()->expectsJson()) {
                return response()->json(['status' => 'success']);
            }
            return redirect()->route('videos.ordered')->with('message', __("Thank you, you can now play the video!"));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Video payment processing failed: ' . $e->getMessage());
            if (request()->expectsJson()) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
            }
            return redirect()->route('dashboard')->with('error', 'Error processing video purchase.');
        }
    }

    /**
     * Process gallery payment success
     */
    private function processGalleryPaymentSuccess($transactionId, $paymentId)
    {
        $transaction = Transaction::find($transactionId);
        if (!$transaction) {
            return redirect()->route('dashboard')->with('error', 'Transaction information not found.');
        }

        // Prevent duplicate processing - check if already completed
        if ($transaction->status === 'completed') {
            if (request()->expectsJson()) {
                return response()->json(['status' => 'already_processed']);
            }
            return redirect()->route('gallery.ordered')->with('message', __("Thank you, you can now play the gallery!"));
        }

        DB::beginTransaction();
        try {
            $metadata = json_decode($transaction->metadata, true);
            $gallery = Gallery::find($metadata['gallery_id']);
            
            if (!$gallery) {
                throw new \Exception('Gallery not found');
            }

            // Check if gallery sale already exists for this user and gallery
            $existingGallerySale = GallerySales::where('gallery_id', $gallery->id)
                ->where('user_id', $transaction->user_id)
                ->first();

            if ($existingGallerySale) {
                // Update transaction status and return
                $transaction->update([
                    'reference_id' => $existingGallerySale->id,
                    'status' => 'completed',
                    'payment_id' => $paymentId
                ]);
                DB::commit();
                
                if (request()->expectsJson()) {
                    return response()->json(['status' => 'success']);
                }
                return redirect()->route('gallery.ordered')->with('message', __("Thank you, you can now play the gallery!"));
            }

            $price = $transaction->amount;
            $adminCommission = $metadata['admin_commission'] ?? (($price * opt('admin_commission_photos')) / 100);

            // Create the gallery sale record NOW (only after payment confirmed)
            $gallerySale = GallerySales::create([
                'gallery_id' => $gallery->id,
                'streamer_id' => $gallery->user_id,
                'user_id' => $transaction->user_id,
                'price' => $price,
                'status' => 'completed',
            ]);

            // Update main transaction with sale reference and mark as completed
            $transaction->update([
                'reference_id' => $gallerySale->id,
                'status' => 'completed',
                'payment_id' => $paymentId
            ]);

            $transactionGroup = json_decode($transaction->metadata, true)['transaction_group'];

            // Create admin commission transaction
            $admin = User::where('is_supper_admin', 'yes')->first();
            if ($admin && $adminCommission > 0) {
                Transaction::create([
                    'user_id' => $admin->id,
                    'transaction_type' => 'admin_commission',
                    'reference_id' => $gallerySale->id,
                    'reference_type' => GallerySales::class,
                    'amount' => $adminCommission,
                    'currency' => 'BRL',
                    'payment_method' => 'mercadopago',
                    'payment_id' => $paymentId,
                    'status' => 'completed',
                    'description' => 'Admin commission from gallery: ' . $gallery->title,
                    'metadata' => json_encode([
                        'gallery_id' => $gallery->id,
                        'gallery_title' => $gallery->title,
                        'buyer_id' => $transaction->user_id,
                        'streamer_id' => $gallery->user_id,
                        'commission_rate' => opt('admin_commission_photos'),
                        'original_amount' => $price,
                        'transaction_group' => $transactionGroup
                    ])
                ]);

                // Also create the commission record for backward compatibility
                $existingCommission = Commission::where('type', 'Buy Gallery')
                    ->where('video_id', $gallery->id)
                    ->where('streamer_id', $gallery->user_id)
                    ->where('admin_id', $admin->id)
                    ->first();

                if (!$existingCommission) {
                    Commission::create([
                        'type' => 'Buy Gallery',
                        'video_id' => $gallery->id,
                        'streamer_id' => $gallery->user_id,
                        'tokens' => $adminCommission,
                        'admin_id' => $admin->id,
                    ]);
                }
            }

            // Create streamer earning transaction
            $streamerAmount = $price - $adminCommission;
            if ($streamerAmount > 0) {
                Transaction::create([
                    'user_id' => $gallery->user_id,
                    'transaction_type' => 'streamer_earning',
                    'reference_id' => $gallerySale->id,
                    'reference_type' => GallerySales::class,
                    'amount' => $streamerAmount,
                    'currency' => 'BRL',
                    'payment_method' => 'mercadopago',
                    'payment_id' => $paymentId,
                    'status' => 'completed',
                    'description' => 'Earning from gallery sale: ' . $gallery->title,
                    'metadata' => json_encode([
                        'gallery_id' => $gallery->id,
                        'gallery_title' => $gallery->title,
                        'buyer_id' => $transaction->user_id,
                        'original_amount' => $price,
                        'admin_commission' => $adminCommission,
                        'transaction_group' => $transactionGroup
                    ])
                ]);
            }

            DB::commit();

            // Send notification (only once)
            if ($gallery && $gallery->streamer) {
                $gallery->streamer->notify(new NewGallerySale($gallerySale));
            }

            if (request()->expectsJson()) {
                return response()->json(['status' => 'success']);
            }
            return redirect()->route('gallery.ordered')->with('message', __("Thank you, you can now play the gallery!"));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gallery payment processing failed: ' . $e->getMessage());
            if (request()->expectsJson()) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
            }
            return redirect()->route('dashboard')->with('error', 'Error processing gallery purchase.');
        }
    }

    /**
     * Handle failed payment
     */
    public function failure(Request $request)
    {
        return redirect()->route('dashboard')->with('error', 'Payment failed. Please try again.');
    }

    /**
     * Handle pending payment
     */
    public function pending(Request $request)
    {
        return redirect()->route('dashboard')->with('info', 'Your payment is pending. Access will be granted once payment is confirmed.');
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

                // Get external reference to find our transaction
                $externalReference = $request->input('external_reference');

                if (!$externalReference) {
                    return response()->json(['status' => 'error', 'message' => 'No external reference found'], 400);
                }

                $transactionId = null;
                $contentType = null;
                // Fix the external reference format to match what we actually send
                if (strpos($externalReference, 'video_transaction_') === 0) {
                    $transactionId = str_replace('video_transaction_', '', $externalReference);
                    $contentType = 'video';
                } elseif (strpos($externalReference, 'gallery_transaction_') === 0) {
                    $transactionId = str_replace('gallery_transaction_', '', $externalReference);
                    $contentType = 'gallery';
                }

                if (!$transactionId || !$contentType) {
                    return response()->json(['status' => 'error', 'message' => 'Invalid external reference'], 400);
                }

                // Process webhook based on content type
                if ($contentType === 'video') {
                    $this->processVideoPaymentSuccess($transactionId, $paymentId);
                } elseif ($contentType === 'gallery') {
                    $this->processGalleryPaymentSuccess($transactionId, $paymentId);
                }
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Mercado Pago content webhook error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Note: Transfer methods removed - now using marketplace_fee for automatic commission handling
     * Money goes directly to streamer minus commission via MercadoPago's marketplace functionality
     */
} 