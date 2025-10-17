<nav class="relative z-40 px-6 py-4">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
        <div class="flex items-center space-x-2">
            @php($isPublicFaceFinder = request()->is('face-finder/public*'))
            <a href="{{ $isPublicFaceFinder ? route('face_finder') : (Auth::check() ? route('face_finder.upload_album') : route('face_finder')) }}" class="flex items-center gap-2">
                <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-green-500 to-emerald-600 text-white inline-flex items-center justify-center font-bold text-sm">FF</div>
                <span class="text-xl font-bold bg-gradient-to-r from-green-600 via-emerald-600 to-teal-600 bg-clip-text text-transparent">Face Finder</span>
            </a>
        </div>
        @unless($isPublicFaceFinder)
            <div class="flex items-center gap-2">
                @auth
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors">
                            {{ __('Log Out') }}
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
