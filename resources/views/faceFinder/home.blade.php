@extends('faceFinder.app')

@section('content')
    <div class="max-w-7xl mx-auto px-6 py-20">
        <!-- Hero -->
        <section class="text-center mb-20">
            <h1
                class="mt-4 text-4xl md:text-6xl font-extrabold bg-gradient-to-r from-green-600 via-emerald-600 to-teal-600 bg-clip-text text-transparent">
                Find and share the right photos in seconds
            </h1>
            <p class="mt-5 text-slate-600 text-lg md:text-xl max-w-3xl mx-auto leading-relaxed">
                Guests scan once. We show only their photos. Agencies upload a single album and share one link—no more
                endless searching, zips, or guesswork.
            </p>
        </section>

        <!-- Problem + Solution -->
        <section id="how" class="mb-16">
            <div class="relative rounded-3xl overflow-hidden">
                <div class="absolute -top-16 -right-16 h-56 w-56 rounded-full bg-emerald-200/40 blur-3xl"></div>
                <div class="absolute -bottom-20 -left-20 h-64 w-64 rounded-full bg-teal-200/40 blur-3xl"></div>

                <div class="relative p-6 md:p-10">
                    <div class="text-center mb-8">
                        <div
                            class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[13px] border border-emerald-200">
                            Why Face Finder</div>
                        <h2 class="mt-3 text-2xl md:text-3xl font-bold text-slate-900">From chaotic sharing to one smart
                            link</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-6">
                            <div class="flex items-start gap-3">
                                <div class="h-10 w-10 text-white inline-flex items-center justify-center">🧩</div>
                                <div>
                                    <h3 class="text-lg font-semibold text-rose-900">The problem</h3>
                                    <p class="text-rose-900/80 mt-2 text-[15px]">At weddings and events, photographers
                                        capture thousands of moments. Later, guests ask for “their” photos. Manually
                                        filtering, exporting, and sending links is slow and stressful for agencies.</p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-6">
                            <div class="flex items-start gap-3">
                                <div class="h-10 w-10 text-white inline-flex items-center justify-center">✨</div>
                                <div>
                                    <h3 class="text-lg font-semibold text-emerald-900">The solution</h3>
                                    <p class="text-emerald-900/80 mt-2 text-[15px]">Upload the album once and share a single
                                        public link. Guests scan their face via camera and instantly see only their
                                        photos—ready to view or download.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 text-center">
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-green-600 to-emerald-600 text-white hover:from-green-700 hover:to-emerald-700 shadow">
                            Start with your next event
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- 3-step flow -->
        <section class="mb-16">
            <h2 class="text-2xl md:text-3xl font-bold text-slate-900 text-center mb-8">Simple 3‑step flow</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="rounded-2xl border border-slate-200 bg-white p-6">
                    <div class="h-10 w-10 rounded-xl bg-blue-500 text-white inline-flex items-center justify-center mb-3">1
                    </div>
                    <div class="text-base font-semibold text-slate-900">Upload album</div>
                    <p class="text-[13px] text-slate-600 mt-1">Agency uploads a ZIP and generates a single public link.</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-6">
                    <div class="h-10 w-10 rounded-xl bg-purple-500 text-white inline-flex items-center justify-center mb-3">
                        2</div>
                    <div class="text-base font-semibold text-slate-900">Share link</div>
                    <p class="text-[13px] text-slate-600 mt-1">Share the album link with guests via QR, WhatsApp, or email.
                    </p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-6">
                    <div
                        class="h-10 w-10 rounded-xl bg-emerald-500 text-white inline-flex items-center justify-center mb-3">
                        3</div>
                    <div class="text-base font-semibold text-slate-900">Guests scan & view</div>
                    <p class="text-[13px] text-slate-600 mt-1">Guests scan their face and immediately see photos where they
                        appear.</p>
                </div>
            </div>
        </section>

        <!-- Benefits -->
        <section class="mb-16">
            <h2 class="text-2xl md:text-3xl font-bold text-slate-900 text-center mb-8">Why photographers love Face Finder
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="rounded-2xl border border-slate-200 bg-white p-6">
                    <div class="text-emerald-600 font-semibold mb-1">Zero manual filtering</div>
                    <p class="text-[13px] text-slate-600">Stop searching names and faces. Our scan shows guests their photos
                        automatically.</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-6">
                    <div class="text-emerald-600 font-semibold mb-1">One link for everything</div>
                    <p class="text-[13px] text-slate-600">Upload once, share once. No more sending multiple folders or zips.
                    </p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-6">
                    <div class="text-emerald-600 font-semibold mb-1">Delight your clients</div>
                    <p class="text-[13px] text-slate-600">Fast, private, and effortless experience for guests and agencies
                        alike.</p>
                </div>
            </div>
        </section>

        <!-- About -->
        <section id="about" class="mb-10">
            <div class="text-center mb-8">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[13px]">About</div>
                <h2 class="mt-3 text-2xl md:text-3xl font-bold text-slate-900">Effortless event photo sharing</h2>
                <p class="mt-2 text-slate-600 max-w-2xl mx-auto">Share a single public link. Guests securely scan and instantly see only their photos.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="rounded-2xl bg-white p-5 shadow-sm">
                    <div class="h-10 w-10 rounded-xl bg-emerald-500 text-white inline-flex items-center justify-center">🔗</div>
                    <div class="mt-3 font-semibold text-slate-900">Single public link</div>
                    <p class="text-[13px] text-slate-600 mt-1">Upload once and share one link for the album.</p>
                </div>
                <div class="rounded-2xl bg-white p-5 shadow-sm">
                    <div class="h-10 w-10 rounded-xl bg-teal-500 text-white inline-flex items-center justify-center">🧠</div>
                    <div class="mt-3 font-semibold text-slate-900">Face‑aware viewing</div>
                    <p class="text-[13px] text-slate-600 mt-1">Guests see only photos where they appear.</p>
                </div>
                <div class="rounded-2xl bg-white p-5 shadow-sm">
                    <div class="h-10 w-10 rounded-xl bg-indigo-500 text-white inline-flex items-center justify-center">📦</div>
                    <div class="mt-3 font-semibold text-slate-900">ZIP download</div>
                    <p class="text-[13px] text-slate-600 mt-1">Download matched photos in one go.</p>
                </div>
                <div class="rounded-2xl bg-white p-5 shadow-sm">
                    <div class="h-10 w-10 rounded-xl bg-sky-500 text-white inline-flex items-center justify-center">📸</div>
                    <div class="mt-3 font-semibold text-slate-900">Unlimited albums</div>
                    <p class="text-[13px] text-slate-600 mt-1">Create albums freely within your plan storage.</p>
                </div>
            </div>


        </section>


    </div>
@endsection
