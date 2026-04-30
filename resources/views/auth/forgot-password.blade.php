<x-face-finder.layout.guest-layout>
    <div class="mb-6 p-4 rounded-xl bg-blue-50 border border-blue-200">
        <div class="flex items-start gap-3">
            <div class="h-5 w-5 rounded-full bg-blue-100 text-blue-700 inline-flex items-center justify-center text-xs font-semibold">i</div>
            <p class="text-sm text-blue-800">
                {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
            </p>
        </div>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
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
                placeholder="Enter your email address" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-600" />
        </div>

        <!-- Submit Button -->
        <button type="submit"
            class="w-full px-6 py-3 rounded-xl bg-gradient-to-r from-green-600 to-emerald-600 text-white font-semibold hover:from-green-700 hover:to-emerald-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
            {{ __('Send Reset Link') }}
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