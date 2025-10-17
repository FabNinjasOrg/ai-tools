<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <style>[x-cloak]{display:none!important}</style>

        <title>Face Finder</title>

        <link rel="icon" type="image/svg+xml" href="/favicon.svg">
        <link rel="alternate icon" href="/favicon.ico">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800" rel="stylesheet" />
        <link href="https://fonts.bunny.net/css?family=jetbrains-mono:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            @include('layout.inline-styles')
        @endif
    </head>
    <body class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 text-slate-900 min-h-screen flex flex-col">

        <!-- Navigation -->
        @include('faceFinder.layout.header')

        <!-- Main -->
        <main class="relative z-10 flex-1">
            @yield('content')
        </main>

        <!-- Footer -->
        @include('faceFinder.layout.footer')

    </body>
    @yield('scripts')
</html>


