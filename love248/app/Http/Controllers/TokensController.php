<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TokenPack;
use App\Models\Gallery;
use App\Models\Video;
use Inertia\Inertia;

class TokensController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['getTokens']);
    }
    public function getTokens()
    {
        $packs = TokenPack::orderBy('price')->get();
        return Inertia::render('Tokens/Packages', compact('packs'));
    }

    public function selectGateway(Gallery $tokenPack, Request $request)
    {
        $paypalEnabled = opt('paypalEnable');
        $stripeEnabled = opt('stripeEnable');
        $bankEnabled = opt('bankEnable');
        $ccbillEnabled = opt('ccbillEnable');
        $mercado_pago = opt('mercado_pago');
        $pagar_me = opt('pagar_me');

        $paypalImg = asset('images/paypal-btn.png');
        $stripeImg = asset('images/stripe-cards.png');
        $ccbillImg = asset('images/ccbill-pay.png');
        $bankImg = asset('images/bank-transfer.png');
        $tokenPack = Gallery::find($tokenPack->id);

        return Inertia::render(
            'Tokens/Select-Gateway',
            compact(
                'pagar_me',
                'tokenPack',
                'paypalEnabled',
                'stripeEnabled',
                'bankEnabled',
                'mercado_pago',
                'ccbillEnabled',
                'paypalImg',
                'stripeImg',
                'bankImg',
                'ccbillImg',
                'bankImg'
            )
        );
    }

    public function videoSelectGateway(Video $tokenPack, Request $request)
    {
        $paypalEnabled = opt('paypalEnable');
        $stripeEnabled = opt('stripeEnable');
        $bankEnabled = opt('bankEnable');
        $ccbillEnabled = opt('ccbillEnable');
        $mercado_pago = opt('mercado_pago');
        $pagar_me = opt('pagar_me');

        $paypalImg = asset('images/paypal-btn.png');
        $stripeImg = asset('images/stripe-cards.png');
        $ccbillImg = asset('images/ccbill-pay.png');
        $bankImg = asset('images/bank-transfer.png');
        $tokenPack = Video::find($tokenPack->id);

        return Inertia::render(
            'Videos/Select-Gateway',
            compact(
                'pagar_me',
                'tokenPack',
                'paypalEnabled',
                'stripeEnabled',
                'bankEnabled',
                'mercado_pago',
                'ccbillEnabled',
                'paypalImg',
                'stripeImg',
                'bankImg',
                'ccbillImg',
                'bankImg'
            )
        );
    }
}
