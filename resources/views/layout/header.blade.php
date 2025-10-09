<nav class="relative z-40 px-6 py-4">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
        <div class="flex items-center space-x-2">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                @if(request()->routeIs('home'))
                    {{-- Main page: Blue-purple theme --}}
                    <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 text-white inline-flex items-center justify-center font-bold text-sm">LM</div>
                    <span class="text-xl font-bold bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-600 bg-clip-text text-transparent">LaraMind</span>
                @elseif(request()->routeIs('text_generation'))
                    {{-- Chat Assistant: Blue-indigo theme --}}
                    <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 text-white inline-flex items-center justify-center font-bold text-sm">LM</div>
                    <span class="text-xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">LaraMind</span>
                @elseif(request()->routeIs('summary_generation'))
                    {{-- Summary Generation: Indigo-violet theme --}}
                    <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-indigo-500 to-violet-600 text-white inline-flex items-center justify-center font-bold text-sm">LM</div>
                    <span class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-violet-600 bg-clip-text text-transparent">LaraMind</span>
                @elseif(request()->routeIs('media_generation'))
                    {{-- Media Analysis: Green-emerald theme --}}
                    <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-green-500 to-emerald-600 text-white inline-flex items-center justify-center font-bold text-sm">LM</div>
                    <span class="text-xl font-bold bg-gradient-to-r from-green-600 via-emerald-600 to-teal-600 bg-clip-text text-transparent">LaraMind</span>
                @else
                    {{-- Default fallback --}}
                    <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-slate-500 to-slate-600 text-white inline-flex items-center justify-center font-bold text-sm">LM</div>
                    <span class="text-xl font-bold bg-gradient-to-r from-slate-600 to-slate-700 bg-clip-text text-transparent">LaraMind</span>
                @endif
            </a>
        </div>
        @if(! request()->routeIs('home'))
        <div class="flex items-center">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9,22 9,12 15,12 15,22"/>
                </svg>
                Return
            </a>
        </div>
        @endif
    </div>
</nav>