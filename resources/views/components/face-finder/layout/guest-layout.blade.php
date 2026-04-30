<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <style>[x-cloak]{display:none!important}</style>

        <title>{{ config('app.name', 'Face Finder') }}</title>

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
        <nav class="relative px-6 py-4">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <a href="{{ route('face_finder') }}" class="flex items-center gap-2">
                        <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-green-500 to-emerald-600 text-white inline-flex items-center justify-center font-bold text-sm">FF</div>
                        <span class="text-xl font-bold bg-gradient-to-r from-green-600 via-emerald-600 to-teal-600 bg-clip-text text-transparent">Face Finder</span>
                    </a>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('face_finder') }}" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors">Back to Home</a>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-1 flex items-center justify-center px-6 py-12">
            <div class="w-full max-w-md">
                <!-- Logo/Brand -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center gap-3 mb-4">
                        <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 text-white inline-flex items-center justify-center font-bold text-lg">FF</div>
                        <span class="text-2xl font-bold bg-gradient-to-r from-green-600 via-emerald-600 to-teal-600 bg-clip-text text-transparent">Face Finder</span>
                    </div>
                    <p class="text-slate-600 text-sm">Welcome back! Please sign in to your account.</p>
                </div>

                <!-- Auth Card -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-lg p-8">
                    {{ $slot }}
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="px-6 py-4 border-t border-slate-200 bg-white/50 backdrop-blur-sm">
            <div class="max-w-7xl mx-auto text-center">
                <p class="text-slate-600 text-sm">© {{ date('Y') }} Face Finder. All rights reserved.</p>
            </div>
        </footer>

    </body>
</html>