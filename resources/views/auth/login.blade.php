<x-face-finder.layout.guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-6">
            <x-input-label for="email" :value="__('Email Address')" class="text-sm font-semibold text-slate-700 mb-2" />
            <x-text-input id="email"
                class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition-colors"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="username"
                placeholder="Enter your email address" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-600" />
        </div>

        <!-- Password -->
        <div class="mb-6">
            <x-input-label for="password" :value="__('Password')" class="text-sm font-semibold text-slate-700 mb-2" />
            <x-text-input id="password"
                class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition-colors"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="Enter your password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-600" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between mb-6">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me"
                    type="checkbox"
                    class="rounded border-slate-300 text-emerald-600 shadow-sm focus:ring-emerald-500 focus:ring-offset-0"
                    name="remember">
                <span class="ml-2 text-sm text-slate-600">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-emerald-600 hover:text-emerald-700 font-medium transition-colors"
                   href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <!-- Login Button -->
        <button type="submit"
            class="w-full px-6 py-3 rounded-xl bg-gradient-to-r from-green-600 to-emerald-600 text-white font-semibold hover:from-green-700 hover:to-emerald-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
            {{ __('Sign In') }}
        </button>

        <!-- Or divider -->
        <div class="my-6 flex items-center">
            <div class="flex-1 h-px bg-slate-200"></div>
            <span class="px-3 text-xs uppercase tracking-wide text-slate-500">or</span>
            <div class="flex-1 h-px bg-slate-200"></div>
        </div>

        <!-- Continue with Google -->
        <a href="{{ route('google.redirect') }}"
           class="w-full inline-flex items-center justify-center gap-3 px-6 py-3 rounded-xl border border-slate-300 bg-white text-slate-800 hover:bg-slate-50 transition-all duration-200 shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" class="h-5 w-5">
                <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12   s5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C33.79,6.053,29.137,4,24,4C12.955,4,4,12.955,4,24s8.955,20,20,20   s20-8.955,20-20C44,22.659,43.861,21.35,43.611,20.083z"/>
                <path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,16.108,18.961,13,24,13c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657   C33.79,6.053,29.137,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"/>
                <path fill="#4CAF50" d="M24,44c5.066,0,9.675-1.941,13.178-5.112l-6.08-5.152C29.089,35.091,26.671,36,24,36   c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.022C9.495,39.556,16.227,44,24,44z"/>
                <path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.106,5.557   c0.001-0.001,0.002-0.001,0.003-0.002l6.08,5.152C36.961,39.243,44,34,44,24C44,22.659,43.861,21.35,43.611,20.083z"/>
            </svg>
            <span class="font-semibold">Continue with Google</span>
        </a>

        <!-- Sign Up Link -->
        <div class="mt-6 text-center">
            <p class="text-sm text-slate-600">
                Don't have an account?
                <a href="{{ route('register') }}" class="text-emerald-600 hover:text-emerald-700 font-semibold transition-colors">
                    {{ __('Create one here') }}
                </a>
            </p>
        </div>
    </form>
</x-face-finder.layout.guest-layout>