<ul class="nav nav-tabs border-0 mb-4 p-3" id="myTab-two" role="tablist">
    <li class="nav-item">
        <a class="nav-link me-2 text-capitalize {{ request()->is('admin/configuration') ? 'active' : '' }}" href="/admin/configuration">{{__('message.general')}}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link me-2 text-capitalize {{ request()->is('admin/configuration/payment') ? 'active' : '' }}" href="/admin/configuration/payment">{{ __('message.payment_gateway') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link me-2 hover:border-slate-400 {{ request()->is('admin/configuration/streaming') ? 'active' : '' }}" href="/admin/configuration/streaming">{{ __('message.live_streaming') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link me-2 text-capitalize {{ request()->is('admin/configuration/chat') ? 'active' : '' }}" href="/admin/configuration/chat">{{ __('message.live_chat') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link me-2 text-capitalize {{ request()->is('admin/split/commisson') ? 'active' : '' }}" href="/admin/split/commisson">{{ __('message.spit_commission') }}</a>
    </li>
</ul>

<!-- <div class="bg-white rounded p-3 my-5 text-gray-200">
    <a href="/admin/configuration"
        class="text-lg mr-2 text-indigo-600 hover:text-indigo-500 font-semibold @if(isset($active) && $active == 'configuration') border-b-indigo-600 font-bold border-b-2 @endif">
        {{ __('General') }}
    </a>
    |
    <a href="/admin/configuration/payment"
        class="text-lg mx-2 text-indigo-600 hover:text-indigo-500 font-semibold @if(isset($active) && $active == 'payments') underline @endif">
        {{ __('Payment Gateways') }}
    </a>
    |
    <a href="/admin/configuration/streaming"
        class="text-lg mx-2 text-indigo-600 hover:text-indigo-500 font-semibold @if(isset($active) && $active == 'streaming') underline @endif">
        {{ __('Live Streaming') }}
    </a>
    |
    <a href="/admin/configuration/chat"
        class="text-lg ml-2 text-indigo-600 hover:text-indigo-500 font-semibold @if(isset($active) && $active == 'chat') underline @endif">
        {{ __('Live Chat') }}
    </a>
</div> -->