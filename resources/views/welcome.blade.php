@extends('app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/fabninjas_apps_style.css') }}">
@endpush

@section('content')

<section class="services-section" id="services">
    <div class="max-w-7xl mx-auto px-4">
        <div class="w-full">
            <div class="section-title text-center">
                <span class="services-badge">Apps</span>
                <h2 class="services-title">Fabninjas Apps</h2>
                <p class="services-subtitle">Comprehensive list of applications that helps you in every aspects</p>
            </div>
        </div>
        <!-- Services Grid -->
        <div class="services-grid mt-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <div class="service-box">
                        <h4>Face Finder</h4>
                        <p> Upload album of photos and find all images containing a specific person's photo by face-recognition.</p>
                        <ul class="service-features">
                            <li>One link sharing</li>
                            <li>Easy to use</li>
                            <li>Face-recognition support</li>
                            <li>Album management made easy</li>
                        </ul>
                        <a href="{{ route('face_finder') }}" class="service-btn">
                            Try Now
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

    {{-- <div class="max-w-7xl mx-auto px-6 py-20">
        <div id="tools" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-8 mb-10">
            <div class="group relative bg-white/70 backdrop-blur-sm rounded-2xl p-8 border border-slate-200 hover:border-purple-300 transition-all duration-300 hover:shadow-2xl hover:-translate-y-2">
                <div class="absolute inset-0 bg-gradient-to-br from-purple-500/10 to-pink-500/10 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative z-10">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-4">Summary Generation</h3>
                    <p class="text-slate-600 mb-6 leading-relaxed">
                        Create concise and accurate summaries from any content in seconds.
                    </p>
                    <a href="{{ route('summary_generation') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-xl font-medium hover:from-purple-700 hover:to-purple-800 transition-all duration-300 group-hover:shadow-lg">
                        Try Now
                        <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <div class="group relative bg-white/70 backdrop-blur-sm rounded-2xl p-8 border border-slate-200 hover:border-blue-300 transition-all duration-300 hover:shadow-2xl hover:-translate-y-2">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-purple-500/10 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative z-10">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-4">Chat Assistant</h3>
                    <p class="text-slate-600 mb-6 leading-relaxed">
                        Get the information you need with our AI-powered chat assistant.
                    </p>
                    <a href="{{ route('text_generation') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl font-medium hover:from-blue-700 hover:to-blue-800 transition-all duration-300 group-hover:shadow-lg">
                        Try now
                        <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </a>
                </div>
            </div>

            <div class="group relative bg-white/70 backdrop-blur-sm rounded-2xl p-8 border border-slate-200 hover:border-green-300 transition-all duration-300 hover:shadow-2xl hover:-translate-y-2">
                <div class="absolute inset-0 bg-gradient-to-br from-green-500/10 to-emerald-500/10 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative z-10">
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-4">Media Analysis</h3>
                    <p class="text-slate-600 mb-6 leading-relaxed">
                        Upload your media, let our vision AI analyze it, and ask questions to get insights.
                    </p>
                    <a href="{{ route('media_generation') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl font-medium hover:from-green-700 hover:to-green-800 transition-all duration-300 group-hover:shadow-lg">
                        Try now
                        <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                    </a>
                </div>
            </div>
            <div class="group relative bg-white/70 backdrop-blur-sm rounded-2xl p-8 border border-slate-200 hover:border-green-300 transition-all duration-300 hover:shadow-2xl hover:-translate-y-2">
                <div class="absolute inset-0 bg-gradient-to-br from-green-500/10 to-emerald-500/10 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative z-10">
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="10" cy="10" r="0.5" fill="currentColor"/>
                            <circle cx="14" cy="10" r="0.5" fill="currentColor"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M10 14c1 1.5 3 1.5 4 0"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h4M3 3v4M21 3h-4M21 3v4M3 21h4M3 21v-4M21 21h-4M21 21v-4"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-4">Face Finder</h3>
                    <p class="text-slate-600 mb-6 leading-relaxed">
                        Upload album of photos and find all images containing a specific person's photo.
                    </p>
                    <a href="{{ route('face_finder') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl font-medium hover:from-green-700 hover:to-green-800 transition-all duration-300 group-hover:shadow-lg">
                        Try now
                        <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                    </a>
                </div>
            </div>
        </div>
    </div> --}}
@endsection
