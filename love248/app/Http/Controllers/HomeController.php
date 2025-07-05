<?php

namespace App\Http\Controllers;

use Aws\Rekognition\Exception\RekognitionException;
use Aws\Rekognition\RekognitionClient;
use Illuminate\Support\Facades\Route;
use App\Models\SubscriptionPlanSell;
use Illuminate\Support\Facades\Log;
use App\Models\SubscriptionPlan;
use App\Models\NSubscription;
use Illuminate\Http\Request;
use App\Models\TagPixel;
use App\Models\Payment;
use App\Models\Video;
use Inertia\Inertia;
use App\Models\User;
use Carbon\Carbon;

class HomeController extends Controller
{
    // index
    public function index(Request $request)
    {

        $all_channels = User::isStreamer()
            ->with(['categories', 'latestVideo'])
            ->withCount(['followers', 'subscribers', 'videos'])
            ->orderByDesc('popularity')
            ->take(6)
            ->get();
        if (is_null($request->query('filter'))) {
            $channels = User::isStreamer()
                ->with(['categories', 'latestVideo'])
                ->withCount(['followers', 'subscribers', 'videos'])
                ->orderByDesc('popularity')
                ->take(6)
                ->get();
        }
        if ($request->query('filter') === 'offline') {
            $channels = User::isStreamer()
                ->where('live_status', 'offline')
                ->with(['categories', 'latestVideo'])
                ->withCount(['followers', 'subscribers', 'videos'])
                ->orderByDesc('popularity')
                ->take(6)
                ->get();
        }
        if ($request->query('filter') === 'online') {
            $channels = User::isStreamer()
                ->where('live_status', 'online')
                ->with(['categories', 'latestVideo'])
                ->withCount(['followers', 'subscribers', 'videos'])
                ->orderByDesc('popularity')
                ->take(6)
                ->get();
        }

        // latest videos - only approved (status=1)
        $videos = Video::with(['category', 'streamer'])
            ->where('status', 1)
            ->latest()
            ->take(6)
            ->get();

        $headerData =  TagPixel::where('type', 'header')->latest()->first();
        $footerData = TagPixel::where('type', 'footer')->latest()->first();

        $meta_title = opt('seo_title');
        $meta_description = opt('seo_desc');
        $meta_keys = opt('seo_keys');

        return Inertia::render('Homepage', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'channels' => $channels,
            'all_channels' => $all_channels,
            'meta_title' => $meta_title,
            'meta_description' => $meta_description,
            'meta_keys' => $meta_keys,
            'videos' => $videos,
            'headerData' => $headerData,
            'footerData' => $footerData,
        ]);
    }

    public function movieDetail($movie_id)
    {

        $video = Video::with(['category', 'streamer'])
            ->where('id', $movie_id)
            ->first();
        $videos = Video::with(['category', 'streamer'])
            ->latest()
            ->take(6)
            ->get();

        return view('website.movie', [
            'videos' => $videos,
            'video' => $video,
        ]);
    }

    public function redirectToDashboard(Request $request)
    {
        $request->session()->flash('message', __('Welcome back, :name', ['name' => $request->user()->name]));

        if ($request->user()->is_streamer == 'yes') {
            return to_route('channel', ['user' => $request->user()->username]);
        } else {
            return to_route('home');
        }
    }
    public function webhooks(Request $request)
    {
        if (json_encode($request->data['subscription_id']) !== null) {
            $nsubs = NSubscription::where('subscription_id', $request->data['subscription_id'])->first();
            if ($nsubs) {
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://api.mercadopago.com/preapproval/' . json_encode($request->data['subscription_id']),
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
                        'Authorization: Bearer ' . opt('MERCADO_SECRET_KEY')
                    ),
                ));

                $response = curl_exec($curl);
                curl_close($curl);
                $data = json_decode($response);
                $sale = SubscriptionPlanSell::where('id', $nsubs->subscription_plan_sells_id)->orderBy('created_at', 'desc')->first();
                if (isset($data->id) && $data->status === 'authorized') {
                    $plan = SubscriptionPlan::where('subscription_name', $sale->subscription_plan)->first();
                    $currentDate = Carbon::now();
                    $expireDate = $currentDate->addDay($plan->days)->toDateString();
                    $sale->expire_date = $expireDate;
                    $sale->save();
                    $nsubs->expired_at = $expireDate;
                    $nsubs->save();
                    Payment::create([
                        "user_id" => $nsubs->user_id,
                        "type" => 'mercado',
                        "item_type" => 'subscription',
                        "item_id" => $sale->id,
                        "status" => $data->status,
                        "transaction_id" => $data->id,
                        'data' => json_encode($data)
                    ]);
                }
                if ($data->status === 'cancelled') {
                    $sale = SubscriptionPlanSell::where('id', $nsubs->subscription_plan_sells_id)->orderBy('created_at', 'desc')->first();
                    $sale->status = 'cancelled';
                    $sale->save();
                    $nsubs->status = 'cancelled';
                    $nsubs->save();
                }
            }
        }
        Log::info("Webhook received: " . json_encode($request->data['id']));
        Log::info("Webhook received: " . json_encode($request->all()));
        return response()->json(['message' => 'Webhook received']);
    }
}
