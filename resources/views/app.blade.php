<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Tooplate">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name' , 'Online Event Ticket System') }}</title>

    <!-- Additional CSS Files -->
    <link rel="stylesheet" type="text/css" href="{{ asset('./assets/css/bootstrap.min.css') }}">

    <link rel="stylesheet" type="text/css" href="{{ asset('./assets/css/font-awesome.css') }}">

    <link rel="stylesheet" type="text/css" href="{{ asset('./assets/css/owl-carousel.css') }}">

    <link rel="stylesheet" href="{{ asset('./assets/css/tooplate-artxibition.css') }}">

    <link rel="stylesheet" href="{{ asset('./assets/css/custom.css') }}">

</head>

<body>

    @include('layouts.header')

    <body>

        @yield('content')

        @include('layouts.footer')

        <!-- jQuery -->
        <script src="{{ asset('./assets/js/jquery-2.1.0.min.js') }}"></script>

        <!-- Bootstrap -->
        <script src="{{ asset('assets/js/popper.js') }}"></script>
        <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>

        <!-- Plugins -->
        <script src="{{ asset('assets/js/scrollreveal.min.js') }}"></script>
        <script src="{{ asset('assets/js/waypoints.min.js') }}"></script>
        <script src="{{ asset('assets/js/jquery.counterup.min.js') }}"></script>
        <script src="{{ asset('assets/js/imgfix.min.js') }}"></script>
        <script src="{{ asset('assets/js/mixitup.js') }}"></script>
        <script src="{{ asset('assets/js/accordions.js') }}"></script>
        <script src="{{ asset('assets/js/owl-carousel.js') }}"></script>

        <!-- Global Init -->
        <script src="{{ asset('assets/js/custom.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/services/auth_service.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/services/home_service.js') }}"></script>
        <script src="https://js.stripe.com/v3/"></script>
        <script src="{{ asset('assets/js/sweetalert.js') }}" type="text/javascript"></script>
        <script>
            const CITY_LIST_API_URL = `{{ route('get.cities') }}`;
            const LOGIN_API_URL = `{{ route('auth.login') }}`;
            const REGISTER_API_URL = `{{ route('auth.register') }}`;
            const FILTER_EVENTS_API_URL = `{{ route('load.upcoming.events') }}`;
        </script>
        @stack('custom-scripts')
    </body>

</html>
