@extends('faceFinder.layout.sidebar-layout')

@section('page-title', 'Storage Usage')

@section('content')
    <div class="max-w-7xl mx-auto px-6">

        @if($storageLimitGB > 0)
            @php
                $progressColor = $percentageUsed >= 90 ? '#ef4444' : ($percentageUsed >= 70 ? '#f97316' : '#10b981');
                $radius = 90;
                $circumference = 2 * pi() * $radius;
                $strokeDashoffset = $circumference - (($percentageUsed / 100) * $circumference);
            @endphp

            <!-- Main Storage Card -->
            <div class="mb-6 rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-green-600 to-emerald-600 p-6">
                    <div class="flex items-center justify-between text-white">
                        <div>
                            <h2 class="text-xl font-bold">{{ $planName ?? 'Your Plan' }}</h2>
                            <p class="text-green-100 text-sm mt-1">Total Storage: {{ number_format($storageLimitGB, 2) }} GB</p>
                        </div>
                        <div class="h-12 w-12 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center">
                            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Circular Progress Section -->
                <div class="p-8 flex flex-col items-center">
                    <div class="relative inline-flex items-center justify-center mb-6">
                        <svg class="transform -rotate-90" width="220" height="220">
                            <!-- Background circle -->
                            <circle cx="110" cy="110" r="{{ $radius }}" stroke="#e2e8f0" stroke-width="12" fill="none"/>
                            <!-- Progress circle -->
                            <circle cx="110" cy="110" r="{{ $radius }}"
                                stroke="{{ $progressColor }}"
                                stroke-width="12"
                                fill="none"
                                stroke-linecap="round"
                                stroke-dasharray="{{ $circumference }}"
                                stroke-dashoffset="{{ $strokeDashoffset }}"
                                style="transition: stroke-dashoffset 0.5s ease"/>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-5xl font-bold text-slate-900">{{ number_format($percentageUsed, 1) }}%</span>
                            <span class="text-sm text-slate-500 mt-2">{{ number_format($storageUsedGB, 2) }} / {{ number_format($storageLimitGB, 2) }} GB</span>
                        </div>
                    </div>

                    <!-- Alert Messages -->
                    @if($percentageUsed >= 90)
                        <div class="w-full max-w-2xl mb-6 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700 flex items-center gap-3">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span><strong>Storage Almost Full!</strong> You've used {{ number_format($percentageUsed, 1) }}% of your storage. Consider upgrading your plan or deleting some albums.</span>
                        </div>
                    @elseif($percentageUsed >= 70)
                        <div class="w-full max-w-2xl mb-6 rounded-xl bg-orange-50 border border-orange-200 px-4 py-3 text-sm text-orange-700 flex items-center gap-3">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>You're using {{ number_format($percentageUsed, 1) }}% of your storage limit.</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Storage Details Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Storage Used (GB) -->
                <div class="rounded-xl border border-blue-200 bg-gradient-to-br from-blue-50 to-indigo-50 p-6 shadow-sm">
                    <div class="flex items-start gap-4">
                        <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center shrink-0">
                            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm font-medium text-blue-700 mb-2">Storage Used</div>
                            <div class="text-4xl font-bold text-blue-800 mb-1">{{ number_format($storageUsedGB, 2) }}</div>
                            <div class="text-sm text-blue-600">Gigabytes (GB)</div>
                        </div>
                    </div>
                </div>

                <!-- Storage Available (GB) -->
                <div class="rounded-xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-emerald-100 p-6 shadow-sm">
                    <div class="flex items-start gap-4">
                        <div class="h-14 w-14 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0">
                            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm font-medium text-emerald-700 mb-2">Storage Available</div>
                            <div class="text-4xl font-bold text-emerald-900 mb-1">{{ number_format($storageLeftGB, 2) }}</div>
                            <div class="text-sm text-emerald-600">Gigabytes (GB)</div>
                        </div>
                    </div>
                </div>
            </div>

        @else
            <!-- No Active Subscription -->
            <div class="rounded-2xl border border-blue-200 bg-gradient-to-br from-blue-50 to-indigo-50 p-8 text-center shadow-sm">
                <div class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 text-blue-600 mb-4">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">No Active Subscription</h3>
                <p class="text-slate-600 mb-6 max-w-md mx-auto">
                    You don't have an active subscription plan. Subscribe to a plan to get storage and start using Face Finder.
                </p>
                <a href="{{ route('face_finder.buy_subscription') }}"
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-green-600 to-emerald-600 text-white font-semibold hover:from-green-700 hover:to-emerald-700 transition-all shadow-md hover:shadow-lg">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Subscribe Now
                </a>
            </div>
        @endif
    </div>
@endsection

