@php
    $isPublicRoute = request()->is('face-finder/public/*');
@endphp

<nav class="relative px-6 py-4">
    <div class="relative max-w-7xl mx-auto flex items-center {{ $isPublicRoute ? 'justify-center' : 'justify-between' }}">
        <div class="flex items-center space-x-2">
            <a href="{{ route('face_finder') }}" class="flex items-center gap-2">
                <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-green-500 to-emerald-600 text-white inline-flex items-center justify-center font-bold text-sm">FF</div>
                <span class="text-xl font-bold bg-gradient-to-r from-green-600 via-emerald-600 to-teal-600 bg-clip-text text-transparent">Face Finder</span>
            </a>
        </div>
        @unless($isPublicRoute)
            <div class="hidden md:flex items-center absolute left-1/2 -translate-x-1/2">
                <nav class="flex items-center gap-1 rounded-full border border-slate-200/80 bg-white/70 backdrop-blur supports-[backdrop-filter]:bg-white/50 shadow-sm px-2 py-1">
                    <a href="{{ route('face_finder') }}#how-it-works" class="px-3 py-1.5 text-sm font-medium text-slate-700 hover:text-slate-900 rounded-full hover:bg-white/80 focus:outline-none transition-colors">How it works</a>
                    <span class="h-5 w-px bg-slate-200 mx-1"></span>
                    <a href="{{ route('face_finder.pricing') }}" class="px-3 py-1.5 text-sm font-medium text-slate-700 hover:text-slate-900 rounded-full hover:bg-white/80 focus:outline-none transition-colors">Pricing</a>
                    <span class="h-5 w-px bg-slate-200 mx-1"></span>
                    <a href="{{ route('face_finder') }}#about" class="px-3 py-1.5 text-sm font-medium text-slate-700 hover:text-slate-900 rounded-full hover:bg-white/80 focus:outline-none transition-colors">About</a>
                    <a href="{{ route('face_finder') }}#faqs" class="px-3 py-1.5 text-sm font-medium text-slate-700 hover:text-slate-900 rounded-full hover:bg-white/80 focus:outline-none transition-colors">FAQs</a>
                </nav>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors">Log in</a>
                <a href="{{ route('register') }}" class="px-4 py-2 rounded-xl bg-gradient-to-r from-green-600 to-emerald-600 text-white hover:from-green-700 hover:to-emerald-700 transition-colors">Sign up</a>
            </div>
        @endunless
    </div>
</nav>
