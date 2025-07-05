<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#7c3aed">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Love248">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="application-name" content="Love248">
    
    <!-- Icons -->
    <link rel="shortcut icon" type="image/png" href="{{ asset(opt('favicon', 'favicon.png')) }}" sizes="128x128" />
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/icons/icon-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('images/icons/icon-512x512.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/icons/icon-192x192.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('images/icons/icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/icons/icon-192x192.png') }}">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <title inertia>{{ opt('seo_title') }}</title>

    @if (request()->route() && request()->route()->getName() == 'home')
        <meta name="description" content="{{ opt('seo_desc') }}" />
        <meta name="keywords" content="{{ opt('seo_keys') }}" />
        {{ $headerData ?? '' }}
    @endif

    @if (request()->route() && request()->route()->getName() == 'channel')
        @php
            $streamUser = \App\Models\User::whereUsername(request()->user)->firstOrFail();
        @endphp
        <meta property="og:title"
            content="{{ __(' :channelName channel (:handle)', [
                'channelName' => $streamUser->name,
                'handle' => '@' . $streamUser->username,
            ]) }}" />
        <meta property="og:url" content="{{ route('channel', ['user' => $streamUser->username]) }}" />
        <meta property="og:image" content="{{ $streamUser->cover_picture }}" />
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
        rel="stylesheet">
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('fonts/nunito/fonts.css') }}" /> -->

            <script>
            window.PUSHER_KEY = '{{ opt('PUSHER_APP_KEY') }}';
            window.PUSHER_CLUSTER = '{{ opt('PUSHER_APP_CLUSTER', 'mt1') }}';
        </script>

    <!-- Scripts -->
    @routes
    @viteReactRefresh
    @vite(['resources/js/app.jsx', "resources/js/Pages/{$page['component']}.jsx"])
    @inertiaHead

    {!! opt('facebook') !!}
    {!! opt('google') !!}
    {!! opt('tiktok') !!}


</head>
<!-- font-sans  -->

<body class="antialiased ">
    <div class="min-h-screen flex flex-col flex-auto flex-shrink-0">
        <x-Translations />
        @inertia
        <div id="modal-root"></div>
    </div>
    {{ $footerData ?? '' }}
</body>

</html>
