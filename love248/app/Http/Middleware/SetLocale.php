<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    public function handle($request, Closure $next)
    {
        // Get language from request or session, or fallback to default
        $locale = $request->get('lang', Session::get('locale', config('app.locale')));

        // Set application locale
        App::setLocale($locale);

        // Store locale in session
        Session::put('locale', $locale);

        return $next($request);
    }
}

