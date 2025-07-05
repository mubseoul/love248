<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\PagarPixPayment;
use Illuminate\Http\Request;
use App\Models\BankCustomer;
use Illuminate\Support\Str;
use App\Models\TokenSale;
use App\Models\TokenPack;
use App\Models\Payment;
use Inertia\Inertia;
use Carbon\Carbon;
use Nette\Utils\Json;

class PagarmeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function purchaseToken(Request $request)
    {
        $customer = $this->PagarCustomer('pagarme');
        $secretKey = base64_encode('your_pagar_secret_key_here:');
        // $secretKey = base64_encode(opt('PAGAR_SECRET_KEY') . ':');
        $curl = curl_init();

        $data = [
            "customer_id" => $customer->customer,
            "items" => [
                [
                    "amount" => floatval($request->input('id')),
                    "description" => "Purchanse Token ",
                    "quantity" => 1,
                    "code" => Str::random(8),
                ]
            ],
            "payments" => [
                [
                    "Pix" => [
                        "expires_in" => 86400,
                    ],
                    "payment_method" => "pix",
                    "amount" => floatval($request->input('id')),
                ]
            ]
        ];

        // Convert data array to JSON
        $json_data = json_encode($data);

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.pagar.me/core/v5/orders',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $json_data,
            CURLOPT_HTTPHEADER => array(
                'accept: application/json',
                'authorization: Basic ' . $secretKey,
                'content-type: application/json',
            ),
        ));

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            // cURL error occurred
            $error_message = curl_error($curl);
            $error_code = curl_errno($curl);

            // Log the error
            Log::error("cURL error ({$error_code}): {$error_message}");

            // Optionally, handle the error by returning a response or throwing an exception
            return response()->json(['error' => 'API request failed', 'message' => $error_message], 500);
        }

        curl_close($curl);
        $this->savePayment(json_decode($response));
        return response()->json(['success' => true, 'message' => 'Payment processed successfully', 'payment' => json_decode($response)]);
    }

    public function savePayment($data)
    {
        if (isset($data->message)) {
            return response()->json(['success' => false, 'error' => $data]);
        }

        PagarPixPayment::create([
            "user_id" => Auth::id(),
            "payment_id" => $data->id,
            "status" => $data->status,
            "amount" => $data->amount,
        ]);
        return true;
    }

    public function PagarCustomer($type)
    {
        $user = Auth::user();
        $isCustomer = BankCustomer::where('type', $type)->where('user_id', $user->id)->first();

        if ($isCustomer) {
            return $isCustomer;
        }

        // $secretKey = base64_encode(opt('PAGAR_SECRET_KEY') . ':');
        $secretKey = base64_encode('your_pagar_secret_key_here:');
        $data = [
            "phones" => [
                "mobile_phone" => [
                    "country_code" => "55",
                    "area_code" => "21",
                    "number" => "000000000"
                ]
            ],
            "birthdate" => $user->dob,
            "name" => $user->name,
            "email" => $user->email,
            "type" => "individual",
            "gender" => "male",
            "document" => "54195071100",
            "document_type" => "CPF",
        ];

        $json_data = json_encode($data);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.pagar.me/core/v5/customers',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $json_data,
            CURLOPT_HTTPHEADER => array(
                'accept: application/json',
                'authorization: Basic ' . $secretKey,
                'content-type: application/json',
            ),
        ));
        $response = curl_exec($curl);

        curl_close($curl);
        $customer = json_decode($response);
        $newCustomer = BankCustomer::create([
            "type" => $type,
            'user_id' => Auth::id(),
            "customer" => $customer->id,
            "document" => $customer->document,
            "name" => $customer->name,
            "document_type" => $customer->document_type
        ]);

        return $newCustomer;
    }

    public function PagarCard(Request $request)
    {
        $price = $request->tokenPack;
        $publicKey = opt('PAGAR_SECRET_KEY');
        $data = [
            'publicKey' => $publicKey,
            'price' => $request->tokenPack,
        ];

        return Inertia::render('Tokens/PagarmeForm', compact('price', 'publicKey'));
    }

    public function PagarCardPaymnet(TokenPack $tokenPack, Request $request)
    {

        $customer = $this->PagarCustomer('pagarme');

        $tokenPack = TokenPack::find($request->input('id'));
        $now = Carbon::now();
        $next24Hours = $now->addDay();
        $date = $next24Hours->toDateTimeString();

        $items = [
            [
                "code" => $tokenPack->price,
                "amount" => $tokenPack->price,
                "description" => $tokenPack->name . " with " . $tokenPack->tokens,
                "quantity" => 1
            ]
        ];

        // $customerid = $this->PagarCustomer('pagarme');
        // dd($customer);
        $customer = [
            "name" => $customer->name,
            "email" => Auth::user()->email,
            "metadata" => [
                "token_id" => $tokenPack->id
            ]
        ];

        $payments = [
            [
                "payment_method" => "checkout",
                "amount" => $tokenPack->price,
                "checkout" => [
                    "customer_editable" => false,
                    "skip_checkout_success_page" => false,
                    "accepted_payment_methods" => ["credit_card", "pix", "debit_card"],
                    "success_url" => "http://localhost:8000/pagar/success",
                    "bank_transfer" => [
                        "bank" => ["237", "001", "341"]
                    ],
                    "boleto" => [
                        "bank" => "033",
                        "instructions" => "Pagar até o vencimento",
                        "due_at" => $date
                    ],
                    "credit_card" => [
                        "capture" => true,
                        "statement_descriptor" => "Desc na fatur",
                        "soft_descriptor" => "Desc na fatur",
                        "installments" => [
                            [
                                "number" => 1,
                                "total" => $tokenPack->price
                            ]
                        ]
                    ],
                    "pix" => [
                        "expires_in" => 84600
                    ],
                    "voucher" => [
                        "capture" => true,
                        "statement_descriptor" => "pagarme"
                    ],
                    "debit_card" => [
                        "authentication" => [
                            "statement_descriptor" => "Desc na fatur",
                            "type" => "threed_secure",
                            "threed_secure" => [
                                "mpi" => "acquirer",
                                "success_url" => "http://localhost:8000/pagar/success"
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $data = [
            "items" => $items,
            "customer" => $customer,
            "payments" => $payments
        ];

        // Encode data to JSON
        $secretKey = base64_encode('your_pagar_secret_key_here:');
        $json_data = json_encode($data);
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.pagar.me/core/v5/orders',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $json_data,
            CURLOPT_HTTPHEADER => [
                'accept: application/json',
                'authorization: Basic ' . $secretKey,
                'content-type: application/json',
            ],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);
        return response()->json(['success' => true, 'message' => 'Payment processed successfully', 'payment' => json_decode($response)]);
    }

    public function PagarSuccess(Request $request, TokenPack $tokenPack)
    {
        if ($request->has('order_id')) {


            $secretKey = base64_encode('your_pagar_secret_key_here:');
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://api.pagar.me/core/v5/orders/' . $request->order_id,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => [
                    'accept: application/json',
                    'authorization: Basic ' . $secretKey,
                    'content-type: application/json',
                ],
            ]);
            $response = curl_exec($curl);
            curl_close($curl);
            $data = json_decode($response);
            $token = $tokenPack->find($data->customer->metadata->token_id);
            if ($data->status == 'paid') {
                Payment::create([
                    "user_id" => Auth::id(),
                    "type" => 'pagar',
                    "status" => $data->status,
                    "transaction_id" => $data->id,
                    'data' => json_encode($data)
                ]);
            }

            TokenSale::create([
                'user_id' => Auth::id(),
                'tokens' => $token->tokens,
                'amount' => $token->price,
                'status' => $data->status,
                'gateway' => 'Pagar'
            ]);
            $request->user()->increment('tokens', $token->tokens);
            $tokens = $token->tokens;
            return Inertia::render('Tokens/MercodaSuccess', compact('tokens'));
            return true;
        }
    }

    public function PagarTransparentCheckout(Request $request, TokenPack $tokenPack)
    {
        $expireMonthYear = explode("/", $request->expiry);
        $customer = $this->PagarCustomer('pagarme');

        $data = [
            "customer" => [
                "name" => $request->name,
                "email" => Auth::user()->email,
            ],
            "customer_id" => 'cus_gDjpo54SgFv4Y5PB',
            "items" => [
                array(
                    "amount" => floatval($request->price) * 100,
                    "description" => 'Purchase token love248',
                    "quantity" => 1,
                    "code" => '1234',
                )
            ],
            "payments" => [
                [
                    "credit_card" => [
                        "card" => [
                            "number" => $request->number,
                            "holder_name" => $request->name,
                            "exp_month" => $expireMonthYear[0],
                            "exp_year" => $expireMonthYear[1],
                            "cvv" => $request->cvc,
                        ],
                        "billing_address" => [
                            "line_1" => "7221, Avenida Dra Ruth Cardoso, Pinheiros",
                            "line_2" => "Prédio",
                            "zip_code" => "05425070",
                            "city" => "São Paulo",
                            "state" => "SP",
                            "country" => "BR"
                        ],
                        "installments" => 1,
                        "statement_descriptor" => "Desc na fatur",
                    ],
                    "payment_method" => "credit_card",
                ]
            ]
        ];

        $curl = curl_init();

        $secretKey = 'c2tfdGVzdF85NjVjY2Y2NjNlOTg0NWUxYjg4ODEwNWZlMWM5YWZhNjo=';
        // $secretKey = base64_encode(opt('PAGAR_SECRET_KEY') . ':');
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.pagar.me/core/v5/orders',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'accept: application/json',
                'content-type: application/json',
                'Authorization: Basic ' . $secretKey,
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $data = json_decode($response);

        if (isset($data->message)) {
            return response()->json(['success' => false, 'error' => $data]);
        }
        if (isset($data->status) && $data->status == 'failed') {
            return response()->json(['success' => false, 'error' => $data]);
        }

        if ($data->status == 'paid') {
            Payment::create([
                "user_id" => Auth::id(),
                "type" => 'pagar',
                "status" => $data->status,
                "transaction_id" => $data->id,
                'data' => json_encode($data)
            ]);
        }

        TokenSale::create([
            'user_id' => Auth::id(),
            'tokens' => floatval($request->price),
            'amount' => $request->price,
            'status' => $data->status,
            'gateway' => 'Pagar'
        ]);
        $request->user()->increment('tokens', $request->price);
        // $tokens = $tokenPack->tokens;
        return response()->json(['success' => true, 'message' => 'Payment successfully']);
    }
}
