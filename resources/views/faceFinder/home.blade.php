@extends('faceFinder.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Hero -->
        <section class="relative py-16 md:py-24 mb-20 md:mb-28">
            <div class="relative rounded-3xl overflow-hidden bg-gradient-to-br from-slate-50 to-white border border-slate-200/50 shadow-lg">
                <div class="absolute -top-16 -right-16 h-56 w-56 rounded-full bg-emerald-200/40 blur-3xl"></div>
                <div class="absolute -bottom-20 -left-20 h-64 w-64 rounded-full bg-teal-200/40 blur-3xl"></div>

                <div class="relative p-6 sm:p-8 md:p-12 lg:p-16">
                    <!-- Hero Headline -->
                    <div class="text-center mb-10 md:mb-12">
                        <h1
                            class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-extrabold bg-gradient-to-r from-green-600 via-emerald-600 to-teal-600 bg-clip-text text-transparent leading-tight mb-6">
                            Find and share the right photos in seconds
                        </h1>
                        <p class="text-base sm:text-lg md:text-xl text-slate-600 max-w-3xl mx-auto leading-relaxed mb-8">
                            Guests scan once. We show only their photos. Agencies upload a single album and share one link—no more
                            endless searching, zips, or guesswork.
                        </p>
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-10">
                            <a href="{{ route('register') }}"
                                class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-green-600 to-emerald-600 text-white hover:from-green-700 hover:to-emerald-700 shadow-lg hover:shadow-xl transition-all duration-200 font-medium">
                                Get started free
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </a>
                            <a href="#how-it-works"
                                class="inline-flex items-center gap-2 px-6 py-3 rounded-xl border-2 border-slate-300 text-slate-700 hover:border-slate-400 hover:bg-slate-50 transition-all duration-200 font-medium">
                                Learn how it works
                            </a>
                        </div>
                    </div>

                    <!-- Problem + Solution Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8 items-stretch max-w-5xl mx-auto">
                        <div class="rounded-2xl border-2 border-rose-200 bg-gradient-to-br from-rose-50 to-rose-100/50 p-6 md:p-8 hover:shadow-lg transition-shadow duration-200">
                            <div class="flex items-start gap-4">
                                <div class="h-12 w-12 rounded-xl bg-rose-500 text-white inline-flex items-center justify-center text-xl flex-shrink-0 shadow-md">
                                    🧩
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg md:text-xl font-bold text-rose-900 mb-2">The problem</h3>
                                    <p class="text-rose-900/80 text-sm md:text-base leading-relaxed">
                                        At weddings and events, photographers capture thousands of moments. Later, guests ask for "their" photos. Manually
                                        filtering, exporting, and sending links is slow and stressful for agencies.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl border-2 border-emerald-200 bg-gradient-to-br from-emerald-50 to-emerald-100/50 p-6 md:p-8 hover:shadow-lg transition-shadow duration-200">
                            <div class="flex items-start gap-4">
                                <div class="h-12 w-12 rounded-xl bg-emerald-500 text-white inline-flex items-center justify-center text-xl flex-shrink-0 shadow-md">
                                    ✨
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg md:text-xl font-bold text-emerald-900 mb-2">The solution</h3>
                                    <p class="text-emerald-900/80 text-sm md:text-base leading-relaxed">
                                        Upload the album once and share a single public link. Guests scan their face via camera and instantly see only their
                                        photos—ready to view or download.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 3-step flow -->
        <section id="how-it-works" class="mb-20 md:mb-28 scroll-mt-20">
            <div class="text-center mb-12 md:mb-16">
                <div
                    class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-50 text-emerald-700 text-sm font-medium border border-emerald-200 mb-4">
                    How it works
                </div>
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-slate-900 mb-3">
                    Simple 3‑step flow
                </h2>
                <p class="text-slate-600 text-base md:text-lg max-w-2xl mx-auto">
                    Get started in minutes and share photos effortlessly
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8">
                <div class="group rounded-2xl border-2 border-slate-200 bg-white p-6 md:p-8 hover:border-blue-300 hover:shadow-lg transition-all duration-200">
                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 text-white inline-flex items-center justify-center mb-4 text-lg font-bold shadow-md group-hover:scale-110 transition-transform duration-200">
                        1
                    </div>
                    <div class="text-lg md:text-xl font-bold text-slate-900 mb-2">Upload album</div>
                    <p class="text-sm md:text-base text-slate-600 leading-relaxed">
                        Agency uploads a ZIP and generates a single public link.
                    </p>
                </div>
                <div class="group rounded-2xl border-2 border-slate-200 bg-white p-6 md:p-8 hover:border-purple-300 hover:shadow-lg transition-all duration-200">
                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 text-white inline-flex items-center justify-center mb-4 text-lg font-bold shadow-md group-hover:scale-110 transition-transform duration-200">
                        2
                    </div>
                    <div class="text-lg md:text-xl font-bold text-slate-900 mb-2">Share link</div>
                    <p class="text-sm md:text-base text-slate-600 leading-relaxed">
                        Share the album link with guests via QR, WhatsApp, or email.
                    </p>
                </div>
                <div class="group rounded-2xl border-2 border-slate-200 bg-white p-6 md:p-8 hover:border-emerald-300 hover:shadow-lg transition-all duration-200">
                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 text-white inline-flex items-center justify-center mb-4 text-lg font-bold shadow-md group-hover:scale-110 transition-transform duration-200">
                        3
                    </div>
                    <div class="text-lg md:text-xl font-bold text-slate-900 mb-2">Guests scan & view</div>
                    <p class="text-sm md:text-base text-slate-600 leading-relaxed">
                        Guests scan their face and immediately see photos where they appear.
                    </p>
                </div>
            </div>
        </section>

        <!-- Benefits -->
        <section class="mb-20 md:mb-28">
            <div class="text-center mb-12 md:mb-16">
                <div
                    class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-50 text-emerald-700 text-sm font-medium border border-emerald-200 mb-4">
                    Benefits
                </div>
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-slate-900 mb-3">
                    Why photographers love Face Finder
                </h2>
                <p class="text-slate-600 text-base md:text-lg max-w-2xl mx-auto">
                    Everything you need to streamline your event photo workflow
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8">
                <div class="rounded-2xl border-2 border-slate-200 bg-white p-6 md:p-8 hover:border-emerald-300 hover:shadow-lg transition-all duration-200">
                    <div class="text-emerald-600 font-bold text-lg md:text-xl mb-2">Zero manual filtering</div>
                    <p class="text-sm md:text-base text-slate-600 leading-relaxed">
                        Stop searching names and faces. Our scan shows guests their photos automatically.
                    </p>
                </div>
                <div class="rounded-2xl border-2 border-slate-200 bg-white p-6 md:p-8 hover:border-emerald-300 hover:shadow-lg transition-all duration-200">
                    <div class="text-emerald-600 font-bold text-lg md:text-xl mb-2">One link for everything</div>
                    <p class="text-sm md:text-base text-slate-600 leading-relaxed">
                        Upload once, share once. No more sending multiple folders or zips.
                    </p>
                </div>
                <div class="rounded-2xl border-2 border-slate-200 bg-white p-6 md:p-8 hover:border-emerald-300 hover:shadow-lg transition-all duration-200">
                    <div class="text-emerald-600 font-bold text-lg md:text-xl mb-2">Delight your clients</div>
                    <p class="text-sm md:text-base text-slate-600 leading-relaxed">
                        Fast, private, and effortless experience for guests and agencies alike.
                    </p>
                </div>
            </div>
        </section>

        <!-- About -->
        <section id="about" class="mb-20 md:mb-28 scroll-mt-20">
            <div class="text-center mb-12 md:mb-16">
                <div
                    class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-50 text-emerald-700 text-sm font-medium border border-emerald-200 mb-4">
                    About
                </div>
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-slate-900 mb-3">
                    Effortless event photo sharing
                </h2>
                <p class="text-slate-600 text-base md:text-lg max-w-2xl mx-auto">
                    Share a single public link. Guests securely scan and instantly see only their photos.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 md:gap-6">
                <div class="rounded-2xl border-2 border-slate-200 bg-white p-6 hover:border-emerald-300 hover:shadow-lg transition-all duration-200">
                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 text-white inline-flex items-center justify-center text-xl mb-4 shadow-md">
                        🔗
                    </div>
                    <div class="font-bold text-slate-900 text-base md:text-lg mb-2">Single public link</div>
                    <p class="text-sm md:text-base text-slate-600 leading-relaxed">
                        Upload once and share one link for the album.
                    </p>
                </div>
                <div class="rounded-2xl border-2 border-slate-200 bg-white p-6 hover:border-teal-300 hover:shadow-lg transition-all duration-200">
                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-teal-500 to-teal-600 text-white inline-flex items-center justify-center text-xl mb-4 shadow-md">
                        🧠
                    </div>
                    <div class="font-bold text-slate-900 text-base md:text-lg mb-2">Face‑aware viewing</div>
                    <p class="text-sm md:text-base text-slate-600 leading-relaxed">
                        Guests see only photos where they appear.
                    </p>
                </div>
                <div class="rounded-2xl border-2 border-slate-200 bg-white p-6 hover:border-indigo-300 hover:shadow-lg transition-all duration-200">
                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 text-white inline-flex items-center justify-center text-xl mb-4 shadow-md">
                        📦
                    </div>
                    <div class="font-bold text-slate-900 text-base md:text-lg mb-2">ZIP download</div>
                    <p class="text-sm md:text-base text-slate-600 leading-relaxed">
                        Download matched photos in one go.
                    </p>
                </div>
                <div class="rounded-2xl border-2 border-slate-200 bg-white p-6 hover:border-sky-300 hover:shadow-lg transition-all duration-200">
                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-sky-500 to-sky-600 text-white inline-flex items-center justify-center text-xl mb-4 shadow-md">
                        📸
                    </div>
                    <div class="font-bold text-slate-900 text-base md:text-lg mb-2">Unlimited albums</div>
                    <p class="text-sm md:text-base text-slate-600 leading-relaxed">
                        Create albums freely within your plan storage.
                    </p>
                </div>
                <div class="rounded-2xl border-2 border-slate-200 bg-white p-6 hover:border-amber-300 hover:shadow-lg transition-all duration-200">
                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 text-white inline-flex items-center justify-center text-xl mb-4 shadow-md">
                        ⚡
                    </div>
                    <div class="font-bold text-slate-900 text-base md:text-lg mb-2">Live uploader link</div>
                    <p class="text-sm md:text-base text-slate-600 leading-relaxed">
                        Generate an uploader link for live events. Photos become available for guests to scan instantly.
                    </p>
                </div>
            </div>
        </section>

        <!-- FAQs -->
        <section id="faqs" class="mb-16 md:mb-24 scroll-mt-20">
            @include('faceFinder.faq-public')
        </section>
    </div>
@endsection
