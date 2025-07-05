<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset(opt('favicon', 'favicon.png')) }}" sizes="128x128" />

    <title>{{ config('app.name', '--') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Styles & Scripts -->
    <!-- <link rel="stylesheet" href="{{asset('css/style.min.css') }}" /> -->
    <link rel="stylesheet" href="{{asset('assets/css/core/libs.min.css') }}" />
    <link rel="stylesheet" href="{{asset('assets/css/streamit.min.css?v=1.0.1') }}" />
    <link rel="stylesheet" href="{{asset('assets/css/custom.min.css?v=1.0.1') }}" />
    <link rel="stylesheet" href="{{asset('assets/css/dashboard-custom.min.css?v=1.0.1') }}" />
    <link rel="stylesheet" href="{{asset('assets/css/dark.min.css?v=1.0.1') }}" />
    <link rel="stylesheet" href="{{asset('assets/css/customizer.min.css?v=1.0.1') }}" />
    <link rel="stylesheet" href="{{asset('assets/css/rtl.min.css?v=1.0.1') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;1,100;1,300&display=swap" rel="stylesheet">
</head>

<body>
    <div class="font-sans text-gray-900 antialiased">
        {{ $slot }}
    </div>
    <script type="text/javascript" src="{{asset('assets/js/core/libs.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/plugins/slider-tabs.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/vendor/lodash/lodash.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/iqonic-script/utility.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/iqonic-script/setting.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/setting-init.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/core/external.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/streamit.js?v=1.0.1')}}"></script>
</body>

</html>