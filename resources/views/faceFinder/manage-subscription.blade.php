@extends('faceFinder.layout.sidebar-layout')

@section('page-title', 'Manage Subscription')

@section('content')
    <div class="mx-auto px-6">
        @php
            // Status label and color mapping
            $status = $subscriptionDetails['status'] ?? 'active';
            $statusMap = [
                'active' => ['label' => 'Active', 'color' => 'text-emerald-700'],
                'created' => ['label' => 'Pending Payment', 'color' => 'text-amber-700'],
                'cancelled' => ['label' => 'Cancelled', 'color' => 'text-rose-700'],
            ];
            $statusLabel = $statusMap[$status]['label'] ?? ucfirst($status);
            $statusColor = $statusMap[$status]['color'] ?? 'text-slate-700';
        @endphp

        <div class="flex items-start gap-4 mb-8">
            <div class="flex-1">
                <div class="rounded-2xl border border-slate-200 shadow-lg overflow-hidden bg-white">
                        <div class="bg-gradient-to-r from-green-500 via-emerald-500 to-teal-500 px-5 py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4 text-white">
                            <div>
                                <p class="uppercase tracking-wide text-xs font-semibold text-white/80">Selected Plan</p>
                                <h4 class="text-xl font-bold">{{ $planData['name'] ?? 'Selected Plan' }}</h4>
                            </div>
                            <div class="flex items-baseline gap-2">
                                <span class="text-3xl font-bold">
                                    @if ($planData['amount'] ?? null)
                                        {{ $currencySymbol }}{{ number_format($planData['amount'], 2) }}
                                    @else
                                        —
                                    @endif
                                </span>
                                <span class="text-sm font-medium text-white/80">per month</span>
                            </div>
                        </div>

                        <div class="p-6 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-4 flex items-start gap-3">
                                    <div class="flex-shrink-0 mt-1">
                                        <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-slate-500 font-semibold">Status</p>
                                        <p class="text-sm font-semibold {{ $statusColor }}">{{ $statusLabel }}</p>
                                    </div>
                                </div>
                                <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-4 flex items-start gap-3">
                                    <div class="flex-shrink-0 mt-1">
                                        <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-slate-500 font-semibold">Plan</p>
                                        <p class="text-sm font-semibold text-slate-900">{{ $planData['name'] ?? 'Selected Plan' }}</p>
                                    </div>
                                </div>
                                <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-4 flex items-start gap-3">
                                    <div class="flex-shrink-0 mt-1">
                                        <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12a9 9 0 1118 0 9 9 0 01-18 0z" /></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-slate-500 font-semibold">Storage</p>
                                        <p class="text-sm font-semibold text-slate-900">{{ $planData['storage'] ?? 'Generous' }}</p>
                                    </div>
                                </div>
                                @if (in_array($status, ['active', 'cancelled'], true))
                                <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-4 flex items-start gap-3">
                                    <div class="flex-shrink-0 mt-1">
                                        <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10m-9 8h8a2 2 0 002-2V7a2 2 0 00-2-2H9a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-slate-500 font-semibold">Start Date</p>
                                        <p class="text-sm font-semibold text-slate-900">{{ isset($subscriptionDetails['start_date']) ? $subscriptionDetails['start_date']->timezone(config('app.timezone'))->format('M j, Y') : '—' }}</p>
                                    </div>
                                </div>
                                <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-4 flex items-start gap-3">
                                    <div class="flex-shrink-0 mt-1">
                                        <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-slate-500 font-semibold">End Date</p>
                                        <p class="text-sm font-semibold text-slate-900">{{ isset($subscriptionDetails['end_date']) ? $subscriptionDetails['end_date']->timezone(config('app.timezone'))->format('M j, Y') : '—' }}</p>
                                    </div>
                                </div>
                                @endif
                            </div>

                            <div>
                                <h5 class="text-sm font-semibold text-slate-600 uppercase tracking-wide mb-3">Benefits Included</h5>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-sm">
                                    <div class="flex items-start gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                                        <div class="flex-shrink-0 mt-1">
                                            <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-900">Priority Uploads</p>
                                            <p class="text-xs text-slate-500 mt-1">Faster processing for your albums</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                                        <div class="flex-shrink-0 mt-1">
                                            <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-900">Public link generation</p>
                                            <p class="text-xs text-slate-500 mt-1">Share albums securely with your team</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                                        <div class="flex-shrink-0 mt-1">
                                            <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-900">ZIP download for viewers</p>
                                            <p class="text-xs text-slate-500 mt-1">Let collaborators download matched photos</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($status == 'created')
                            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <div>
                                    <p class="text-sm font-semibold text-emerald-800">Complete your payment to unlock all premium features.</p>
                                    <p class="text-xs text-emerald-700 mt-1">You can resume the payment anytime using the button below.</p>
                                </div>
                                <button id="rzpPayNow" type="button"
                                        class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg bg-gradient-to-r from-green-600 to-emerald-600 text-white font-semibold text-sm hover:from-green-700 hover:to-emerald-700 transition-colors shadow-md hover:shadow-lg">
                                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                    Pay Now
                                </button>
                            </div>
                            @elseif($status == 'active')
                            <div class="relative mt-4 rounded-2xl border border-emerald-200 bg-emerald-50/80 p-5 shadow-sm overflow-hidden">
                                <div class="absolute inset-y-0 right-0 w-28 bg-gradient-to-l from-emerald-200/70 to-transparent pointer-events-none"></div>
                                <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
                                    <div class="flex items-start gap-3">
                                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-white text-emerald-600 shadow-inner">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                                        </span>
                                        <div>
                                            <p class="text-sm font-semibold text-emerald-900">Looking for more features?</p>
                                            <p class="text-xs text-emerald-700 mt-1">Upgrade your plan to unlock higher limits and advanced capabilities.</p>
                                        </div>
                                    </div>
                                    <div class="flex sm:items-end">
                                        <a href="{{ route('face_finder.buy_subscription') }}"
                                           class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg bg-gradient-to-r from-green-600 to-emerald-600 text-white font-semibold text-sm shadow-md hover:shadow-lg hover:from-green-700 hover:to-emerald-700 transition-colors">
                                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                                            Upgrade Plan
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="relative rounded-2xl border border-rose-200 bg-rose-50/80 p-5 shadow-sm overflow-hidden">
                                <div class="absolute inset-y-0 right-0 w-28 bg-gradient-to-l from-rose-200/70 to-transparent pointer-events-none"></div>
                                <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
                                    <div class="flex items-start gap-3">
                                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-white text-rose-600 shadow-inner">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        </span>
                                        <div>
                                            <p class="text-sm font-semibold text-rose-900">Your premium plan is currently active.</p>
                                            <p class="text-xs text-rose-700 mt-1">Need to step away? Cancel anytime and keep access until the end of this billing cycle.</p>
                                        </div>
                                    </div>
                                    <div class="flex flex-col sm:items-end gap-2">
                                        <form method="POST" action="{{ route('face_finder.subscription.cancel') }}" class="flex flex-col sm:items-end gap-2">
                                            @csrf
                                            <input type="hidden" name="subscription_id" value="{{ $subscriptionDetails['subscription_id'] ?? '' }}">
                                            <button type="submit"
                                                    class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg bg-gradient-to-r from-rose-500 via-rose-600 to-rose-700 text-white font-semibold text-sm shadow-md hover:shadow-lg hover:from-rose-600 hover:to-rose-800 transition-colors">
                                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                                Cancel Subscription
                                            </button>
                                            <p class="text-[11px] text-rose-500 sm:text-right">We don't bill again once the cancellation is confirmed.</p>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @elseif($status == 'cancelled')
                            <div class="relative rounded-2xl border border-slate-300 bg-slate-50/80 p-5 shadow-sm overflow-hidden">
                                <div class="absolute inset-y-0 right-0 w-28 bg-gradient-to-l from-slate-200/50 to-transparent pointer-events-none"></div>
                                <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
                                    <div class="flex items-start gap-3">
                                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-white text-slate-600 shadow-inner">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        </span>
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900">Your subscription has been cancelled.</p>
                                            <p class="text-xs text-slate-600 mt-1">You can access premium features until {{ isset($subscriptionDetails['end_date']) ? $subscriptionDetails['end_date']->timezone(config('app.timezone'))->format('M j, Y') : 'the end of billing cycle' }}. Reactivate anytime to continue enjoying all benefits.</p>
                                        </div>
                                    </div>
                                    <div class="flex flex-col sm:items-end gap-2">
                                        <a href="{{ route('face_finder.buy_subscription') }}"
                                           class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg bg-gradient-to-r from-green-600 to-emerald-600 text-white font-semibold text-sm shadow-md hover:shadow-lg hover:from-green-700 hover:to-emerald-700 transition-colors">
                                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                            Renew Subscription
                                        </a>
                                        <p class="text-[11px] text-slate-500 sm:text-right">Choose a plan that works for you</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@parent
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var subscriptionId = @json(session('razorPayDeatils.subscription_id') ?? ($subscriptionDetails['subscription_id'] ?? null));
        if (!subscriptionId) {
            return;
        }

        try {
            var options = {
                key: "{{ env('RAZOR_PAY_API_KEY_ID') }}",
                subscription_id: subscriptionId,
                name: 'Face Finder',
                description: 'Subscription payment',
                theme: { color: '#10b981' },
                handler: (response) => {
                    var messages = Alpine.store('messages');
                    if (response && response.razorpay_payment_id && response.razorpay_subscription_id) {
                        messages.showSuccess('🎉 Payment successful! Your subscription will activate shortly. Please refresh this page after a few minutes.', 0);
                    } else {
                        messages.showError('Something went wrong while payment. Please contact support.', 0);
                    }
                },
                modal: { ondismiss: function () {} }
            };

            var rzp = new Razorpay(options);
            var triggerBtn = document.getElementById('rzpPayNow');
            if (triggerBtn) {
                triggerBtn.addEventListener('click', function () {
                    rzp.open();
                });
            }

            var shouldAutoOpen = @json(session()->has('razorPayDeatils.subscription_id'));
            if (shouldAutoOpen) {
                rzp.open();
            }
        } catch (e) {
            console.error('Razorpay init error', e);
        }
    });
</script>
@endsection
