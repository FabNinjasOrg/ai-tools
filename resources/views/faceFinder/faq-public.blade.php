<div class="max-w-4xl mx-auto">
    <div class="text-center mb-12 md:mb-16">
        <div
            class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-50 text-emerald-700 text-sm font-medium border border-emerald-200 mb-4">
            FAQs
        </div>
        <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-slate-900 mb-3">
            Frequently Asked Questions
        </h2>
        <p class="text-slate-600 text-base md:text-lg max-w-2xl mx-auto">
            Find answers to common questions about FaceFinder
        </p>
    </div>

    <div x-data="{ openIndex: null }" class="space-y-4">
        <div class="bg-white rounded-xl border-2 border-slate-200 shadow-sm overflow-hidden hover:border-emerald-300 hover:shadow-md transition-all duration-200">
            <button
                @click="openIndex = openIndex === 0 ? null : 0"
                class="w-full px-6 py-5 flex items-center justify-between text-left hover:bg-slate-50/50 transition-colors duration-200"
            >
                <span class="text-lg font-semibold text-slate-900 pr-4">What is FaceFinder?</span>
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
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform -translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform -translate-y-2"
                class="px-6 pb-5 text-slate-600 leading-relaxed overflow-hidden"
            >
                <p>FaceFinder is an AI-powered platform that helps you find specific people across your event photo collections. Simply upload your event photos, and guests can search for photos of themselves using a selfie.</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border-2 border-slate-200 shadow-sm overflow-hidden hover:border-emerald-300 hover:shadow-md transition-all duration-200">
            <button
                @click="openIndex = openIndex === 1 ? null : 1"
                class="w-full px-6 py-5 flex items-center justify-between text-left hover:bg-slate-50/50 transition-colors duration-200"
            >
                <span class="text-lg font-semibold text-slate-900 pr-4">What photo formats are supported?</span>
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
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform -translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform -translate-y-2"
                class="px-6 pb-5 text-slate-600 leading-relaxed overflow-hidden"
            >
                <p>We support PNG, JPG, JPEG, and WEBP image formats. You can upload individual photos or ZIP files containing multiple photos.</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border-2 border-slate-200 shadow-sm overflow-hidden hover:border-emerald-300 hover:shadow-md transition-all duration-200">
            <button
                @click="openIndex = openIndex === 2 ? null : 2"
                class="w-full px-6 py-5 flex items-center justify-between text-left hover:bg-slate-50/50 transition-colors duration-200"
            >
                <span class="text-lg font-semibold text-slate-900 pr-4">Can we use FaceFinder for free?</span>
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
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform -translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform -translate-y-2"
                class="px-6 pb-5 text-slate-600 leading-relaxed overflow-hidden"
            >
                <p>FaceFinder is not free to use. but you can signup for free and explore it's features on trial basis. the trial includes one event creation with albums, 10 photos upload limit, public link sharing for your guests.</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border-2 border-slate-200 shadow-sm overflow-hidden hover:border-emerald-300 hover:shadow-md transition-all duration-200">
            <button
                @click="openIndex = openIndex === 3 ? null : 3"
                class="w-full px-6 py-5 flex items-center justify-between text-left hover:bg-slate-50/50 transition-colors duration-200"
            >
                <span class="text-lg font-semibold text-slate-900 pr-4">How accurate is the face recognition?</span>
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
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform -translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform -translate-y-2"
                class="px-6 pb-5 text-slate-600 leading-relaxed overflow-hidden"
            >
                <p>We use state-of-the-art InsightFace technology for face recognition. Accuracy depends on photo quality, lighting conditions, and face visibility. Our system shows similarity percentages for each match, and only displays results with 40% or higher similarity.</p>
            </div>
        </div>
    </div>
</div>