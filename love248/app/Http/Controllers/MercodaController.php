<?php

namespace App\Http\Controllers;

use App\Notifications\StreamRequestNotification;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\VideosController;
use App\Notifications\ThanksNotification;
use Illuminate\Support\Facades\Http;
use App\Notifications\NewSubscriber;
use Illuminate\Support\Facades\Auth;
use App\Models\SubscriptionPlanSell;
use App\Models\PendingStreamPayment;
use Illuminate\Support\Facades\Log;
use App\Models\MercadoPixPayment;
use App\Models\SubscriptionPlan;
use App\Models\StreamingPrice;
use App\Models\MercadoAccount;
use App\Models\NSubscription;
use App\Models\PrivateStream;
use Illuminate\Http\Request;
use App\Models\Subscription;
use Illuminate\Support\Str;
use App\Models\TokenPack;
use App\Models\TokenSale;
use App\Models\Gallery;
use App\Models\History;
use App\Models\Payment;
use App\Models\Video;
use App\Models\User;
use App\Models\Tier;
use Inertia\Inertia;
use Carbon\Carbon;

class MercodaController extends Controller
{
    protected $gallerycontroller;
    protected $videocontroller;
    public function __construct(GalleryController $gallerycontroller, VideosController $videocontroller)
    {
        $this->gallerycontroller = $gallerycontroller;
        $this->videocontroller = $videocontroller;
        $this->middleware(['auth']);
    }

    public function purchase(Gallery $tokenPack, Request $request)
    {
        try {
            $user = Auth::user();
            if (floatval($tokenPack->price) < 1) {
                $this->gallerycontroller->purchaseGalleryWithMercado($tokenPack->id, $request);
                return redirect()->route('gallery.ordered')->with('message', __("Thank you, you can now play the Gallery!"));
            }

            $mercadoaccount = MercadoAccount::where(['user' => $tokenPack->user_id])->first();
            if ($mercadoaccount === null) {
                return redirect('/browse-gallery')->with('message', __("Streamer still did not connect mercado account!"));
            }

            if (env('ENV_MOD') === 'dev') {
                $payer = [
                    "name" => 'Test',
                    "surname" => 'test',
                    "email" => 'test_user_242304508@testuser.com',
                ];
            } else {
                $payer = [
                    "name" => $user->name,
                    "surname" => $user->name,
                    "email" => $user->email,
                ];
            }

            $data = [
                "auto_return" => "approved",
                "back_urls" => [
                    "success" => "https://love248.com/mercado/success",
                    "failure" => "https://love248.com/mercado/fail"
                ],
                "expires" => false,
                "items" => [
                    [
                        "title" => "Token Purchase",
                        "description" => "Token Purchase with 3",
                        "currency_id" => "BRL",
                        "quantity" => 1,
                        "unit_price" => floatval($tokenPack->price),
                        "meta" => $tokenPack->id
                    ]
                ],
                "payer" => $payer,
                "payment_methods" => [
                    "excluded_payment_methods" => [],
                    "installments" => 1,
                    "default_installments" => 1
                ]
            ];

            $json_data = json_encode($data);

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.mercadopago.com/checkout/preferences',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $json_data,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $mercadoaccount->access_token
                ),
            ));

            $preference = curl_exec($curl);

            curl_close($curl);
            $price = floatval($tokenPack->price);
            $tokenPack = $tokenPack->id;
            $preferenceid = json_decode($preference)->id;
            $publicKey = $mercadoaccount->public_key;
            return Inertia::render('Tokens/MercodaForm', compact('price', 'preferenceid', 'publicKey', 'tokenPack'));
        } catch (\Exception $e) {
            // dd($e);
            Log::error('Unexpected Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function mercodaPayment(Gallery $tokenPack, Request $request)
    {
        try {
            $tokenPack = Gallery::find($request->id);
            $mercadoaccount = MercadoAccount::where(['user' => $tokenPack->user_id])->first();
            if ($mercadoaccount === null) {
                return redirect('/browse-gallery')->with('message', __("You can not purchase this Gallery!"));
            }
            $admin_amm = (opt('admin_commission_photos') / 100) * $request->input('transaction_amount');
            $request_body = [
                "additional_info" => [
                    "items" => [
                        [
                            "title" => "Point Mini",
                            "description" => "Point product for card payments via Bluetooth.",
                            "quantity" => 1,
                            "unit_price" => $request->input('transaction_amount')
                        ]
                    ]
                ],
                "capture" => true,
                "coupon_amount" => null,
                "description" => "Payment for product",
                "installments" => 1,
                "payer" => [
                    "entity_type" => "individual",
                    "type" => "customer",
                    "email" => $request->input('payer')['email']
                ],

                "payment_method_id" => $request->input('payment_method_id'),
                "transaction_amount" => $request->input('transaction_amount'),
                "application_fee" => $admin_amm,
            ];

            if ($request->input('payment_method_id') !== 'pix') {
                $request_body['token'] = $request->input('token');
            }

            $request_json = json_encode($request_body);
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.mercadopago.com/v1/payments',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $request_json,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'X-Idempotency-Key: ' . $this->MercadoUniqueKey(),
                    'Authorization: Bearer ' . $mercadoaccount->access_token
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            $this->savePayment('gallery', $tokenPack->id, json_decode($response));
            $gallery = $request->id;
            $this->gallerycontroller->purchaseGalleryWithMercado($gallery, $request);
            return response()->json(['success' => true, 'message' => 'Payment processed successfully', 'payment' => json_decode($response), 'redirect' => '/my-gallery']);
        } catch (\Exception $e) {
            dd($e);
            Log::error('Unexpected Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function savePayment($type, $type_id, $data)
    {
        if (!isset($data->id)) {
            dd($data);
        }

        if (isset($data->payment_method_id) !== 'pix') {
            Payment::create([
                "user_id" => Auth::id(),
                "type" => 'mercado',
                "item_type" => $type,
                "item_id" => $type_id,
                "status" => $data->status,
                "transaction_id" => $data->id,
                'data' => json_encode($data)
            ]);
        } else {
            MercadoPixPayment::create([
                "user_id" => Auth::id(),
                "payment_id" => $data->id,
                "status" => $data->status,
                "amount" => $data->transaction_amount,
            ]);
        }
        return true;
    }

    public function MercadoSuccess(Request $request)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.mercadopago.com/preapproval/' . $request->preapproval_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            // CURLOPT_POSTFIELDS => $request_json,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'X-Idempotency-Key: ' . $this->MercadoUniqueKey(),
                // 'Authorization: Bearer TEST-8554740214832400-082312-0ae735b3341a8db79a99b8c880510b0a-1957807413'
                'Authorization: Bearer ' . opt('MERCADO_SECRET_KEY')
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $data = json_decode($response);
        $plan = SubscriptionPlan::where('subscription_name', (string)$data->reason)->first();
        // dd($data);
        if (isset($data->id) && $data->status === 'authorized') {
            $currentDate = Carbon::now();
            $expireDate = $currentDate->addDay($plan->days)->toDateString();
            $sale = SubscriptionPlanSell::create([
                'user_id' => Auth::user()->id,
                'subscription_plan' => $data->reason,
                'price' => $data->auto_recurring->transaction_amount,
                'expire_date' =>  $expireDate,
                'status' => 'active',
                'gateway' => 'mercado',
            ]);
            NSubscription::create([
                'subscription_plan_sells_id' => $sale->id,
                'status' => 'active',
                'user_id' => auth()->user()->id,
                'subscription_status' => $data->status,
                'subscription_id' => $data->id,
                'expired_at' => $sale->expire_date,
            ]);
            Payment::create([
                "user_id" => auth()->user()->id,
                "type" => 'mercado',
                "item_type" => 'subscription',
                "item_id" => $plan->id,
                "status" => $data->status,
                "transaction_id" => $data->id,
                'data' => json_encode($data)
            ]);
            return redirect(route('myPlan'))->with('message', __("Subscription plan activated successfully!"));
        } else {
            History::create([
                "user_id" => Auth::id(),
                'data' => json_encode($data)
            ]);
            return redirect(route('myPlan'))->with('message', __("Something went wrong Try again!"));
        }
    }
    public function MercadoFail() {}

    public function MercadoUniqueKey()
    {
        $idempotencyKey = sprintf(
            '%s-%s-%s-%s-%s',
            Str::random(8),
            Str::random(4),
            Str::random(4),
            Str::random(4),
            Str::random(12)
        );
        return $idempotencyKey;
    }

    public function SplitPayment(Request $request)
    {
        // dd($request);
        try {
            // $clientSecret = '991uWq1NlTSbvUJ13KYss7S0Q8EtIcA6';
            // $clientId = 8554740214832400;
            $clientId = opt('admin_client_id');
            $clientSecret = opt('admin_client_secret');
            $authorizationCode = $request->code;

            // Prepare the data to be sent
            $postData = [
                'client_secret' => $clientSecret,
                'client_id' => $clientId,
                'grant_type' => 'authorization_code',
                'code' => $authorizationCode,
                'redirect_uri' => 'https://love248.com/mercado/oauth',
                'test_token' => 'true'
            ];

            // Send the request to MercadoPago API
            $response = Http::asForm()->post('https://api.mercadopago.com/oauth/token', $postData);
            $data = $response->json();
            $existMercado = MercadoAccount::where('user', Auth::user()->id)->orderBy('created_at', 'asc')->first();
            if ($existMercado !== null) {
                MercadoAccount::where('user', Auth::user()->id)->orderBy('created_at', 'asc')->first()->delete();
            }
            $mercadoacc = MercadoAccount::create([
                'user' => Auth::user()->id,
                'access_token' => $data['access_token'],
                'expires_in' => $data['expires_in'],
                'scope' => $data['scope'],
                'user_id' => $data['user_id'],
                'refresh_token' => $data['refresh_token'],
                'public_key' => $data['public_key'],
            ]);
            // dd($mercadoacc);

            // MercadoAccount::where('user', Auth::user()->id)->delete();
            return redirect()->back()->with('message', __("Thank you, your account has been connected!"));
        } catch (\Exception $e) {
            dd($e);
            Log::error('Unexpected Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function runTheOAuthurl()
    {
        // https://auth.mercadopago.com.br/authorization?client_id=8554740214832400&response_type=code&platform_id=mp&redirect_uri=https://love248.com/mercado/oauth

        $client_id = '8554740214832400';
        $redirect_uri = 'https://love248.com/mercado/oauth';
        $auth_url = "https://auth.mercadopago.com.br/authorization?client_id={$client_id}&response_type=code&platform_id=mp&redirect_uri={$redirect_uri}";

        return redirect()->away($auth_url);
    }

    // public function generateAccessToken (){
    //     $authorization_code = request('code'); // The authorization code returned from Mercado Pago

    //     $response = Http::asForm()->post('https://api.mercadopago.com/oauth/token', [
    //             'grant_type' => 'authorization_code',
    //             'client_id' => $client_id,
    //             'client_secret' => 'YOUR_CLIENT_SECRET',
    //             'code' => $authorization_code,
    //             'redirect_uri' => $redirect_uri,
    //             'code_verifier' => $code_verifier,
    //         ]);

    //     $access_token = $response->json('access_token');
    // }

    public function videoPurchase(Video $tokenPack, Request $request)
    {
        try {
            $user = Auth::user();

            if (floatval($tokenPack->price) < 1) {
                $this->gallerycontroller->purchaseGalleryWithMercado($tokenPack->id, $request);
                return redirect()->route('gallery.ordered')->with('message', __("Thank you, you can now play the Gallery!"));
            }

            $mercadoaccount = MercadoAccount::where(['user' => $tokenPack->user_id])->first();
            if ($mercadoaccount === null) {
                return redirect('/browse-gallery')->with('message', __("Streamer still did not connect mercado account!"));
            }

            if (env('ENV_MOD') === 'dev') {
                $payer = [
                    "name" => 'Test',
                    "surname" => 'test',
                    "email" => 'test_user_242304508@testuser.com',
                ];
            } else {
                $payer = [
                    "name" => $user->name,
                    "surname" => $user->name,
                    "email" => $user->email,
                ];
            }

            $data = [
                "auto_return" => "approved",
                "back_urls" => [
                    "success" => "https://love248.com/mercado/success",
                    "failure" => "https://love248.com/mercado/fail"
                ],
                "expires" => false,
                "items" => [
                    [
                        "title" => "Token Purchase",
                        "description" => "Token Purchase with 3",
                        "currency_id" => "BRL",
                        "quantity" => 1,
                        "unit_price" => floatval($tokenPack->price),
                        "meta" => $tokenPack->id
                    ]
                ],
                "payer" => $payer,
                "payment_methods" => [
                    "excluded_payment_methods" => [],
                    "installments" => 1,
                    "default_installments" => 1
                ]
            ];

            $json_data = json_encode($data);

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.mercadopago.com/checkout/preferences',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $json_data,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $mercadoaccount->access_token
                ),
            ));

            $preference = curl_exec($curl);

            curl_close($curl);
            $price = floatval($tokenPack->price);
            $tokenPack = $tokenPack->id;
            $preferenceid = json_decode($preference)->id;
            $publicKey = $mercadoaccount->public_key;
            return Inertia::render('Videos/MercadoForm', compact('price', 'preferenceid', 'publicKey', 'tokenPack'));
        } catch (\Exception $e) {
            // dd($e);
            Log::error('Unexpected Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function videoMercodaPayment(Request $request)
    {
        try {
            $tokenPack = Video::find($request->id);
            $mercadoaccount = MercadoAccount::where(['user' => $tokenPack->user_id])->first();
            if ($mercadoaccount === null) {
                return redirect('/browse-gallery')->with('message', __("You can not purchase this Gallery!"));
            }
            $admin_amm = (opt('admin_commission_videos') / 100) * $request->input('transaction_amount');
            $request_body = [
                "additional_info" => [
                    "items" => [
                        [
                            "title" => "Point Mini",
                            "description" => "Point product for card payments via Bluetooth.",
                            "quantity" => 1,
                            "unit_price" => $request->input('transaction_amount')
                        ]
                    ]
                ],
                "capture" => true,
                "coupon_amount" => null,
                "description" => "Payment for product",
                "installments" => 1,
                "payer" => [
                    "entity_type" => "individual",
                    "type" => "customer",
                    "email" => $request->input('payer')['email']
                ],

                "payment_method_id" => $request->input('payment_method_id'),
                "transaction_amount" => $request->input('transaction_amount'),
                "application_fee" => $admin_amm,
            ];

            if ($request->input('payment_method_id') !== 'pix') {
                $request_body['token'] = $request->input('token');
            }

            $request_json = json_encode($request_body);
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.mercadopago.com/v1/payments',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $request_json,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'X-Idempotency-Key: ' . $this->MercadoUniqueKey(),
                    'Authorization: Bearer ' . $mercadoaccount->access_token
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            $this->savePayment('video', $tokenPack->id, json_decode($response));
            $gallery = $request->id;
            $this->videocontroller->purchaseVideoWithMercado($gallery, $request);
            return response()->json(['success' => true, 'message' => 'Payment processed successfully', 'payment' => json_decode($response), 'redirect' => '/my-videos']);
        } catch (\Exception $e) {
            dd($e);
            Log::error('Unexpected Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function mercadoSubsPlanPurchase(SubscriptionPlan $tokPack, Request $request)
    {
        try {
            return Inertia::render('Subscriptionplan/MercadoSubscription', compact('tokPack'));
        } catch (\Exception $e) {
            Log::error('Unexpected Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function mercodaSubsPlanPayment(Request $request)
    {
        try {
            $plan = SubscriptionPlan::find($request->id);
            $request_body = [
                "additional_info" => [
                    "items" => [
                        [
                            "title" => $plan->subscription_name,
                            "description" => $plan->details,
                            "quantity" => 1,
                            "unit_price" => floatval($request->input('transaction_amount'))
                        ]
                    ]
                ],
                "capture" => true,
                "coupon_amount" => null,
                "description" => $plan->details,
                "installments" => 1,
                "payer" => [
                    "entity_type" => "individual",
                    "type" => "customer",
                    "email" => $request->input('payer')['email']
                ],

                "payment_method_id" => $request->input('payment_method_id'),
                "transaction_amount" => floatval($request->input('transaction_amount')),
            ];

            if ($request->input('payment_method_id') !== 'pix') {
                $request_body['token'] = $request->input('token');
            }

            $request_json = json_encode($request_body);
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.mercadopago.com/v1/payments',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $request_json,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'X-Idempotency-Key: ' . $this->MercadoUniqueKey(),
                    'Authorization: Bearer ' . opt('MERCADO_SECRET_KEY')
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            if (json_decode($response)->status === 'approved') {
                $currentDate = Carbon::now();
                $expireDate = $currentDate->addDay($plan->days)->toDateString();
                $this->savePayment('subscription', $plan->id, json_decode($response));
                $sale = SubscriptionPlanSell::create([
                    'user_id' => $request->user()->id,
                    'subscription_plan' => $plan->subscription_name,
                    'price' => $plan->subscription_price,
                    'expire_date' =>  $expireDate,
                    'status' => 'active',
                    'gateway' => 'mercado',
                ]);
                if ($sale) {
                    NSubscription::create([
                        'subscription_plan_sells_id' => $sale->id,
                        'status' => 'active',
                        'user_id' => $request->user()->id,
                        'expired_at' => $sale->expire_date,
                    ]);
                }

                return response()->json(['success' => true, 'message' => 'Payment processed successfully', 'payment' => json_decode($response), 'redirect' => '/profile', 'type' => 'payment']);
            } else {
                return response()->json(['success' => false, 'message' => 'Unable to process payment', 'payment' => json_decode($response)]);
            }
        } catch (\Exception $e) {
            dd($e);
            Log::error('Unexpected Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function subsMercadoPaymentdone()
    {
        return redirect('/my-plan')->with('message', __("Subscription has been activated!"));
    }

    public function mercadoPurchasePrivateStream(Request $request)
    {
        try {
            if (empty($request->streamingId)) {
                return response()->json(['status' => false, 'message' => __("Please select time and tokens!")]);
            }

            $user = Auth::user();
            // Retrieve streaming data
            $streamingData = StreamingPrice::where('id', $request->streamingId)->with('getStreamerPrice')->first();
            $mercadoaccount = MercadoAccount::where(['user' => $streamingData->streamer_id])->first();
            // channel
            if (!$mercadoaccount) {
                return response()->json(['status' => false, 'message' => __("Streamer still did not added  mercado account!")]);
            }
            if ($user->id === $streamingData->streamer_id) {
                return response()->json(['status' => false, 'message' => __("Do not send private chat to yourself!")]);
            }

            $user = Auth::user();

            if (env('ENV_MOD') === 'dev') {
                $payer = [
                    "name" => 'Test',
                    "surname" => 'test',
                    "email" => 'test_user_242304508@testuser.com',
                ];
            } else {
                $payer = [
                    "name" => $user->name,
                    "surname" => $user->name,
                    "email" => $user->email,
                ];
            }

            $data = [
                "auto_return" => "approved",
                "back_urls" => [
                    "success" => "https://love248.com/mercado/success",
                    "failure" => "https://love248.com/mercado/fail"
                ],
                "expires" => false,
                "items" => [
                    [
                        "title" => 'Private Stream',
                        "description" => 'Private Stream',
                        "currency_id" => "BRL",
                        "quantity" => 1,
                        "unit_price" => floatval($streamingData->token_amount),
                        "meta" => $streamingData->id
                    ]
                ],
                "payer" => $payer,
                "payment_methods" => [
                    "excluded_payment_methods" => [],
                    "installments" => 1,
                    "default_installments" => 1
                ]
            ];

            $json_data = json_encode($data);

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.mercadopago.com/checkout/preferences',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $json_data,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $mercadoaccount->access_token
                ),
            ));

            $preference = curl_exec($curl);

            curl_close($curl);
            $streamid = $streamingData->id;
            $preferenceid = json_decode($preference)->id;
            $price = floatval($streamingData->token_amount);
            $publicKey = $mercadoaccount->public_key;

            return response()->json([
                'status' => true,
                'price' => $price,
                'streamid' => $streamid,
                'publicKey' => $publicKey,
                'preferenceid' => $preferenceid,
            ]);
        } catch (\Exception $e) {
            // dd($e);
            Log::error('Unexpected Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function mercadoPurchasePrivateStreamForm($price, $streamid, $publicKey = null, $preferenceid = null)
    {
        try {
            $tokenPack = $streamid;
            return Inertia::render('Channel/MercodaForm', compact('price', 'preferenceid', 'publicKey', 'tokenPack'));
        } catch (\Exception $e) {
            Log::error('Unexpected Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function mercodaPrvateStreamPayment(Request $request)
    {
        try {
            $streamingData = StreamingPrice::where('id', $request->id)->with('getStreamerPrice')->first();
            $suser = User::find($streamingData->streamer_id);
            $mercadoaccount = MercadoAccount::where(['user' => $streamingData->streamer_id])->first();
            $admin_amm = (opt('admin_commission_photos') / 100) * $request->input('transaction_amount');

            $user = Auth::user();
            $request_body = [
                "additional_info" => [
                    "items" => [
                        [
                            "title" => "Private Streaming",
                            "description" => "Private Streaming",
                            "quantity" => 1,
                            "unit_price" => floatval($request->input('transaction_amount'))
                        ]
                    ]
                ],
                "capture" => $suser->live_status === 'offline' ? false : true,
                "coupon_amount" => null,
                "description" => "Private Streaming",
                "installments" => 1,
                "payer" => [
                    "entity_type" => "individual",
                    "type" => "customer",
                    "email" => $request->input('payer')['email']
                ],

                "payment_method_id" => $request->input('payment_method_id'),
                "transaction_amount" => $request->input('transaction_amount'),
                // "application_fee" => $admin_amm,
            ];

            if ($request->input('payment_method_id') !== 'pix') {
                $request_body['token'] = $request->input('token');
            }

            $request_json = json_encode($request_body);
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.mercadopago.com/v1/payments',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $request_json,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'X-Idempotency-Key: ' . $this->MercadoUniqueKey(),
                    'Authorization: Bearer ' . $mercadoaccount->access_token
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            // dd(json_decode($response));
            if ($suser->live_status === 'offline') {
                Payment::create([
                    "user_id" => Auth::id(),
                    "type" => 'mercado',
                    "item_type" => 'private stream',
                    "item_id" => $streamingData->id,
                    "status" => json_decode($response)->status,
                    "transaction_id" => json_decode($response)->id,
                    'data' => $response
                ]);
                PendingStreamPayment::create([
                    "user_id" => Auth::id(),
                    "type" => 'mercado',
                    "streamer" => $streamingData->streamer_id,
                    "item_type" => 'private stream',
                    "item_id" => $streamingData->id,
                    "status" => 'pending',
                    "transaction_id" => json_decode($response)->id,
                    'data' => $response
                ]);
                $suser->notify(new StreamRequestNotification());
                return response()->json(['success' => true, 'message' => 'Private request sent successfully!', 'payment' => json_decode($response), 'redirect' => "/channel/live-stream/" . $suser->username]);
            }
            if (json_decode($response)->status === 'approved') {
                $this->savePayment('private stream', $streamingData->id, json_decode($response));
                Payment::create([
                    "user_id" => Auth::id(),
                    "type" => 'mercado',
                    "item_type" => 'private stream',
                    "item_id" => $streamingData->id,
                    "status" => json_decode($response)->status,
                    "transaction_id" => json_decode($response)->id,
                    'data' => $response
                ]);
                $privateStream = PrivateStream::create([
                    'streamer_id' => $streamingData->streamer_id,
                    'user_id' => $user->id,
                    'tokens' => $streamingData->token_amount,
                    'stream_time' => $streamingData->getStreamerPrice->streaming_time ?? '',
                    'message' => $request->message ?? '',
                ]);
                return response()->json(['success' => true, 'message' => 'Private request sent successfully!', 'payment' => json_decode($response), 'redirect' => "/channel/live-stream/" . $suser->username]);
            } else {
                return response()->json(['success' => false, 'message' => 'Unable to process payment', 'payment' => json_decode($response)]);
            }
        } catch (\Exception $e) {
            dd($e);
            Log::error('Unexpected Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function purchaseTiers(Tier $tier, Request $request)
    {
        try {

            $user = User::find($tier->user_id);

            if ($user->is_streamer !== 'yes') {
                abort(403, __("User is not a streamer"));
            } elseif ($tier->user_id !== $user->id) {
                abort(403, __("Tier is not owned by this streamer"));
            } elseif (!isset($request->plan)) {
                abort(403, __("Plan is required in order to subscribe"));
            }
            if (floatval($tier->price) < 1) {
                // $this->gallerycontroller->purchaseGalleryWithMercado($tokenPack->id, $request);
                // return redirect()->route('gallery.ordered')->with('message', __("Thank you, you can now play the Gallery!"));
            }
            $user = Auth::user();
            $mercadoaccount = MercadoAccount::where(['user' => $tier->user_id])->first();
            if ($mercadoaccount === null) {
                return redirect('/browse-gallery')->with('message', __("Streamer still did not connect mercado account!"));
            }

            if (env('ENV_MOD') === 'dev') {
                $payer = [
                    "name" => 'Test',
                    "surname" => 'test',
                    "email" => 'test_user_242304508@testuser.com',
                ];
            } else {
                $payer = [
                    "name" => $user->name,
                    "surname" => $user->name,
                    "email" => $user->email,
                ];
            }

            $data = [
                "auto_return" => "approved",
                "back_urls" => [
                    "success" => "https://love248.com/mercado/success",
                    "failure" => "https://love248.com/mercado/fail"
                ],
                "expires" => false,
                "items" => [
                    [
                        "title" => "Token Purchase",
                        "description" => "Token Purchase with 3",
                        "currency_id" => "BRL",
                        "quantity" => 1,
                        "unit_price" => floatval($tier->price),
                        "meta" => $tier->id
                    ]
                ],
                "payer" => $payer,
                "payment_methods" => [
                    "excluded_payment_methods" => [],
                    "installments" => 1,
                    "default_installments" => 1
                ]
            ];

            $json_data = json_encode($data);

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.mercadopago.com/checkout/preferences',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $json_data,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $mercadoaccount->access_token
                ),
            ));

            $preference = curl_exec($curl);

            curl_close($curl);
            $price = floatval($tier->price);
            $tier = $tier->id;
            $plan = $request->plan;
            $preferenceid = json_decode($preference)->id;
            $publicKey = $mercadoaccount->public_key;
            return Inertia::render('Channel/MercadoForm', compact('price', 'preferenceid', 'publicKey', 'tier', 'plan'));
        } catch (\Exception $e) {
            Log::error('Unexpected Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function mercodaTierPayment(Tier $tier, Request $request)
    {
        $tier = Tier::find($request->id);
        $user = User::find($tier->user_id);

        // dd($request->all());
        $plan = $request->plan;

        if (!in_array($plan, [
            'Monthly',
            '6 Months',
            'Yearly'
        ])) {
            abort(403, __("Plan not reckognized"));
        }

        // compute price for this channel & tier + plan combo
        $price = match ($plan) {
            'Monthly' => $tier->price,
            '6 Months' => $tier->six_months_price,
            'Yearly' => $tier->yearly_price,
        };

        // compute expiration
        $expiration = match ($plan) {
            'Monthly' => strtotime("+1 Month"),
            '6 Months' => strtotime("+6 Months"),
            'Yearly' => strtotime("+1 Year"),
        };

        try {
            $mercadoaccount = MercadoAccount::where(['user' => $tier->user_id])->first();
            if ($mercadoaccount === null) {
                return redirect('/browse-gallery')->with('message', __("You can not purchase this Gallery!"));
            }
            $admin_amm = (opt('admin_commission_photos') / 100) * $request->input('transaction_amount');
            $request_body = [
                "additional_info" => [
                    "items" => [
                        [
                            "title" => $tier->tier_name,
                            "description" => $tier->perks,
                            "quantity" => 1,
                            "unit_price" => $request->input('transaction_amount')
                        ]
                    ]
                ],
                "capture" => true,
                "coupon_amount" => null,
                "description" => "Payment for product",
                "installments" => 1,
                "payer" => [
                    "entity_type" => "individual",
                    "type" => "customer",
                    "email" => $request->input('payer')['email']
                ],

                "payment_method_id" => $request->input('payment_method_id'),
                "transaction_amount" => $request->input('transaction_amount'),
                "application_fee" => $admin_amm,
            ];

            if ($request->input('payment_method_id') !== 'pix') {
                $request_body['token'] = $request->input('token');
            }

            $request_json = json_encode($request_body);
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.mercadopago.com/v1/payments',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $request_json,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'X-Idempotency-Key: ' . $this->MercadoUniqueKey(),
                    'Authorization: Bearer ' . $mercadoaccount->access_token
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            $this->savePayment('tier', $tier->id, json_decode($response));
            // $tier = $request->id;
            $user = User::find($tier->user_id);
            // create subscription and subtract balance
            $subscription = new Subscription();

            $subscription->tier_id = $tier->id;
            $subscription->streamer_id = $user->id;
            $subscription->subscriber_id = $request->user()->id;
            $subscription->subscription_date = now();
            $subscription->subscription_expires = $expiration;
            $subscription->status = 'Active';
            $subscription->subscription_tokens = $price;
            $subscription->save();
            // increase popularity by 10
            $user->increment('popularity', 10);

            try {
                // notify creator
                $user->notify(new NewSubscriber($subscription));

                // notify the subscribe with thanks message if any
                if ($thanksMessage = user_meta('thanks_message', true, $user->id)) {
                    $request->user()->notify(new ThanksNotification($subscription, $thanksMessage));
                }
            } catch (\Exception $e) {
                Log::error($e->getMessage());
            }
            return response()->json(['success' => true, 'payment' => json_decode($response), 'message' => 'Payment processed successfully', 'redirect' => '/channel/' . $user->username]);
        } catch (\Exception $e) {
            dd($e);
            Log::error('Unexpected Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function CaptureStreamPayment(Request $request, $paymnetid, $id)
    {
        try {
            $pendingStream = PendingStreamPayment::find($id);
            $streamingData = StreamingPrice::where('id', $pendingStream->item_id)->with('getStreamerPrice')->first();

            $mercadoaccount = MercadoAccount::where(['user' => Auth::user()->id])->first();
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.mercadopago.com/v1/payments/' . $paymnetid,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_POSTFIELDS => '{"capture": true}',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'X-Idempotency-Key: ' . $this->MercadoUniqueKey(),
                    'Authorization: Bearer ' . $mercadoaccount->access_token
                ),
            ));

            $response = curl_exec($curl);
            if (json_decode($response)->status === 'approved') {
                $this->savePayment('private stream', $streamingData->id, json_decode($response));
                $privateStream = PrivateStream::create([
                    'streamer_id' => $streamingData->streamer_id,
                    'user_id' => $pendingStream->user_id,
                    'tokens' => $streamingData->token_amount,
                    'stream_time' => $streamingData->getStreamerPrice->streaming_time ?? '',
                    'message' => $request->message ?? '',
                ]);
                $payment = Payment::where('transaction_id', $paymnetid)->first();
                if ($payment) {
                    $payment->update([
                        "status" => json_decode($response)->status,
                    ]);
                }

                if ($pendingStream) {
                    PendingStreamPayment::destroy($id);
                }

                return redirect()->back()->with('message', 'Private stream request accepted!');
            } else {
                return redirect()->back()->with('message', 'Unable to approve the payment!');
            }
        } catch (\Exception $e) {
            dd($e);
            Log::error('Unexpected Error: ' . $e->getMessage());
            return redirect()->back()->with('message', $e->getMessage());
        }
    }

    public function rejectStreamPayment($id)
    {
        try {
            $pendingStream = PendingStreamPayment::find($id);
            if ($pendingStream) {
                PendingStreamPayment::destroy($id);
            }
            return redirect()->back()->with('message', 'Private stream request rejected!');
        } catch (\Exception $e) {
            Log::error('Unexpected Error: ' . $e->getMessage());
            return redirect()->back()->with('message', $e->getMessage());
        }
    }

    public function mercadoNewSubsPlanPurchase(Request $request)
    {
        // Log the input to debug
        Log::info('Received subscription request', [
            'plan_id' => $request->input('tokPack'),
            'email' => $request->input('email')
        ]);

        $plan = SubscriptionPlan::find($request->input('tokPack'));
        if (!$plan) {
            Log::error('Plan not found', ['tokPack' => $request->input('tokPack')]);
            return response()->json(['error' => 'Plan not found'], 404);
        }

        $request_body = [
            "reason" => $plan->subscription_name,
            "external_reference" => $plan->subscription_name,
            "payer_email" => $request->input('email'),
            "notification_url" => "https://love248.com/mercado/success",
            "auto_recurring" => [
                "frequency" => 1,
                "frequency_type" => "months",
                "transaction_amount" => floatval($plan->subscription_price),
                "currency_id" => "BRL"
            ],
            "back_url" => "https://love248.com/mercado/success",
            "status" => "pending"
        ];

        $request_json = json_encode($request_body);
        $curl = curl_init();

        // Check if MERCADO_SECRET_KEY is set
        $apiKey = opt('MERCADO_SECRET_KEY');
        if (empty($apiKey)) {
            Log::error('MERCADO_SECRET_KEY is not set');
            return response()->json(['error' => 'API key not configured'], 500);
        }

        Log::info('Making Mercado Pago API request', [
            'url' => 'https://api.mercadopago.com/preapproval',
            'request_body' => $request_body
        ]);

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.mercadopago.com/preapproval',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $request_json,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'X-Idempotency-Key: ' . $this->MercadoUniqueKey(),
                'Authorization: Bearer ' . $apiKey
            ),
            CURLOPT_VERBOSE => true,
        ));

        $response = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlErrorNumber = curl_errno($curl);
        $curlError = curl_error($curl);
        curl_close($curl);

        Log::info('Mercado Pago API response', [
            'status' => $httpStatus,
            'curl_error_number' => $curlErrorNumber,
            'curl_error' => $curlError,
            'response' => $response
        ]);

        // Check for curl errors
        if ($curlErrorNumber) {
            Log::error('cURL error', [
                'code' => $curlErrorNumber,
                'message' => $curlError
            ]);
            return response()->json(['error' => 'Connection error: ' . $curlError], 500);
        }


        $data = json_decode($response);

        // Check for API error in response
        if ($httpStatus >= 400 || isset($data->error)) {
            Log::error('Mercado Pago API error', [
                'status' => $httpStatus,
                'response' => $data,
                'error' => $data->message ?? $data->error ?? 'Unknown error'
            ]);

            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'error' => $data->message ?? $data->error ?? 'API error',
                    'details' => $data
                ], $httpStatus >= 400 ? $httpStatus : 500);
            }

            return redirect()->back()->with('message', $data->message ?? 'Unable to create subscription!');
        }



        // Check for successful response with init_point
        if (isset($data->id) && isset($data->init_point)) {
            Log::info('Successfully created subscription', [
                'id' => $data->id,
                'init_point' => $data->init_point
            ]);

            if (request()->wantsJson() || request()->ajax()) {
                return response()->json($data->init_point);
            }

            return Inertia::location($data->init_point);
        } else {
            Log::error('Unexpected API response format', [
                'response' => $data
            ]);

            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['error' => 'Invalid API response format'], 500);
            }

            return redirect()->back()->with('message', 'Unable to create subscription! Please try again.');
        }
    }
}
