<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - Dealer's Registration - Eurofurence</title>

    <!-- Scripts -->
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
<body class="bg-light">
<div>
    @include('layouts.navigation')

    <!-- Page Content -->
    <div class="container">
        <div class="px-2">
            @yield('content')
        </div>
    </div>

    <div class="container">
        <footer class="py-3 my-4">
            <img class="d-block mx-auto" width="150px" src="{{ Vite::asset('resources/assets/dd-logo.png') }}" alt="Dealers Den Logo">
            <ul class="nav justify-content-center border-bottom pb-3 mb-3">
                <li class="nav-item"><a href="https://help.eurofurence.org/legal/imprint" class="nav-link px-2 text-muted">Imprint & Legal Notice</a></li>
                <li class="nav-item"><a href="https://help.eurofurence.org/legal/privacy" class="nav-link px-2 text-muted">Privacy Statement</a></li>
                <li class="nav-item"><a href="https://www.eurofurence.org/EF27/dealersden/" class="nav-link px-2 text-muted">Rules and Information</a></li>
            </ul>
            <p class="text-center text-muted">© Eurofurence e.V. (MIT Licensed Software)</p>
        </footer>
    </div>
</div>
</body>
</html>
