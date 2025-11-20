@extends('faceFinder.layout.sidebar-layout')

@section('page-title', 'FAQs')
@section('page-subtitle', 'Find answers to common questions')

@section('content')
    <div class="px-6 lg:px-8 py-6 lg:py-8">
        <div class="max-w-4xl mx-auto">
            <div x-data="{ openIndex: null }" class="space-y-4">
                <div class="bg-white rounded-xl border-2 border-slate-200 shadow-sm overflow-hidden hover:border-emerald-300 hover:shadow-md transition-all duration-200">
                    <button
                        @click="openIndex = openIndex === 0 ? null : 0"
                        class="w-full px-6 py-5 flex items-center justify-between text-left hover:bg-slate-50/50 transition-colors duration-200"
                    >
                        <span class="text-lg font-semibold text-slate-900 pr-4">Where to find public link for an event?</span>
                        <svg
                            class="w-5 h-5 text-slate-500 transition-transform duration-300 flex-shrink-0"
                            :class="{ 'rotate-180': openIndex === 0 }"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div
                        x-show="openIndex === 0"
                        style="display: none;"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform -translate-y-2"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 transform translate-y-0"
                        x-transition:leave-end="opacity-0 transform -translate-y-2"
                        class="px-6 pb-5 text-slate-600 leading-relaxed overflow-hidden"
                    >
                        <p>You can find the public link in the Events section. Navigate to "Manage Events" from the sidebar, select your event, and you'll see the option to generate or view the public link for that event.</p>
                    </div>
                </div>

                <div class="bg-white rounded-xl border-2 border-slate-200 shadow-sm overflow-hidden hover:border-emerald-300 hover:shadow-md transition-all duration-200">
                    <button
                        @click="openIndex = openIndex === 1 ? null : 1"
                        class="w-full px-6 py-5 flex items-center justify-between text-left hover:bg-slate-50/50 transition-colors duration-200"
                    >
                        <span class="text-lg font-semibold text-slate-900 pr-4">Where to find and generate the uploader link?</span>
                        <svg
                            class="w-5 h-5 text-slate-500 transition-transform duration-300 flex-shrink-0"
                            :class="{ 'rotate-180': openIndex === 1 }"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div
                        x-show="openIndex === 1"
                        style="display: none;"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform -translate-y-2"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 transform translate-y-0"
                        x-transition:leave-end="opacity-0 transform -translate-y-2"
                        class="px-6 pb-5 text-slate-600 leading-relaxed overflow-hidden"
                    >
                        <p>You can find and generate the uploader link in the Albums section. Go to "Albums" from the sidebar, select an album, and you'll see the option to create or manage the uploader link for that album. This link allows photographers to upload photos directly during live events.</p>
                    </div>
                </div>

                <div class="bg-white rounded-xl border-2 border-slate-200 shadow-sm overflow-hidden hover:border-emerald-300 hover:shadow-md transition-all duration-200">
                    <button
                        @click="openIndex = openIndex === 2 ? null : 2"
                        class="w-full px-6 py-5 flex items-center justify-between text-left hover:bg-slate-50/50 transition-colors duration-200"
                    >
                        <span class="text-lg font-semibold text-slate-900 pr-4">What public link analytics shows?</span>
                        <svg
                            class="w-5 h-5 text-slate-500 transition-transform duration-300 flex-shrink-0"
                            :class="{ 'rotate-180': openIndex === 2 }"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div
                        x-show="openIndex === 2"
                        style="display: none;"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform -translate-y-2"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 transform translate-y-0"
                        x-transition:leave-end="opacity-0 transform -translate-y-2"
                        class="px-6 pb-5 text-slate-600 leading-relaxed overflow-hidden"
                    >
                        <p>The public link analytics shows the number of guests have successfully accessed the link, how many unique users have used link, and the number of attempts that does not find the matched photo through the public link.</p>
                    </div>
                </div>

                <div class="bg-white rounded-xl border-2 border-slate-200 shadow-sm overflow-hidden hover:border-emerald-300 hover:shadow-md transition-all duration-200">
                    <button
                        @click="openIndex = openIndex === 3 ? null : 3"
                        class="w-full px-6 py-5 flex items-center justify-between text-left hover:bg-slate-50/50 transition-colors duration-200"
                    >
                        <span class="text-lg font-semibold text-slate-900 pr-4">How to upgrade the plan?</span>
                        <svg
                            class="w-5 h-5 text-slate-500 transition-transform duration-300 flex-shrink-0"
                            :class="{ 'rotate-180': openIndex === 3 }"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div
                        x-show="openIndex === 3"
                        style="display: none;"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform -translate-y-2"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 transform translate-y-0"
                        x-transition:leave-end="opacity-0 transform -translate-y-2"
                        class="px-6 pb-5 text-slate-600 leading-relaxed overflow-hidden"
                    >
                        <p>To upgrade your plan, please contact us. You can reach out through our support channels, and our team will assist you with the upgrade process and answer any questions about available plans and features.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

