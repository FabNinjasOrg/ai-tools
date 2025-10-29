<nav class="relative px-6 py-4">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
        <div class="flex items-center space-x-2">
            @php($isPublicFaceFinder = request()->is('face-finder/public*'))
            <a href="{{ $isPublicFaceFinder ? route('face_finder') : (Auth::check() ? route('face_finder') : route('face_finder')) }}" class="flex items-center gap-2">
                <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-green-500 to-emerald-600 text-white inline-flex items-center justify-center font-bold text-sm">FF</div>
                <span class="text-xl font-bold bg-gradient-to-r from-green-600 via-emerald-600 to-teal-600 bg-clip-text text-transparent">Face Finder</span>
            </a>
        </div>
        @unless($isPublicFaceFinder)
            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ route('face_finder.profile.edit') }}" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors inline-flex items-center gap-2" title="Profile">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="8" r="4"/>
                            <path d="M6 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/>
                        </svg>
                        <span class="font-medium">{{ __('Profile') }}</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors inline-flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H9" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7V5a2 2 0 00-2-2H6a2 2 0 00-2 2v14a2 2 0 002 2h5a2 2 0 002-2v-2" />
                            </svg>
                            <span>{{ __('Log Out') }}</span>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors">Log in</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 rounded-xl bg-gradient-to-r from-green-600 to-emerald-600 text-white hover:from-green-700 hover:to-emerald-700 transition-colors">Sign up</a>
                @endauth
            </div>
        @endunless
    </div>
</nav>
