<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ app_name() }}</title>

    <meta name="app_name" content="{{ app_name() }}">

    @include('components.partials.head.head')
    <!-- Scripts -->
    @vite(['resources/css/app.scss', 'public/dashboard/scss/streamit.scss', 'public/dashboard/scss/dashboard-custom.scss', 'public/dashboard/scss/dark.scss', 'public/dashboard/scss/customizer.scss', 'public/dashboard/scss/pro.scss', 'public/dashboard/scss/rtl.scss', 'public/dashboard/scss/custom.scss', 'resources/js/app.js'])

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;1,100;1,300&display=swap"
        rel="stylesheet">
</head>

<body class="dark {{ isset($bodyClass) ? $bodyClass : '' }}">
    <!-- loader Start -->
    <div id="loading">
        @include('components.partials.loader')
    </div>
    @include('components.partials.sidebar')
    <main class="main-content">
        <div class="position-relative {{ isset($isBanner) && $isBanner ? 'iq-banner' : '' }}">
            @include('components.partials.header')
            @if (isset($isBanner) && $isBanner)
                @include('components.partials.sub-header')
            @endif
        </div>
        <div class="content-inner container-fluid pb-0" id="page_layout">
            @yield('content')
        </div>
        @include('components.partials.footer')
    </main>
    <!-- loader END -->

    @include('components.partials.customizer')

    @include('components.partials.scripts.plugin')
    @include('components.partials.scripts.script')
</body>

</html>
