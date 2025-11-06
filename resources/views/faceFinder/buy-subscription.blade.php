@extends('faceFinder.layout.sidebar-layout')

@section('page-title', userSubscriptionActivated() ? 'Upgrade Plan' : 'Buy Subscription')

@section('content')
    <div class="mx-auto px-6">
        @if(isUserOnTrial())
            <div class="mb-8 rounded-2xl border border-amber-200 bg-gradient-to-r from-amber-50 to-orange-50 shadow-lg p-6 relative">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-3">
                            <h3 class="text-lg font-bold text-orange-900">You're on Trial</h3>
                        </div>
                        <p class="text-amber-800 text-sm mb-4">Experience our Face Finder with some limitations. Upgrade to unlock full features!</p>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div class="flex items-start gap-2 text-sm text-amber-800">
                                <div class="flex-shrink-0 mt-1">
                                    <svg class="h-5 w-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-semibold">Single Album</span>
                                    <p class="text-xs text-amber-700 mt-0.5">Only one album creation</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2 text-sm text-amber-800">
                                <div class="flex-shrink-0 mt-1">
                                    <svg class="h-5 w-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-semibold">10 Images Allowed</span>
                                    <p class="text-xs text-amber-700 mt-0.5">Maximum photos per album</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2 text-sm text-amber-800">
                                <div class="flex-shrink-0 mt-1">
                                    <svg class="h-5 w-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-semibold">No Zip File Download</span>
                                    <p class="text-xs text-amber-700 mt-0.5">Not allowed to download matched photos zip file</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @include('faceFinder.components.pricing-cards')
    </div>
@endsection
