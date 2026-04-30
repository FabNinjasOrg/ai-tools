@extends('faceFinder.layout.base')

@section('body')
    <!-- Navigation -->
    @include('faceFinder.layout.header')

    <!-- Main -->
    <main class="relative z-10 flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    @include('faceFinder.layout.footer')
@endsection
