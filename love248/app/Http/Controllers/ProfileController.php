<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Support\Facades\Redirect;
use App\Models\PendingStreamPayment;
use App\Models\SubscriptionPlanSell;
use Illuminate\Support\Facades\Auth;
use App\Models\MercadoAccount;
use App\Models\NSubscription;
use App\Models\ReportContent;
use Illuminate\Http\Request;
use App\Models\ReportStream;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use App\Models\TokenSale;
use App\Models\Payment;
use App\Models\Gallery;
use App\Models\Report;
use App\Models\Video;
use Inertia\Inertia;
use App\Models\User;
use Carbon\Carbon;
use Intervention\Image\Facades\Image;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    // auth middleware
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function generatePaymentPDF(Request $request)
    {
        $payments = Payment::where('user_id', Auth::user()->id)->get();

        $pdf = Pdf::loadView('transaction_pdf', compact('payments'));
        return $pdf->download('transaction-pdf.pdf');
        // return $pdf->stream('payments.pdf');
    }

    public function generateInvoicePDF(Request $request, $id)
    {
        $admin = User::where('is_admin', 'yes')->first();
        $email = $admin->email;

        // Use Transaction model instead of Payment
        $transaction = Transaction::find($id);

        // Check if transaction exists and has metadata
        if (!$transaction || !$transaction->metadata) {
            return redirect()->back()->with('error', __('Invoice data not available'));
        }

        $metadata = json_decode($transaction->metadata);

        // Check if JSON decode was successful
        if (!$metadata) {
            return redirect()->back()->with('error', __('Invalid invoice data format'));
        }

        // Get user information
        $user = User::find($transaction->user_id);
        if (!$user) {
            return redirect()->back()->with('error', __('User information not available'));
        }

        $invoicedate = $transaction->created_at->format('Y-m-d');

        // Create invoice data structure expected by the view
        $invoice = (object)[
            'id' => $transaction->id,
            'date_approved' => $transaction->created_at,
            'amount' => $transaction->amount,
            'currency' => $transaction->currency,
            'payment_method' => $transaction->payment_method,
            'status' => $transaction->status,
            'description' => $transaction->description,
            'meta' => $metadata,
            'transaction_amount' => number_format($transaction->amount, 2),
            'payer' => (object)[
                'email' => $user->email,
                'name' => $user->name,
                'id' => $user->id
            ],
            'additional_info' => (object)[
                'items' => [
                    (object)[
                        'title' => $transaction->description ?? 'Subscription Plan',
                        'description' => $metadata->plan_name ?? $transaction->description ?? 'Subscription Payment',
                        'quantity' => 1,
                        'unit_price' => number_format($transaction->amount, 2)
                    ]
                ]
            ]
        ];

        $pdf = Pdf::loadView('invoice_pdf', compact('transaction', 'invoice', 'invoicedate', 'email'));
        // return $pdf->download('invoice-pdf.pdf');
        return $pdf->stream('invoice.pdf');
    }

    public function userplan()
    {
        // Get the latest active subscription
        $activePlan = SubscriptionPlanSell::where('user_id', Auth::user()->id)
            ->where(function ($query) {
                $query->where('status', 'active')
                    ->orWhere('status', 'cancelled'); // Include cancelled plans that haven't expired yet
            })
            ->where('expire_date', '>=', now())
            ->orderBy('created_at', 'desc')
            ->first();

        // If no active plan, get the most recent plan regardless of status
        if (!$activePlan) {
            $activePlan = SubscriptionPlanSell::where('user_id', Auth::user()->id)
                ->orderBy('created_at', 'desc')
                ->first();
        }

        // Format plan data for the frontend
        $formattedPlan = null;

        if ($activePlan) {
            // Get transactions related to this subscription
            $transactions = Transaction::where('user_id', Auth::user()->id)
                ->where('reference_type', SubscriptionPlanSell::class)
                ->where('transaction_type', 'subscription')
                ->orderBy('created_at', 'desc')
                ->get();

            // Log transactions for debugging
            Log::info('User plan transactions', [
                'user_id' => Auth::user()->id,
                'active_plan_id' => $activePlan->id,
                'transaction_count' => $transactions->count(),
                'transactions' => $transactions->map(function ($tx) {
                    return [
                        'id' => $tx->id,
                        'status' => $tx->status,
                        'payment_id' => $tx->payment_id
                    ];
                })
            ]);

            // Format invoices/transactions for display
            $invoices = [];
            foreach ($transactions as $transaction) {
                $invoices[] = [
                    'id' => $transaction->id,
                    'date' => $transaction->created_at->format('M d, Y'),
                    'amount_formatted' => opt('payment-settings.currency_symbol') . number_format($transaction->amount, 2),
                    'status' => $transaction->status,
                    'payment_id' => $transaction->payment_id,
                    'invoice_url' => $transaction->status === 'completed' ? route('invoice.pdf', $transaction->id) : null,
                ];
            }

            // Log invoices for debugging
            Log::info('User plan invoices', [
                'user_id' => Auth::user()->id,
                'invoice_count' => count($invoices),
                'invoices' => $invoices
            ]);

            $formattedPlan = [
                'id' => $activePlan->id,
                'name' => $activePlan->subscription_plan,
                'status' => $activePlan->status,
                'price' => $activePlan->price,
                'price_formatted' => opt('payment-settings.currency_symbol') . number_format($activePlan->price, 2),
                'expire_date' => $activePlan->expire_date->format('M d, Y'),
                'next_billing_date' => $activePlan->expire_date->format('M d, Y'),
                'created_at' => $activePlan->created_at->format('M d, Y'),
                'invoices' => $invoices,
                'upgrade_info' => $activePlan->upgrade_data ? $activePlan->upgrade_data : null,
                'is_cancelled' => $activePlan->status === 'cancelled',
                'gateway' => $activePlan->gateway
            ];
        }

        return Inertia::render('Profile/MyPlan', [
            'plan' => $formattedPlan
        ]);
    }

    public function StreamRequest(Request $request)
    {
        $payments = PendingStreamPayment::where([
            'status' => 'pending',
            'streamer' => Auth::user()->id
        ])
            ->leftJoin('users', 'users.id', '=', 'pending_stream_payments.user_id')
            ->orderBy('created_at', 'desc')
            ->select('pending_stream_payments.*', 'users.username', 'users.name', 'users.email', 'users.profile_picture')
            ->paginate(12)
            ->appends($request->query());
        return Inertia::render('StreamRequest', [
            'payments' => $payments,
        ]);
    }

    public function userTransaction(Request $request)
    {
        // Get regular payments
        $payments = Payment::where('user_id', Auth::user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(12)
            ->appends($request->query());

        // Get transactions from the new Transaction model
        $transactions = \App\Models\Transaction::where('user_id', Auth::user()->id)
            ->orderBy('created_at', 'desc')
            ->take(10)  // Just get latest 10 for preview
            ->get();

        return Inertia::render('Transactions', [
            'payments' => $payments,
            'transactions' => $transactions,
        ]);
    }

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

    public function userplanremove($id)
    {
        // Check if the subscription belongs to the user
        $subscription = SubscriptionPlanSell::where('id', $id)
            ->where('user_id', Auth::id())
            ->where('status', 'active')
            ->first();

        if (!$subscription) {
            return Redirect::route('myPlan')->with('error', __('Subscription not found or already cancelled.'));
        }

        // Update the plan status
        $subscription->status = 'cancelled';
        $subscription->save();

        // Log the cancellation in transaction history
        if (class_exists('App\Models\Transaction')) {
            \App\Models\Transaction::create([
                'user_id' => Auth::id(),
                'transaction_type' => 'subscription_cancellation',
                'reference_id' => $subscription->id,
                'reference_type' => SubscriptionPlanSell::class,
                'amount' => 0,
                'currency' => opt('payment-settings.currency_code'),
                'payment_method' => 'System',
                'status' => 'completed',
                'description' => 'Subscription cancelled: ' . $subscription->subscription_plan,
                'metadata' => json_encode([
                    'plan_id' => $subscription->id,
                    'plan_name' => $subscription->subscription_plan,
                    'cancelled_at' => now()->toDateTimeString(),
                    'original_expire_date' => $subscription->expire_date
                ]),
            ]);
        }

        return Redirect::route('myPlan')->with('message', __('Your subscription has been cancelled. It will remain active until it expires on :date', ['date' => $subscription->expire_date->format('M d, Y')]));
    }

    /**
     * Display the user's profile form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function edit(Request $request)
    {
        $mercadoaccount = MercadoAccount::where('user', Auth::user()->id)->first();
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'mercadoaccount' => $mercadoaccount
        ]);
    }

    /**
     * Update the user's profile information.
     *
     * @param  \App\Http\Requests\ProfileUpdateRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProfileUpdateRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();

        // Remove profilePicture from the data to be filled
        $data = Arr::except($validated, ['profilePicture']);
        $user->fill($data);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Handle date of birth if it exists in the request
        if (isset($validated['dob'])) {
            $user->dob = $validated['dob'];
        }

        $user->save();

        // Handle profile picture upload
        $uploadedFile = $request->validated()['profilePicture'] ?? null;
        if ($uploadedFile) {
            $profilePicture = Image::make($uploadedFile);
            $picturePath = 'profilePics/' . $user->id . '-' . uniqid() . '.' . $uploadedFile->getClientOriginalExtension();

            $profilePicture->fit(80, 80, function ($constrain) {
                $constrain->upsize();
            })->save(public_path($picturePath), 100);

            $user->profile_picture = $picturePath;
            $user->save();
        }

        return Redirect::route('profile.edit')->with('message', __('Account details updated.'));
    }

    /**
     * Delete the user's account.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current-password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function toggleFollow(User $user)
    {
        if (auth()->user()->id != $user->id) {
            auth()->user()->toggleFollow($user);

            return response()->json(['result' => 'ok']);
        } else {
            return response()->json(['error' => __("You can't follow yourself")], 403);
        }
    }

    public function followings(Request $request)
    {
        $following = $request->user()->followings()->with('followable')->get();

        return Inertia::render('Profile/Following', compact('following'));
    }

    public function myTokens()
    {
        $orders = TokenSale::where('user_id', auth()->user()->id)
            ->where('status', 'paid')
            ->paginate(10);

        return Inertia::render('Profile/TokenOrders', compact('orders'));
    }

    public function userTransactions(Request $request)
    {
        $transactions = Transaction::where('user_id', Auth::id())
            ->when($request->filled('type'), function ($query) use ($request) {
                return $query->where('transaction_type', $request->type);
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                return $query->where('status', $request->status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $types = Transaction::where('user_id', Auth::id())
            ->distinct()
            ->pluck('transaction_type');

        $statuses = Transaction::where('user_id', Auth::id())
            ->distinct()
            ->pluck('status');

        return Inertia::render('Profile/Transactions', [
            'transactions' => $transactions,
            'filters' => [
                'type' => $request->type,
                'status' => $request->status,
            ],
            'types' => $types,
            'statuses' => $statuses,
        ]);
    }

    public function modalUserInfo(User $user, Request $r)
    {
        $membershipTier = null;
        $banned_date = null;

        $subscription = $user->subscriptions()
            ->where('streamer_id', auth()->user()->id)
            ->where('subscription_expires', '>=', now())
            ->first();

        if ($subscription) {
            $membershipTier = $subscription->created_at;
        }

        $ban = $user->bannedFromRooms()->where('streamer_id', auth()->user()->id)->first();
        if ($ban) {
            $banned_date = $ban->created_at->format('Y-m-d H:i:s');
        }


        return [
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'profile_picture' => $user->profile_picture,
            'channel_follower' => $user->isFollowing(auth()->user()),
            'channel_member' => $user->hasSubscriptionTo(auth()->user()),
            'membership_tier' => $membershipTier,
            'is_admin' => $user->is_admin === 'yes',
            'is_user_banned' => $user->bannedFromRooms()->where('streamer_id', auth()->user()->id)->exists(),
            'banned_date' => $banned_date
        ];
    }

    public function reportUser(Request $request)
    {
        $request->validate([
            'reason' => 'required|string',
            'email' => 'required|string|email|max:255',
        ]);

        $user = User::find($request->id);
        Report::create([
            'reason' => $request->reason,
            'email' => $request->email,
            'user_id' => $request->id
        ]);

        return redirect(route('channel', ['user' => $user->username]))->with('message', __('Report this user successfully!'));
    }

    public function reportContent(Request $request)
    {
        $request->validate([
            'reason' => 'required|string',
            'email' => 'required|string|email|max:255',
        ]);

        if ($request->item === 'video') {
            $video = Video::find($request->item_id);
            ReportContent::create([
                'item' => $request->item,
                'email' => $request->email,
                'reason' => $request->reason,
                'user_id' => $video->user_id,
                'item_id' => $request->item_id,
            ]);
        }

        if ($request->item === 'gallery') {
            $gallery = Gallery::find($request->item_id);
            ReportContent::create([
                'item' => $request->item,
                'email' => $request->email,
                'reason' => $request->reason,
                'user_id' => $gallery->user_id,
                'item_id' => $request->item_id,
            ]);
        }

        return redirect()->back()->with('message', __('Report this content successfully!'));
    }

    public function reportStream(Request $request)
    {
        $request->validate([
            'reason' => 'required|string',
            'email' => 'required|string|email|max:255',
        ]);

        ReportStream::create([
            'item' => 'stream',
            'email' => $request->email,
            'reason' => $request->reason,
            'user_id' => $request->id,
            'item_id' => $request->item_id,
        ]);

        // Handle JSON request vs normal form request
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Report this stream successfully!')
            ]);
        }

        return redirect()->back()->with('message', __('Report this stream successfully!'));
    }

    public function cancelPlan(Request $request)
    {
        $plan = SubscriptionPlanSell::where('user_id', Auth::user()->id)
            ->where('status', 'active')
            ->where('expire_date', '>=', now())
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$plan) {
            return redirect()->route('myPlan')->with('error', __('No active subscription found.'));
        }

        // Handle different payment gateways
        if ($plan->gateway === 'Mercado') {
            // For Mercado Pago subscriptions, use the dedicated cancellation method
            return redirect()->route('mercado.cancelSubscription');
        } else if ($plan->gateway === 'Stripe') {
            // For Stripe subscriptions, use the existing Stripe cancellation flow
            // This is handled by the StripePlanController (existing code)
            // You might want to check if you need a dedicated route for this
        }

        // Default behavior for other gateways or if specific handling isn't implemented
        // Update the plan status
        $plan->status = 'cancelled';
        $plan->save();

        // Log the cancellation in transaction history
        if (class_exists('App\Models\Transaction')) {
            \App\Models\Transaction::create([
                'user_id' => Auth::user()->id,
                'transaction_type' => 'subscription_cancellation',
                'reference_id' => $plan->id,
                'reference_type' => SubscriptionPlanSell::class,
                'amount' => 0,
                'currency' => opt('payment-settings.currency_code'),
                'payment_method' => 'System',
                'status' => 'completed',
                'description' => 'Subscription cancelled: ' . $plan->subscription_plan,
                'metadata' => json_encode([
                    'plan_id' => $plan->id,
                    'plan_name' => $plan->subscription_plan,
                    'cancelled_at' => now()->toDateTimeString(),
                    'original_expire_date' => $plan->expire_date
                ]),
            ]);
        }

        return redirect()->route('myPlan')->with('message', __('Your subscription has been cancelled. It will remain active until it expires on :date', ['date' => $plan->expire_date->format('M d, Y')]));
    }
}
