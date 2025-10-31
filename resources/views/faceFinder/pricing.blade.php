@extends('faceFinder.app')

@section('content')
    <div class="max-w-7xl mx-auto px-6 py-16">
        <div class="text-center mb-8">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[13px] border border-emerald-200">Pricing</div>
            <h1 class="mt-3 text-3xl md:text-5xl font-extrabold bg-gradient-to-r from-green-600 via-emerald-600 to-teal-600 bg-clip-text text-transparent">Simple, predictable plans</h1>
            <p class="mt-4 text-slate-600 max-w-2xl mx-auto">All plans include public link generation, unlimited albums up to your storage limit, and ZIP download for your public link viewers.</p>
        </div>

        @include('faceFinder.components.pricing-cards')

        @guest
            <!-- Signup CTA -->
            <div class="mt-8 text-center">
                <div class="inline-flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3">
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl bg-gradient-to-r from-green-600 to-emerald-600 text-white hover:from-green-700 hover:to-emerald-700 text-sm">Login</a>
                    <span class="text-slate-800 text-sm">Please sign in and proceed with desired plan.</span>
                </div>
            </div>
        @endguest
    </div>
@endsection


