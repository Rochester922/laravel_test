<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('meta_title')</title>
    @vite(['resources/sass/app.scss'])
</head>

<body>
    <div id="app-container">
        <div>
            @include('header')
            @yield('content')
            @include('footer')
        </div>
    </div>

    @vite(['resources/js/app.js'])
</body>

</html>
