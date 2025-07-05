<?php

namespace App\Http\Controllers;

use App\Models\MercadoAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MercadoAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Check if user has connected Mercado account
     */
    public function checkConnection()
    {
        $mercadoAccount = MercadoAccount::where('user', Auth::user()->id)->first();

        return response()->json([
            'connected' => $mercadoAccount !== null,
            'account' => $mercadoAccount
        ]);
    }

    /**
     * Generate OAuth URL for Mercado connection
     */
    public function generateOAuthUrl()
    {
        $client_id = opt('admin_client_id');
        $redirect_uri = env('CALL_BACK_URL') . '/mercado/account/oauth/callback';

        // Change to .com.br domain and don't URL encode the redirect URI
        $auth_url = "https://auth.mercadopago.com.br/authorization?client_id={$client_id}&response_type=code&platform_id=mp&redirect_uri={$redirect_uri}";
        return redirect()->away($auth_url);
    }

    /**
     * Handle OAuth callback from Mercado
     */
    public function handleOAuthCallback(Request $request)
    {
        try {
            $clientId = opt('admin_client_id');
            $clientSecret = opt('admin_client_secret');
            $authorizationCode = $request->code;



            if (!$authorizationCode) {
                Log::error('Mercado OAuth Error: No authorization code provided');
                return redirect()->route('profile.edit')->with('error', __("Failed to connect Mercado account. No authorization code provided."));
            }

            // Prepare the data to be sent
            $postData = [
                'client_secret' => $clientSecret,
                'client_id' => $clientId,
                'grant_type' => 'authorization_code',
                'code' => $authorizationCode,
                'redirect_uri' => env('CALL_BACK_URL') . '/mercado/account/oauth/callback',
                'test_token' => 'true'
            ];

            // Send the request to MercadoPago API
            $response = Http::asForm()->post('https://api.mercadopago.com/oauth/token', $postData);

            if (!$response->successful()) {

                return redirect()->route('profile.edit')->with('error', __("Failed to connect Mercado account. API request failed."));
            }

            $data = $response->json();

            if (!isset($data['access_token'])) {

                return redirect()->route('profile.edit')->with('error', __("Failed to connect Mercado account. No access token provided."));
            }

            // Delete existing Mercado account if exists
            $existMercado = MercadoAccount::where('user', Auth::user()->id)->first();
            if ($existMercado !== null) {
                $existMercado->delete();
            }

            // Create new Mercado account
            MercadoAccount::create([
                'user' => Auth::user()->id,
                'access_token' => $data['access_token'],
                'expires_in' => $data['expires_in'],
                'scope' => $data['scope'],
                'user_id' => $data['user_id'],
                'refresh_token' => $data['refresh_token'],
                'public_key' => $data['public_key'],
            ]);

            return redirect()->route('profile.edit')->with('message', __("Thank you, your Mercado account has been connected!"));
        } catch (\Exception $e) {
            Log::error('Mercado OAuth Error: ' . $e->getMessage());
            return redirect()->route('profile.edit')->with('error', __("Failed to connect Mercado account. Please try again."));
        }
    }

    /**
     * Disconnect Mercado account
     */
    public function disconnect()
    {
        try {
            MercadoAccount::where('user', Auth::user()->id)->delete();
            return redirect()->route('profile.edit')->with('message', __("Your Mercado account has been disconnected."));
        } catch (\Exception $e) {
            Log::error('Mercado Disconnect Error: ' . $e->getMessage());
            return redirect()->route('profile.edit')->with('error', __("Failed to disconnect Mercado account. Please try again."));
        }
    }

    /**
     * Generate a unique key for Mercado API calls
     */
    private function generateUniqueKey()
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
}
