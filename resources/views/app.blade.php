<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <link href="{{ asset('vendor/metrica/dist/assets/libs/mobius1-selectr/selectr.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('vendor/metrica/dist/assets/libs/huebee/huebee.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('vendor/metrica/dist/assets/libs/vanillajs-datepicker/css/datepicker.min.css') }}" rel="stylesheet" type="text/css" />

        <link href="{{ asset('vendor/metrica/dist/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('vendor/metrica/dist/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('vendor/metrica/dist/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />

        <link href="{{ asset('vendor/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('vendor/Select-1.7.0/css/select.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body data-layout="horizontal" class="dark-topbar">
        @inertia

        <script src="{{ asset('vendor/metrica/dist/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('vendor/metrica/dist/assets/libs/simplebar/simplebar.min.js') }}"></script>
        <script src="{{ asset('vendor/metrica/dist/assets/libs/feather-icons/feather.min.js') }}"></script>

        <script src="{{ asset('vendor/jquery/jquery.min.js') }}" crossorigin="anonymous" defer></script>
        <script src="{{ asset('vendor/datatables.net/js/jquery.dataTables.min.js') }}" crossorigin="anonymous" defer></script>
        <script src="{{ asset('vendor/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}" crossorigin="anonymous" defer></script>
        <script src="{{ asset('vendor/Select-1.7.0/js/dataTables.select.min.js') }}" crossorigin="anonymous" defer></script>

        <script src="{{ asset('vendor/metrica/dist/assets/libs/mobius1-selectr/selectr.min.js') }}"></script>
        <script src="{{ asset('vendor/metrica/dist/assets/libs/huebee/huebee.pkgd.min.js') }}"></script>
        <script src="{{ asset('vendor/metrica/dist/assets/libs/vanillajs-datepicker/js/datepicker-full.min.js') }}"></script>

        {{-- <script src="{{ asset('vendor/metrica/dist/assets/libs/apexcharts/apexcharts.min.js') }}"></script> --}}
        {{-- <script src="{{ asset('vendor/metrica/dist/assets/js/pages/analytics-index.init.js') }}"></script> --}}
        <!-- App js -->
        <script src="{{ asset('vendor/metrica/dist/assets/js/app.js') }}"></script>
    </body>
</html>
