<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- SEO --}}
        <meta name="description" content="Face Finder is a tool for accurate face recognition and image analysis, designed to help you manage and organize your album of photos effortlessly.">
        <meta name="keywords" content="Face Finder, Face Recognition, Image Analysis, Photo Management, Facial Detection, Album Organization, AI Photo Tool, Image Processing">
        <meta name="author" content="FabNinjas Private Limited">

        <style>[x-cloak]{display:none!important}</style>

        <title>Face Finder</title>

        <link rel="icon" type="image/svg+xml" href="/favicon_ff.svg">
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

        <!-- Alpine.js Global Message Store -->
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.store('messages', {
                    success: '',
                    error: '',
                    info: '',

                    showSuccess(message, duration = 5000) {
                        this.success = message;
                        this.error = '';
                        this.info = '';
                        this.scrollToTopForMessage();
                        if (duration > 0) {
                            setTimeout(() => { this.success = ''; }, duration);
                        }
                    },

                    showError(message, duration = 0) {
                        this.error = message;
                        this.success = '';
                        this.info = '';
                        this.scrollToTopForMessage();
                        if (duration > 0) {
                            setTimeout(() => { this.error = ''; }, duration);
                        }
                    },

                    showInfo(message, duration = 5000) {
                        this.info = message;
                        this.success = '';
                        this.error = '';
                        this.scrollToTopForMessage();
                        if (duration > 0) {
                            setTimeout(() => { this.info = ''; }, duration);
                        }
                    },

                    clear() {
                        this.success = '';
                        this.error = '';
                        this.info = '';
                    },

                    scrollToTopForMessage() {
                        try {
                            const main = document.getElementById('app-main-scroll');
                            if (main) {
                                main.scrollTo({
                                    top: 0,
                                    behavior: 'smooth'
                                });
                            } else {
                                window.scrollTo({
                                    top: 0,
                                    behavior: 'smooth'
                                });
                            }
                        } catch (e) {
                            window.scrollTo(0, 0);
                        }
                    }
                });

                const persistedPolling = Alpine.$persist(false).as('faceFinderUploadPolling');
                const persistedSessions = Alpine.$persist([]).as('faceFinderUploadSessions');

                Alpine.store('uploading_data', {
                    polling: persistedPolling,
                    upload_session_ids: persistedSessions
                });
            });
        </script>
    </head>
    <body class="@yield('body-class', 'bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 text-slate-900 min-h-screen flex flex-col')" data-public-page="{{ request()->is('face-finder/public/*') ? 'true' : 'false' }}" data-has-otp-session="{{ request()->is('face-finder/public/*') && request()->cookie('otp_session_token') ? 'true' : 'false' }}">
        @yield('body')

        @include('faceFinder.layout.upload-progress-panel')
        @includeWhen(!request()->routeIs('face_finder.uploader.show'), 'faceFinder.components.public-consent')
    </body>
    @yield('scripts')
</html>
