<!DOCTYPE html>

<html lang="{{ session()->get('locale') ?? app()->getLocale() }}" data-bs-theme="light">

<head>
    <meta charset="utf-8"/>
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"/>

    <title>@yield('title') | {{ getenv('APP_NAME') }}</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700"/>

    <script> document.documentElement.setAttribute("data-bs-theme", 'light');</script>


    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.bundle.css') }}"> {{--Bootstrap 5--}}
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/global/plugins.bundle.css') }}"> {{-- Theme css--}}
    <link href="{{ asset('assets/css/horizon.css') }}" rel="stylesheet" type="text/css">

    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script> {{--JQuery Library--}}
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script> {{--Theme Library--}}
    @vite('resources/assets/js/custom/apps/main.js')
    @yield('setup-script')
    @yield('page-style')
</head>

<body>

@yield('content')
@yield('page-script')
</body>

</html>
