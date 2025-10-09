@extends('app')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-20">
    <div class="text-center mb-16">
        <h1 class="text-5xl md:text-7xl font-bold mb-6 bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-600 bg-clip-text text-transparent">
            LaraMind AI Workspace
        </h1>
        <p class="text-xl md:text-2xl text-slate-600 mb-8 max-w-3xl mx-auto">
            Build, analyze, and create faster with an integrated suite of AI tools—chat, summarize, and understand media. Private by default. Blazing fast.
        </p>
        <div class="flex flex-wrap justify-center gap-4">
            <div class="px-4 py-2 bg-blue-100 rounded-full text-blue-700 font-medium">
                ✨ Powered by AI
            </div>
            <div class="px-4 py-2 bg-purple-100 rounded-full text-purple-700 font-medium">
                🚀 Lightning Fast
            </div>
            <div class="px-4 py-2 bg-green-100 rounded-full text-green-700 font-medium">
                🔒 Secure & Private
            </div>
        </div>
    </div>

    <!-- AI Tools Grid -->
    <div id="tools" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-8 mb-10">
        <!-- Summary Generation Card -->
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

        <!-- Text Generation Card -->
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

        <!-- Image Analysis Card -->
        {{-- <div class="group relative bg-white/70 backdrop-blur-sm rounded-2xl p-8 border border-slate-200 hover:border-green-300 transition-all duration-300 hover:shadow-2xl hover:-translate-y-2">
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
        </div> --}}
    </div>

    <!-- About Section -->
    <section id="about" class="relative py-24">
        <div class="absolute inset-0 -z-10">
            <div class="mx-auto h-56 w-56 md:h-80 md:w-80 rounded-full bg-gradient-to-tr from-purple-200 via-blue-200 to-pink-200 blur-3xl opacity-20"></div>
        </div>

        <div class="p-10 md:p-14">
            <div class="text-center mb-10">
                <h2 class="text-4xl md:text-5xl font-extrabold mb-4">
                    <span class="bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-600 bg-clip-text text-transparent">About LaraMind</span>
                </h2>
                <p class="text-lg md:text-xl text-slate-600 max-w-3xl mx-auto leading-relaxed">
                    We craft intuitive, AI‑powered experiences that help you move faster—from idea to outcome.
                    Built for speed, privacy, and delightful results.
                </p>
            </div>

            <div class="mt-10 text-center">
                <a href="#tools" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl font-medium hover:from-blue-700 hover:to-purple-700 transition-all duration-300 shadow">
                    Explore tools
                    <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
            </div>
        </div>
    </section>
</div>
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var anchor = document.querySelector('a[href="#tools"]');
    if (!anchor) return;
    anchor.addEventListener('click', function (e) {
        var tools = document.getElementById('tools');
        if (!tools) return;
        e.preventDefault();
        tools.scrollIntoView({ behavior: 'smooth', block: 'center' });
        location.hash = 'tools';
    });
});
</script>
@endsection
@endsection