<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;

class SubscriptionLevelMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $requiredLevel
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $requiredLevel = 'premium')
    {
        // Check if user is authenticated
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $user = $request->user();
        
        // Allow streamers to bypass subscription requirements
        if ($user->is_streamer === 'yes') {
            return $next($request);
        }
        
        $userLevel = $user->getSubscriptionLevel();

        // Define required subscription levels
        $levelRequirements = [
            'premium' => SubscriptionPlan::LEVEL_PREMIUM,
            'boosted' => SubscriptionPlan::LEVEL_BOOSTED,
        ];

        $minimumLevel = $levelRequirements[$requiredLevel] ?? SubscriptionPlan::LEVEL_PREMIUM;

        // Check if user meets the subscription level requirement
        if ($userLevel < $minimumLevel) {
            $levelName = match($requiredLevel) {
                'premium' => 'Premium',
                'boosted' => 'Boosted',
                default => 'Premium'
            };

            // Check if this is an API request
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => __('This feature requires a :level subscription plan.', ['level' => $levelName]),
                    'required_level' => $levelName,
                    'current_level' => $user->getSubscriptionLevelName(),
                    'redirect_url' => route('subscription.plan')
                ], 403);
            }

            // For web requests, redirect with error message
            return redirect()->route('subscription.plan')->with('error', 
                __('This feature requires a :level subscription plan. Please upgrade to access this feature.', 
                ['level' => $levelName])
            );
        }

        return $next($request);
    }
} 