<x-face-finder.layout.guest-layout>
    <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200">
        <div class="flex items-start gap-3">
            <div class="h-5 w-5 rounded-full bg-emerald-100 text-emerald-700 inline-flex items-center justify-center text-xs font-semibold">✓</div>
            <p class="text-sm text-emerald-800">
                {{ __('Please enter your new password below. Make sure it\'s secure and easy to remember.') }}
            </p>
        </div>
    </div>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div class="mb-6">
            <x-input-label for="email" :value="__('Email Address')" class="text-sm font-semibold text-slate-700 mb-2" />
            <x-text-input id="email"
                class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition-colors"
                type="email"
                name="email"
                :value="old('email', $request->email)"
                required
                autofocus
                autocomplete="username"
                placeholder="Enter your email address" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-600" />
        </div>

        <!-- Password -->
        <div class="mb-6">
            <x-input-label for="password" :value="__('New Password')" class="text-sm font-semibold text-slate-700 mb-2" />
            <x-text-input id="password"
                class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition-colors"
                type="password"
                name="password"
                required
                autocomplete="new-password"
                placeholder="Enter your new password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-600" />
        </div>

        <!-- Confirm Password -->
        <div class="mb-6">
            <x-input-label for="password_confirmation" :value="__('Confirm New Password')" class="text-sm font-semibold text-slate-700 mb-2" />
            <x-text-input id="password_confirmation"
                class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition-colors"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                placeholder="Confirm your new password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-sm text-red-600" />
        </div>

        <!-- Reset Button -->
        <button type="submit"
            class="w-full px-6 py-3 rounded-xl bg-gradient-to-r from-green-600 to-emerald-600 text-white font-semibold hover:from-green-700 hover:to-emerald-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
            {{ __('Reset Password') }}
        </button>

        <!-- Back to Login -->
        <div class="mt-6 text-center">
            <p class="text-sm text-slate-600">
                Remember your password?
                <a href="{{ route('login') }}" class="text-emerald-600 hover:text-emerald-700 font-semibold transition-colors">
                    {{ __('Back to sign in') }}
                </a>
            </p>
        </div>
    </form>
</x-face-finder.layout.guest-layout>