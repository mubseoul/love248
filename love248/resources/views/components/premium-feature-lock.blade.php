@props(['requiredLevel' => 'premium', 'featureName' => '', 'description' => ''])

@php
    $user = auth()->user();
    $userLevel = $user?->getSubscriptionLevel() ?? 1;
    
    $requiredLevelNum = match($requiredLevel) {
        'premium' => 2,
        'boosted' => 3,
        default => 2
    };
    
    $requiredLevelName = match($requiredLevel) {
        'premium' => 'Premium',
        'boosted' => 'Boosted',
        default => 'Premium'
    };
    
    $hasAccess = $userLevel >= $requiredLevelNum;
@endphp

@if(!$hasAccess)
<div class="bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-800 dark:to-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center">
    <div class="flex flex-col items-center space-y-4">
        <div class="text-4xl">
            @if($requiredLevel === 'boosted')
                ⭐
            @else
                💎
            @endif
        </div>
        
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                {{ $featureName ? __('Unlock :feature', ['feature' => $featureName]) : __('Premium Feature') }}
            </h3>
            
            @if($description)
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    {{ $description }}
                </p>
            @endif
            
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                {{ __('This feature requires a :level subscription plan.', ['level' => $requiredLevelName]) }}
            </p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-3 items-center">
            <x-subscription-level-badge :user="$user" :showUpgrade="false" />
            
            <a href="{{ route('subscription.plan') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                {{ __('Upgrade to :level', ['level' => $requiredLevelName]) }}
            </a>
        </div>
    </div>
</div>
@endif 