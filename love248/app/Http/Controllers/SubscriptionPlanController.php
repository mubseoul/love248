<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;
use App\Models\Tier;
use Inertia\Inertia;

class SubscriptionPlanController extends Controller
{

    public function __construct()
    {
        // $this->middleware('auth')->except(['getTokens']);
    }
    public function getSubscription()
    {
        $packs = SubscriptionPlan::orderBy('subscription_price')->get();

        return Inertia::render('Subscriptionplan/Packages', compact('packs'));
    }

    public function selectGateways(SubscriptionPlan $tokenPack)
    {
        $mercadoEnabled = opt('mercado_pago');
        $paypalEnabled = opt('paypalEnable');
        $stripeEnabled = opt('stripeEnable');
        $bankEnabled = opt('bankEnable');
        $ccbillEnabled = opt('ccbillEnable');

        $paypalImg = asset('images/paypal-btn.png');
        $stripeImg = asset('images/stripe-cards.png');
        $ccbillImg = asset('images/ccbill-pay.png');
        $bankImg = asset('images/bank-transfer.png');

        return Inertia::render(
            'Subscriptionplan/Payment-Method',
            compact(
                'mercadoEnabled',
                'tokenPack',
                'paypalEnabled',
                'stripeEnabled',
                'bankEnabled',
                'ccbillEnabled',
                'paypalImg',
                'stripeImg',
                'bankImg',
                'ccbillImg',
                'bankImg'
            )
        );
    }

    public function selectGatewaysForTiers(Tier $tiers, $plan)
    {
        $mercadoEnabled = opt('mercado_pago');
        $paypalEnabled = opt('paypalEnable');
        $stripeEnabled = opt('stripeEnable');
        $bankEnabled = opt('bankEnable');
        $ccbillEnabled = opt('ccbillEnable');

        $paypalImg = asset('images/paypal-btn.png');
        $stripeImg = asset('images/stripe-cards.png');
        $ccbillImg = asset('images/ccbill-pay.png');
        $bankImg = asset('images/bank-transfer.png');

        return Inertia::render(
            'Channel/PaymentMethod',
            compact(
                'mercadoEnabled',
                'paypalEnabled',
                'stripeEnabled',
                'bankEnabled',
                'ccbillEnabled',
                'paypalImg',
                'stripeImg',
                'ccbillImg',
                'bankImg',
                'bankImg',
                'tiers',
                'plan'
            )
        );
    }
}
