@extends('faceFinder.app')

@section('content')
    <style>
        [data-yearly="true"] .price-monthly { display: none; }
        [data-yearly="false"] .price-yearly { display: none; }
    </style>
    <div class="max-w-7xl mx-auto px-6 py-16" id="pricing" data-yearly="true">
        <div class="text-center mb-12">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[13px] border border-emerald-200">Pricing</div>
            <h1 class="mt-3 text-3xl md:text-5xl font-extrabold bg-gradient-to-r from-green-600 via-emerald-600 to-teal-600 bg-clip-text text-transparent">Simple, predictable plans</h1>
            <p class="mt-4 text-slate-600 max-w-2xl mx-auto">All plans include public link generation, unlimited albums up to your storage limit, and ZIP download for your public link viewers.</p>
        </div>

        <!-- Billing toggle -->
        <div class="text-center mb-10">
            <div class="inline-flex items-center rounded-full border border-slate-200 bg-white/70 backdrop-blur px-1 py-1 shadow-sm">
                <button id="billingMonthly" class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors text-slate-600">Monthly</button>
                <button id="billingYearly" class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors text-white bg-emerald-600">Yearly</button>
            </div>
        </div>

        <!-- Plans -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- 5 GB -->
            <div class="relative rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-slate-900">Starter</h3>
                    <div class="mt-2 text-slate-600">5 GB storage</div>
                    <div class="mt-4 flex items-end gap-1">
                        <div class="text-3xl font-extrabold text-slate-900 price-monthly">$5</div>
                        <div class="text-3xl font-extrabold text-slate-900 price-yearly">$50</div>
                        <span class="text-slate-500 mb-1 price-monthly">/mo</span>
                        <span class="text-slate-500 mb-1 price-yearly">/yr</span>
                    </div>
                    <ul class="mt-6 space-y-3 text-sm text-slate-700">
                        <li class="flex items-start gap-2"><span class="text-emerald-600">✓</span> Public link generation</li>
                        <li class="flex items-start gap-2"><span class="text-emerald-600">✓</span> Unlimited albums up to 5 GB</li>
                        <li class="flex items-start gap-2"><span class="text-emerald-600">✓</span> ZIP download for public link users</li>
                    </ul>
                </div>
            </div>

            <!-- 10 GB (featured) -->
            <div class="relative rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-slate-900">Pro</h3>
                    <div class="mt-2 text-slate-600">10 GB storage</div>
                    <div class="mt-4 flex items-end gap-1">
                        <div class="text-3xl font-extrabold text-slate-900 price-monthly">$10</div>
                        <div class="text-3xl font-extrabold text-slate-900 price-yearly">$100</div>
                        <span class="text-slate-500 mb-1 price-monthly">/mo</span>
                        <span class="text-slate-500 mb-1 price-yearly">/yr</span>
                    </div>
                    <ul class="mt-6 space-y-3 text-sm text-slate-700">
                        <li class="flex items-start gap-2"><span class="text-emerald-600">✓</span> Public link generation</li>
                        <li class="flex items-start gap-2"><span class="text-emerald-600">✓</span> Unlimited albums up to 10 GB</li>
                        <li class="flex items-start gap-2"><span class="text-emerald-600">✓</span> ZIP download for public link users</li>
                    </ul>
                </div>
            </div>

            <!-- 20 GB -->
            <div class="relative rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-slate-900">Business</h3>
                    <div class="mt-2 text-slate-600">20 GB storage</div>
                    <div class="mt-4 flex items-end gap-1">
                        <div class="text-3xl font-extrabold text-slate-900 price-monthly">$20</div>
                        <div class="text-3xl font-extrabold text-slate-900 price-yearly">$200</div>
                        <span class="text-slate-500 mb-1 price-monthly">/mo</span>
                        <span class="text-slate-500 mb-1 price-yearly">/yr</span>
                    </div>
                    <ul class="mt-6 space-y-3 text-sm text-slate-700">
                        <li class="flex items-start gap-2"><span class="text-emerald-600">✓</span> Public link generation</li>
                        <li class="flex items-start gap-2"><span class="text-emerald-600">✓</span> Unlimited albums up to 20 GB</li>
                        <li class="flex items-start gap-2"><span class="text-emerald-600">✓</span> ZIP download for public link users</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Signup CTA -->
        <div class="mt-12 text-center">
            <div class="inline-flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3">
                <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl bg-gradient-to-r from-green-600 to-emerald-600 text-white hover:from-green-700 hover:to-emerald-700 text-sm">Login</a>
                <span class="text-slate-800 text-sm">Please sign in and procees with desired plan.</span>
            </div>
        </div>

    </div>

    <script>
        (function(){
            const root = document.getElementById('pricing');
            const btnMonthly = document.getElementById('billingMonthly');
            const btnYearly = document.getElementById('billingYearly');
            function setYearly(on){
                root.setAttribute('data-yearly', on ? 'true' : 'false');
                if(on){
                    btnYearly.classList.add('text-white','bg-emerald-600');
                    btnYearly.classList.remove('text-slate-600');
                    btnMonthly.classList.add('text-slate-600');
                    btnMonthly.classList.remove('text-white','bg-emerald-600');
                } else {
                    btnMonthly.classList.add('text-white','bg-emerald-600');
                    btnMonthly.classList.remove('text-slate-600');
                    btnYearly.classList.add('text-slate-600');
                    btnYearly.classList.remove('text-white','bg-emerald-600');
                }
            }
            btnMonthly.addEventListener('click', function(){ setYearly(false); });
            btnYearly.addEventListener('click', function(){ setYearly(true); });
        })();
    </script>
@endsection


