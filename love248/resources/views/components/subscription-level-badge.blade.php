@props(['user' => null, 'showUpgrade' => true])

@php
    $user = $user ?? auth()->user();
    $level = $user?->getSubscriptionLevel() ?? 1;
    $levelName = $user?->getSubscriptionLevelName() ?? 'Free';
    
    $badgeColors = [
        1 => 'bg-gray-500 text-white',
        2 => 'bg-blue-500 text-white', 
        3 => 'bg-gradient-to-r from-purple-500 to-pink-500 text-white'
    ];
    
    $badgeColor = $badgeColors[$level] ?? $badgeColors[1];
@endphp

<div class="flex items-center space-x-2">
    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $badgeColor }}">
        @if($level == 3)
            ⭐ {{ $levelName }}
        @elseif($level == 2)
            💎 {{ $levelName }}
        @else
            🆓 {{ $levelName }}
        @endif
    </span>
    
    @if($showUpgrade && $level < 3)
        <a href="{{ route('subscription.plan') }}" 
           class="text-xs text-blue-500 hover:text-blue-700 underline">
            {{ __('Upgrade') }}
        </a>
    @endif
</div> 