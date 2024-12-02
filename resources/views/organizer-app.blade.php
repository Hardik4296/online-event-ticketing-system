<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Tooplate">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{config('app.name' , 'Online Event Ticket System')}}</title>

    <!-- Additional CSS Files -->
    <link rel="stylesheet" type="text/css" href="{{ asset('./assets/css/bootstrap.min.css') }}">

    <!-- DataTables CSS for Bootstrap 4 -->
    <link rel="stylesheet" href="{{ asset('./assets/css/dataTables.bootstrap4.min.css') }}">

    <link rel="stylesheet" type="text/css" href="{{ asset('./assets/css/font-awesome.css') }}">

    <link rel="stylesheet" type="text/css" href="{{ asset('./assets/css/owl-carousel.css') }}">

    <link rel="stylesheet" href="{{ asset('./assets/css/tooplate-artxibition.css') }}">

    <link rel="stylesheet" href="{{ asset('./assets/css/custom.css') }}">

    @stack('custom-style')

</head>

<body>

    @include('layouts.dashboard.header')

    <body>

        @yield('content')

        @include('layouts.dashboard.footer')

        <!-- jQuery -->
        <script src="{{ asset('./assets/js/jquery-2.1.0.min.js')}}"></script>

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
        <script src="{{ asset('assets/js/sweetalert.js') }}" type="text/javascript"></script>

        <!-- DataTables JS -->
        <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('assets/js/dataTables.bootstrap4.min.js') }}"></script>
        <script>
            const CITY_LIST_API_URL = `{{ route('get.cities') }}`;
        </script>

        @stack('custom-scripts')

    </body>

</html>
