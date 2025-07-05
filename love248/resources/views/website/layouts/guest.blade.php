<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ app_name() }}</title>

    <meta name="setting_options" content="{{ setting('customization_json') }}">

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">


 <!-- Scripts -->
 @vite(['resources/css/app.scss', 'public/dashboard/scss/streamit.scss', 'public/dashboard/scss/dashboard-custom.scss', 'public/dashboard/scss/dark.scss', 'public/dashboard/scss/customizer.scss', 'public/dashboard/scss/pro.scss', 'public/dashboard/scss/rtl.scss', 'public/dashboard/scss/custom.scss', 'resources/js/app.js'])

</head>

<body class="card-default dark">
    <div>
        @yield('content')
    </div>
</body>

</html>
