<!-- Pricing Section Component -->
<form method="POST" action="{{ route('face_finder.subscribe_plan') }}" class="mb-8 rounded-2xl border border-blue-200 bg-gradient-to-r from-blue-50 to-indigo-50 shadow-lg p-8" x-data="pricingData()" @submit="confirmSubscription($event)">
    @csrf
    <!-- Header -->
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-slate-900 mb-2">{{ userSubscriptionActivated() ? 'Upgrade Plan' : 'Choose your plan' }}</h2>
        <p class="text-slate-600 text-sm mb-5">{{ userSubscriptionActivated() ? 'Upgrade to a higher plan to unlock more features.' : 'Get the right plan for your business. Plans can be upgraded in the future.' }}</p>

        <!-- Billing Toggle -->
        <div class="flex items-center justify-center mb-2">
            <div class="inline-flex items-center gap-2 bg-white rounded-full p-1 shadow-md border border-slate-200">
                <button type="button" @click="billingCycle = 'monthly'"
                        :class="billingCycle === 'monthly' ? 'bg-gradient-to-r from-green-600 to-emerald-600 text-white shadow-md' : 'text-slate-700 hover:text-slate-900'"
                        class="px-5 py-1.5 rounded-full text-sm font-semibold transition-all">
                    Monthly
                </button>
                <button type="button" @click="billingCycle = 'yearly'"
                        :class="billingCycle === 'yearly' ? 'bg-gradient-to-r from-green-600 to-emerald-600 text-white shadow-md' : 'text-slate-700 hover:text-slate-900'"
                        class="px-5 py-1.5 rounded-full text-sm font-semibold transition-all">
                    Yearly
                </button>
            </div>
        </div>
    </div>

    <!-- Pricing Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5" x-data="{ selectedPlan: null }">
        @php
            // Derive currency symbol from currency code
            $currencySymbol = $currency === 'INR' ? '₹' : '$';
        @endphp

        <template x-for="(plan, index) in getFilteredPlans()" :key="plan.id">
        <div class="rounded-2xl bg-white border shadow-lg p-6 transition-shadow relative"
             :class="[
                 currentPlanId && plan.id == currentPlanId ? 'border-emerald-500 border-2 ring-2 ring-emerald-200 cursor-not-allowed' :
                 selectedPlan === plan.id ? 'border-green-500 border-2 ring-2 ring-green-200 cursor-pointer hover:shadow-xl' :
                 'border-slate-200 cursor-pointer hover:shadow-xl'
             ]"
             @click="!(currentPlanId && plan.id == currentPlanId) && (selectedPlan = plan.id)">

            <!-- Current Plan Badge -->
            <template x-if="currentPlanId && plan.id == currentPlanId">
                <div class="absolute -top-3 left-4 px-3 py-1 rounded-full bg-emerald-500 text-white text-xs font-semibold shadow-lg z-10">
                    Current Plan
                </div>
            </template>

            <!-- Icon in Top Right Corner -->
            <template x-if="index === 0">
                <div class="absolute top-4 right-4 h-12 w-12 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-md">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
            </template>
            <template x-if="index === 1">
                <div class="absolute top-4 right-4 h-12 w-12 rounded-full bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center shadow-md">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                    </svg>
                </div>
            </template>
            <template x-if="index === 2">
                <div class="absolute top-4 right-4 h-12 w-12 rounded-full bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center shadow-md">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </template>

            <!-- Radio Button and Title -->
            <div class="flex items-center gap-2 mb-4" :class="currentPlanId && plan.id == currentPlanId ? 'mt-2' : ''">
                <input type="radio" name="plan_id" :value="plan.id"
                       x-model="selectedPlan"
                       :id="'plan_' + plan.id"
                       :disabled="currentPlanId && plan.id == currentPlanId"
                       :class="currentPlanId && plan.id == currentPlanId ? 'h-5 w-5 text-green-600 focus:ring-green-500 border-slate-300 cursor-not-allowed opacity-60' : 'h-5 w-5 text-green-600 focus:ring-green-500 border-slate-300 cursor-pointer'"
                       required>
                <label :for="'plan_' + plan.id"
                       :class="currentPlanId && plan.id == currentPlanId ? 'text-lg font-bold text-slate-900 cursor-not-allowed' : 'text-lg font-bold text-slate-900 cursor-pointer'"
                       x-text="plan.name"></label>
            </div>
            <div class="mb-4">
                <div class="flex items-baseline gap-1">
                    <span class="text-3xl font-bold text-slate-900" x-text="currencySymbol + parseFloat(plan.price).toFixed(2)"></span>
                    <span class="text-sm text-slate-600">/ <span x-text="billingCycle === 'monthly' ? 'month' : 'year'">month</span></span>
                </div>
            </div>
            <ul class="space-y-2.5 mb-6">
                <li class="flex items-start gap-2">
                    <svg class="h-4 w-4 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-sm text-slate-700"><strong x-text="plan.storage"></strong> storage</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="h-4 w-4 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-sm text-slate-700">Public link generation</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="h-4 w-4 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-sm text-slate-700">ZIP download for viewers</span>
                </li>
            </ul>
        </div>
        </template>
    </div>

    <!-- Submit Button -->
    <div class="mt-8 text-center">
        @auth
            @if(userSubscribedButPaymentPending())
                <div class="mb-4">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 text-sm">
                        <svg class="h-5 w-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <span>Please complete your pending payment before subscribing to a new plan.</span>
                    </div>
                </div>
            @endif
            <button type="submit"
                    :disabled="paymentPending"
                    :class="paymentPending ? 'px-8 py-3 rounded-xl bg-slate-400 text-white text-base font-semibold cursor-not-allowed shadow-lg opacity-60' : 'px-8 py-3 rounded-xl bg-gradient-to-r from-green-600 to-emerald-600 text-white text-base font-semibold hover:from-green-700 hover:to-emerald-700 transition-colors shadow-lg hover:shadow-xl'">
                {{ userSubscriptionActivated() ? 'Upgrade to Selected Plan' : 'Subscribe to Selected Plan' }}
            </button>
        @else
            <a href="{{ route('login') }}" class="inline-block px-8 py-3 rounded-xl bg-gradient-to-r from-green-600 to-emerald-600 text-white text-base font-semibold hover:from-green-700 hover:to-emerald-700 transition-colors shadow-lg hover:shadow-xl">
                Sign In to Continue
            </a>
        @endauth
    </div>
</form>

{{-- <script src="https://checkout.razorpay.com/v1/checkout.js"></script> --}}
<script>
    function pricingData() {
        return {
            billingCycle: 'monthly',
            plans: @json($plans ?? []),
            currency: '{{ $currency }}',
            currentPlanId: @json($currentPlanId ?? null),
            paymentPending: @json($paymentPending ?? false),

            get currencySymbol() {
                return this.currency === 'INR' ? '₹' : '$';
            },

            getFilteredPlans() {
                // Filter plans based on billing cycle
                return this.plans.filter(plan => plan.billing_type === this.billingCycle);
            },

            confirmSubscription(event) {
                const isUpgrade = @json(userSubscriptionActivated() ? true : false);
                const message = isUpgrade
                    ? 'Are you sure you want to upgrade to the selected plan?'
                    : 'Are you sure you want to subscribe to the selected plan?';

                if (!confirm(message)) {
                    event.preventDefault();
                }
            }
        };
    }
</script>

{{-- @if (session('razorPayDeatils.subscription_id'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        try {
            var subscriptionId = @json(session('razorPayDeatils.subscription_id'));
            if (!subscriptionId) return;

            var options = {
                key: "{{ env('RAZOR_PAY_API_KEY_ID') }}",
                subscription_id: subscriptionId,
                name: 'AI Tools',
                description: 'Subscription payment',
                theme: { color: '#10b981' },
                // success handler
                handler: function (response) {
                    window.location.reload();
                },
                modal: {
                    ondismiss: function () {
                        //
                    }
                }
            };

            var rzp = new Razorpay(options);
            rzp.open();
        } catch (e) {
            console.error('Razorpay init error', e);
        }
    });
</script>
@endif --}}
