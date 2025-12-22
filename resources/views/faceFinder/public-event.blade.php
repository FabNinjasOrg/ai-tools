@extends('faceFinder.app')

@section('content')
    @if (empty($eventName))
        <div class="max-w-7xl mx-auto px-6 py-12">
            <div class="rounded-2xl border border-red-200 bg-red-50 text-red-800 px-4 py-4">
                <div class="flex items-start gap-3">
                    <div
                        class="shrink-0 h-6 w-6 rounded-full bg-red-100 text-red-700 inline-flex items-center justify-center">
                        !</div>
                    <div class="flex-1">
                        This URL is not proper. Please contact the admin.
                    </div>
                </div>
            </div>
        </div>
    @else
        <div x-data="eventPage('{{ $uuid }}')" x-init="init()" x-cloak class="max-w-7xl mx-auto px-6 py-12">
            <div class="mb-6">
                <template x-if="successMessage">
                    <div class="w-full mb-3">
                        <div
                            class="rounded-xl border border-green-200 border-l-4 border-l-green-500 bg-green-50 px-4 py-3 text-sm shadow-sm text-green-800">
                            <div class="flex items-start gap-3">
                                <div
                                    class="shrink-0 h-5 w-5 rounded-full bg-green-100 text-green-700 inline-flex items-center justify-center">
                                    ✓</div>
                                <div class="flex-1" x-text="successMessage"></div>
                                <button @click="successMessage=''" class="text-green-700/70 hover:text-green-800">✕</button>
                            </div>
                        </div>
                    </div>
                </template>
                <template x-if="errorMessage">
                    <div class="w-full mb-3">
                        <div
                            class="rounded-xl border border-red-200 border-l-4 border-l-red-500 bg-red-50 px-4 py-3 text-sm shadow-sm text-red-800">
                            <div class="flex items-start gap-3">
                                <div
                                    class="shrink-0 h-5 w-5 rounded-full bg-red-100 text-red-700 inline-flex items-center justify-center">
                                    !</div>
                                <div class="flex-1" x-text="errorMessage"></div>
                                <button @click="errorMessage=''" class="text-red-700/70 hover:text-red-800">✕</button>
                            </div>
                        </div>
                    </div>
                </template>
                <template x-if="infoMessage">
                    <div class="w-full mb-3">
                        <div
                            class="rounded-xl border border-slate-200 border-l-4 border-l-slate-400 bg-slate-50 px-4 py-3 text-sm shadow-sm text-slate-800">
                            <div class="flex items-start gap-3">
                                <div
                                    class="shrink-0 h-5 w-5 rounded-full bg-slate-100 text-slate-700 inline-flex items-center justify-center">
                                    i</div>
                                <div class="flex-1" x-text="infoMessage"></div>
                                <button @click="infoMessage=''" class="text-slate-600/70 hover:text-slate-800">✕</button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Event Header Card -->
            <div class="mb-8 rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="px-6 py-5 md:px-8 md:py-6">
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                        <!-- Event Name and Info -->
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="flex-shrink-0 pt-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 md:h-8 md:w-8 text-emerald-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="flex-1 flex items-baseline gap-3 flex-wrap">
                                    <h2 class="text-2xl md:text-3xl lg:text-4xl xl:text-5xl font-bold bg-gradient-to-r from-green-600 via-emerald-600 to-teal-600 bg-clip-text text-transparent leading-none">
                                        {{ $eventName }}
                                    </h2>
                                </div>
                            </div>
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-50 border border-emerald-100">
                                <span class="h-4 w-4 rounded-full bg-emerald-100 text-emerald-700 inline-flex items-center justify-center font-semibold text-xs">i</span>
                                <span class="text-sm text-slate-700 font-medium">Images will show only when the match is 40% or higher.</span>
                            </div>
                        </div>

                        <!-- Logout Button -->
                        <div x-show="hasActiveSession" x-cloak class="flex-shrink-0">
                            <form method="POST" action="{{ route('face_finder.public.logout') }}" class="inline">
                                @csrf
                                <input type="hidden" name="uuid" value="{{ $uuid }}">
                                <button type="submit"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 rounded-xl shadow-sm hover:shadow-md transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div x-cloak class="mb-8 relative rounded-2xl min-h-[60vh] md:min-h-[50vh] lg:min-h-[60vh]">
                <!-- Blurred placeholder image shapes when locked -->
                <div x-show="!matchedPhotos.length" class="absolute inset-0 z-0">
                    <div class="h-full w-full p-4">
                        <div class="grid grid-cols-2 grid-rows-2 gap-4 h-full w-full filter blur-sm opacity-80">
                            <div class="bg-slate-300/80 rounded-xl"></div>
                            <div class="bg-slate-400/80 rounded-xl"></div>
                            <div class="bg-slate-400/80 rounded-xl"></div>
                            <div class="bg-slate-300/80 rounded-xl"></div>
                        </div>
                    </div>
                    <div class="absolute inset-0 bg-white/40"></div>
                </div>
                <div class="relative z-10 h-full flex flex-col items-center justify-center space-y-4">
                    {{-- <div x-show="matchedPhotos.length > 0" x-cloak class="mb-3 w-full flex justify-end items-center">
                        @if(userHasAccessibility())
                        <!-- Download Zip Button -->
                        <button @click="downloadMatchedPhotosZip()"
                            class="px-4 py-2 rounded-lg bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-medium shadow hover:from-purple-700 hover:to-indigo-700 inline-flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Download ZIP
                        </button>
                        @endif
                    </div> --}}
                    <!-- Scan Again Button (outside photos grid card) -->
                    <div x-show="matchedPhotos.length > 0" x-cloak class="mb-4 flex justify-center w-full">
                        <button @click="openCamera()" data-force-consent
                            class="px-4 py-2 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium shadow hover:from-blue-700 hover:to-indigo-700 inline-flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Scan Face Again
                        </button>
                    </div>

                    <div x-show="matchedPhotos.length > 0" x-cloak
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-4">
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                            <template x-for="photo in matchedPhotos" :key="photo.id">
                                <div
                                    class="group rounded-xl overflow-hidden border border-slate-200 bg-white w-full shadow-sm hover:shadow-md transition duration-200">
                                    <div class="relative w-full aspect-square">
                                        <img :src="photo.src" :alt="photo.filename"
                                            class="w-full h-full object-cover group-hover:scale-[1.01] transition-transform duration-200" />
                                        <!-- subtle gradient overlay on hover -->
                                        <div
                                            class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/40 via-black/0 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                                        </div>
                                        <!-- bottom-left match badge -->
                                        <div class="absolute left-2 bottom-2 px-2 py-0.5 rounded-md text-[11px] font-medium bg-white/90 text-slate-800 shadow"
                                            x-text="`${Math.round(((photo.similarity || 0) * 100))}% Matched`"></div>

                                        <a :href="photo.src" :download="photo.filename || true"
                                            class="absolute top-2 right-2 flex items-center justify-center h-8 w-8 rounded-md bg-white/90 text-slate-700 shadow hover:bg-white"
                                            title="Download">
                                            <!-- Download (arrow down into tray) icon -->
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M7.5 12 12 16.5 16.5 12" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V3" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="mt-4 flex items-center justify-start gap-3">
                            <div class="inline-flex items-center gap-2 rounded-lg bg-slate-50 border border-slate-200 px-3 py-2 w-full sm:w-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="text-sm font-medium text-slate-700"
                                    x-text="`${matchedPhotos.length} ${matchedPhotos.length === 1 ? 'photo' : 'photos'} found`"></span>
                            </div>
                        </div>

                        <!-- Load More Button -->
                        <div x-show="matchedPhotosHasMore" x-cloak class="flex justify-center mt-6">
                            <button @click="loadMoreMatchedPhotos()"
                                class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="matchedPhotosLoading">
                                <span x-show="!matchedPhotosLoading">Load more</span>
                                <span x-show="matchedPhotosLoading" class="inline-flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Loading…
                                </span>
                            </button>
                        </div>
                    </div>

                    <!-- Download all + WhatsApp buttons below grid card -->
                    <div x-show="matchedPhotos.length > 0" x-cloak
                        class="mt-3 flex flex-col sm:flex-row gap-3 justify-start w-full">
                        <div x-data="{ isDesktop: isDesktopDevice() }"
                            x-show="isDesktop && matchedPhotos.length > 0" x-cloak
                            class="w-full sm:w-auto">
                            <button @click="downloadAllPhotos()"
                                class="w-full sm:w-auto px-4 py-2 rounded-lg bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-medium shadow hover:from-purple-700 hover:to-indigo-700 inline-flex items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M7.5 12 12 16.5 16.5 12" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V3" />
                                </svg>
                                Download all photos
                            </button>
                        </div>

                        <button type="button" @click="shareOnWhatsApp()"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-green-500 to-emerald-500 text-white px-5 py-2 text-sm font-medium shadow hover:from-green-600 hover:to-emerald-600">
                            <svg class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill="currentColor" d="M16.75,13.96C17,14.09 17.16,14.16 17.21,14.26C17.27,14.37 17.25,14.87 17,15.44C16.8,16 15.76,16.54 15.3,16.56C14.84,16.58 14.83,16.92 12.34,15.83C9.85,14.74 8.35,12.08 8.23,11.91C8.11,11.74 7.27,10.53 7.31,9.3C7.36,8.08 8,7.5 8.26,7.26C8.5,7 8.77,6.97 8.94,7H9.41C9.56,7 9.77,6.94 9.96,7.45L10.65,9.32C10.71,9.45 10.75,9.6 10.66,9.76L10.39,10.17L10,10.59C9.88,10.71 9.74,10.84 9.88,11.09C10,11.35 10.5,12.18 11.2,12.87C12.11,13.75 12.91,14.04 13.15,14.17C13.39,14.31 13.54,14.29 13.69,14.13L14.5,13.19C14.69,12.94 14.85,13 15.08,13.08L16.75,13.96M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22C10.03,22 8.2,21.43 6.65,20.45L2,22L3.55,17.35C2.57,15.8 2,13.97 2,12A10,10 0 0,1 12,2M12,4A8,8 0 0,0 4,12C4,13.72 4.54,15.31 5.46,16.61L4.5,19.5L7.39,18.54C8.69,19.46 10.28,20 12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4Z" />
                            </svg>
                            <span>Get all photos on WhatsApp</span>
                        </button>
                    </div>

                    <!-- Loading state while checking verification -->
                    <div x-show="isCheckingVerification" x-cloak class="w-full text-center">
                        <div class="inline-flex items-center gap-3 text-slate-600">
                            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-sm">Checking session...</span>
                        </div>
                    </div>

                    <!-- No matches message in the same area -->
                    <div x-show="showNoMatches" x-cloak class="w-full text-center">
                        <div class="bg-white/70 backdrop-blur rounded-xl px-4 py-6 border border-slate-200">
                            <h3 class="text-base font-semibold text-slate-900 mb-1">No Matching Photos Found</h3>
                            <p class="text-slate-600 text-sm">Try taking another photo or check if the person appears in
                                this album.</p>
                            <div class="mt-4">
                                <button @click="openCamera()"
                                    class="px-5 py-2.5 rounded-lg bg-gradient-to-r from-green-600 to-emerald-600 text-white font-medium shadow hover:from-green-700 hover:to-emerald-700 inline-flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Try Again
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Unlock/Camera controls (hidden once results or no-matches are shown) -->
                    <div x-show="!matchedPhotos.length && !showNoMatches && !isCheckingVerification" x-cloak
                        class="flex flex-col items-center space-y-4">
                        <!-- Step 1: Unlock My Photos Button -->
                        <template x-if="!showUnlockStep">
                            <button @click="openPhoneModal()"
                                class="px-6 py-4 rounded-xl bg-gradient-to-r from-green-600 to-emerald-600 text-white font-medium shadow hover:from-green-700 hover:to-emerald-700 inline-flex items-center gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                Unlock My Photos
                            </button>
                        </template>

                        <!-- Step 2: Camera Button and Info -->
                        <template x-if="showUnlockStep">
                            <div class="flex flex-col items-center space-y-4">
                        <button @click="openCamera()" data-force-consent
                                    class="px-6 py-4 rounded-xl bg-gradient-to-r from-green-600 to-emerald-600 text-white font-medium shadow hover:from-green-700 hover:to-emerald-700 inline-flex items-center gap-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Camera
                                </button>
                                <p class="text-slate-600 text-sm text-center max-w-md">Click your image to find your photos
                                    from the album</p>
                            </div>
                        </template>
                    </div>
            </div>
        </div>

        <!-- Phone Verification Modal -->
            <div x-show="showPhoneModal" x-cloak
                class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center">
                <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-slate-900">Enter Phone Number</h3>
                        <button @click="closePhoneModal()" class="text-slate-400 hover:text-slate-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Phone Number Input Step -->
                    {{-- <div x-show="!showOTPInput" class="space-y-4"> --}}
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Phone Number</label>
                            <div class="flex">
                                <span
                                    class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-slate-300 bg-slate-50 text-slate-700 text-sm">
                                    +91
                                </span>
                                <input x-model="phoneNumber" @input="validatePhoneNumber" type="tel"
                                    placeholder="1234567890"
                                    class="w-full px-3 py-2 border border-slate-300 border-l-0 rounded-r-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                    :disabled="isSendingOTP">
                            </div>
                            <p class="text-xs text-slate-500 mt-1">Enter your 10-digit Indian phone number. </p>
                            <div x-show="otpErrorMessage" x-cloak class="text-red-500 text-xs mt-1" x-text="otpErrorMessage"></div>
                            {{-- <div x-show="otpSuccessMessage" x-cloak class="text-green-500 text-xs mt-1" x-text="otpSuccessMessage"></div> --}}
                        </div>

                        <div class="flex gap-3">
                            <button @click="closePhoneModal()"
                                class="flex-1 px-4 py-2 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50">Cancel</button>
                            {{-- Original OTP flow button --}}
                            {{-- <button @click="sendOTP()" :disabled="!phoneNumber || isSendingOTP || otpErrorMessage !== ''"
                                class="flex-1 px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:from-green-700 hover:to-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center justify-center gap-2">
                                <svg x-show="isSendingOTP" class="h-4 w-4 animate-spin" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10" class="opacity-25" />
                                    <path d="M4 12a8 8 0 018-8" class="opacity-75" />
                                </svg>
                                <span x-text="isSendingOTP ? 'Sending...' : 'Send OTP'"></span>
                            </button> --}}
                            <button @click="submitPhoneNumber()" :disabled="!phoneNumber || isSendingOTP || otpErrorMessage !== ''"
                                class="flex-1 px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:from-green-700 hover:to-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center justify-center gap-2">
                                <svg x-show="isSendingOTP" class="h-4 w-4 animate-spin" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10" class="opacity-25" />
                                    <path d="M4 12a8 8 0 018-8" class="opacity-75" />
                                </svg>
                                <span x-text="isSendingOTP ? 'Submitting...' : 'Submit'"></span>
                            </button>
                        </div>
                    </div>
                    {{-- </div> --}}

                    {{-- OTP Input Step (commented out - can be enabled in future) --}}
                    {{-- <div x-show="showOTPInput" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Enter OTP</label>
                            <input x-model="otpCode" type="text" placeholder="123456" maxlength="6"
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-center text-lg tracking-widest"
                                :disabled="isVerifyingOTP">
                            <p class="text-xs text-slate-500 mt-1">Enter the 6-digit code sent to <span
                                    x-text="'+' + '91 ' + phoneNumber"></span></p>
                            <div x-show="otpErrorMessage" x-cloak class="text-red-500 text-xs mt-1" x-text="otpErrorMessage"></div>
                            <div x-show="otpSuccessMessage" x-cloak class="text-green-500 text-xs mt-1" x-text="otpSuccessMessage"></div>
                        </div>

                        <div class="flex gap-3">
                            <button @click="backToPhoneInput()"
                                class="flex-1 px-4 py-2 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50">Back</button>
                            <button @click="verifyOTP()" :disabled="!otpCode || otpCode.length !== 6 || isVerifyingOTP"
                                class="flex-1 px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:from-green-700 hover:to-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center justify-center gap-2">
                                <svg x-show="isVerifyingOTP" class="h-4 w-4 animate-spin" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10" class="opacity-25" />
                                    <path d="M4 12a8 8 0 018-8" class="opacity-75" />
                                </svg>
                                <span x-text="isVerifyingOTP ? 'Verifying...' : 'Verify'"></span>
                            </button>
                        </div>
                    </div> --}}
                </div>
            </div>

            <!-- Camera Modal -->
            <div x-show="showCamera" x-cloak
                class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center">
                <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-slate-900">Take Photo</h3>
                        <button @click="closeCamera()" class="text-slate-400 hover:text-slate-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="mb-4">
                        <video x-ref="videoElement" class="w-full h-64 bg-slate-100 rounded-lg object-cover"
                            autoplay></video>
                        <canvas x-ref="canvasElement" class="hidden"></canvas>
                    </div>

                    <div class="flex gap-3">
                        <button @click="closeCamera()"
                            class="flex-1 px-4 py-2 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50">Cancel</button>
                        <button @click="capturePhoto()" :disabled="isProcessing"
                            class="flex-1 px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:from-green-700 hover:to-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center justify-center gap-2">
                            <svg x-show="isProcessing" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10" class="opacity-25" />
                                <path d="M4 12a8 8 0 018-8" class="opacity-75" />
                            </svg>
                            <span x-text="isProcessing ? 'Processing...' : 'Capture'"></span>
                        </button>
                    </div>
                </div>
            </div>

            <template x-if="isMatchingLoader">
                <div class="fixed inset-0 z-[9999] backdrop-blur-lg flex items-center justify-center">
                    <div class="text-center bg-white/95 rounded-2xl px-8 py-6 shadow-2xl border border-white/20">
                        <div class="inline-flex items-center justify-center mb-4">
                            <svg class="animate-spin h-12 w-12 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                        <p class="text-lg font-medium text-slate-700">Matching your photo...</p>
                        <p class="text-sm text-slate-500 mt-2">Please wait while we find your photos</p>
                    </div>
                </div>
            </template>
        </div>
    @endif
@endsection

@section('scripts')
    <!-- libphonenumber CDN -->
    <script src="https://cdn.jsdelivr.net/npm/libphonenumber-js@1.10.58/bundle/libphonenumber-min.js"></script>
    <script>
        function eventPage(uuid) {
            return {
                uuid,
                showUnlockStep: false,
                showCamera: false,
                showPhoneModal: false,
                // showOTPInput: false, // Commented out - OTP flow disabled
                phoneNumber: '',
                // otpCode: '', // Commented out - OTP flow disabled
                // isVerifyingOTP: false, // Commented out - OTP flow disabled
                isSendingOTP: false,
                otpErrorMessage: '',
                // otpSuccessMessage: '', // Commented out - OTP flow disabled
                stream: null,
                matchedPhotos: [],
                showNoMatches: false,
                isProcessing: false,
                isMatchingLoader: false,
                successMessage: '',
                errorMessage: '',
                infoMessage: '',
                isCheckingVerification: true,
                hasCheckedOtp: false,
                // Pagination state
                matchedPhotosPage: 1,
                matchedPhotosHasMore: false,
                matchedPhotosLoading: false,
                hasActiveSession: false,

                init() {
                    if (!this.hasCheckedOtp) {
                        this.hasCheckedOtp = true;
                        this.checkOtpVarification().finally(() => {
                            this.isCheckingVerification = false;
                        });
                    }
                },

                async checkOtpVarification() {
                    try {
                        const url = '{{ route('face_finder.public.otp_verified', ['uuid' => $uuid]) }}';
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        if (response.ok) {
                            const data = await response.json();
                            if (data.verified) {
                                this.showUnlockStep = true;
                                this.hasActiveSession = true;

                                // Set matched photos from backend - await it to prevent flicker
                                await this.loadMatchedPhotos();
                            } else {
                                this.hasActiveSession = false;
                            }

                            return data.verified;
                        } else {
                            this.hasActiveSession = false;
                        }
                    } catch (e) {
                        console.warn('Failed to check OTP verification');
                        this.hasActiveSession = false;
                    }
                },

                async loadMatchedPhotos(reset = true) {
                    if (reset) {
                        this.matchedPhotos = [];
                        this.matchedPhotosPage = 1;
                    }

                    this.setInfo('Loading your matched photo…');

                    try {
                        const url = `{{ route('face_finder.public.matched_photos', ['uuid' => $uuid]) }}?page=${this.matchedPhotosPage}`;
                        const response = await fetch(url, {
                            method: 'GET',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            credentials: 'include'
                        });

                        if (response.ok) {
                            const data = await response.json();
                            if (data.success && data.matched_photos) {
                                this.matchedPhotos = data.matched_photos;
                                this.matchedPhotosHasMore = data.has_more || false;
                            }
                        }
                    } catch (e) {
                        console.warn('Failed to load matched photos');
                    } finally {
                        this.infoMessage = '';
                    }
                },

                async loadMoreMatchedPhotos() {
                    if (this.matchedPhotosLoading || !this.matchedPhotosHasMore) return;

                    this.matchedPhotosLoading = true;
                    this.matchedPhotosPage++;

                    try {
                        const url = `{{ route('face_finder.public.matched_photos', ['uuid' => $uuid]) }}?page=${this.matchedPhotosPage}`;
                        const response = await fetch(url, {
                            method: 'GET',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            credentials: 'include'
                        });

                        if (response.ok) {
                            const data = await response.json();
                            if (data.success && data.matched_photos) {
                                // Append new photos to existing array
                                this.matchedPhotos = [...this.matchedPhotos, ...data.matched_photos];
                                this.matchedPhotosHasMore = data.has_more || false;
                            }
                        }
                    } catch (e) {
                        console.warn('Failed to load more matched photos');
                    } finally {
                        this.matchedPhotosLoading = false;
                    }
                },

                async logOtpAttempt(phoneNumber) {
                    try {
                        const url = '{{ route('face_finder.public.otp_attempt', ['uuid' => $uuid]) }}';
                        const payload = {
                            phone_number: phoneNumber || null,
                        };
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(payload)
                        });
                    } catch (e) {
                        console.warn('Failed to log OTP attempt');
                    }
                },

                openPhoneModal() {
                    this.showPhoneModal = true;
                    // this.showOTPInput = false; // Commented out - OTP flow disabled
                    // this.otpCode = ''; // Commented out - OTP flow disabled
                },

                closePhoneModal() {
                    this.showPhoneModal = false;
                    // this.showOTPInput = false; // Commented out - OTP flow disabled
                    // this.otpCode = ''; // Commented out - OTP flow disabled
                    this.clearOtpMessages();
                },

                // Direct submit without OTP
                async submitPhoneNumber() {
                    // Clear previous messages
                    this.clearOtpMessages();

                    // Check phone validation
                    if (!this.phoneNumber || this.otpErrorMessage) {
                        this.otpErrorMessage = this.otpErrorMessage || 'Please enter a valid phone number.';
                        return;
                    }

                    this.isSendingOTP = true;
                    try {
                        // Log phone number attempt
                        await this.logOtpAttempt('+91' + this.phoneNumber);

                        // Set session as active (cookie is set by backend)
                        this.hasActiveSession = true;

                        // Close modal and show camera directly
                        this.closePhoneModal();
                        this.showUnlockStep = true;
                        this.openCamera();
                    } catch (e) {
                        console.error('Failed to submit phone number', e);
                        this.otpErrorMessage = 'Failed to submit. Please try again.';
                    } finally {
                        this.isSendingOTP = false;
                    }
                },

                // Original OTP flow functions (commented out - can be enabled in future)
                // async sendOTP() {
                //     // Clear previous messages
                //     this.clearOtpMessages();
                //
                //     // Check phone validation
                //     if (!this.phoneNumber || this.otpErrorMessage) {
                //         this.otpErrorMessage = this.otpErrorMessage || 'Please enter a valid phone number.';
                //         return;
                //     }
                //
                //     this.isSendingOTP = true;
                //     try {
                //         // Optionally call backend to send OTP here
                //         // For now, simulate a short delay and success
                //         await new Promise(r => setTimeout(r, 600));
                //         this.otpSuccessMessage = 'OTP sent successfully. Please check your phone.';
                //         this.otpErrorMessage = '';
                //
                //         // Move to OTP input step
                //         this.showOTPInput = true;
                //     } catch (e) {
                //         console.error('Failed to send OTP', e);
                //         this.otpErrorMessage = 'Failed to send OTP. Please try again.';
                //         this.otpSuccessMessage = '';
                //     } finally {
                //         this.isSendingOTP = false;
                //     }
                // },
                //
                // async verifyOTP() {
                //     if (!this.otpCode || this.otpCode.length !== 6) {
                //         this.otpErrorMessage = 'Please enter a valid 6-digit OTP';
                //         this.otpSuccessMessage = '';
                //         return;
                //     }
                //
                //     this.isVerifyingOTP = true;
                //     this.clearOtpMessages();
                //
                //     try {
                //         // Static OTP check
                //         if (this.otpCode !== '123456') {
                //             throw new Error('invalid_static_otp');
                //         }
                //
                //         this.otpSuccessMessage = 'OTP verified successfully!';
                //         this.otpErrorMessage = '';
                //
                //         this.logOtpAttempt('+91' + this.phoneNumber);
                //
                //         // Close modal and show camera
                //         this.closePhoneModal();
                //         this.showUnlockStep = true;
                //         this.openCamera();
                //
                //     } catch (error) {
                //         console.error('Error verifying OTP:', error);
                //         this.otpErrorMessage = 'Invalid OTP. Please check and try again.';
                //         this.otpSuccessMessage = '';
                //     } finally {
                //         this.isVerifyingOTP = false;
                //     }
                // },
                //
                // backToPhoneInput() {
                //     this.showOTPInput = false;
                //     this.otpCode = '';
                //     this.clearOtpMessages();
                // },

                validatePhoneNumber() {
                    this.otpErrorMessage = '';

                    if (this.phoneNumber && this.phoneNumber.length > 0) {
                        this.phoneNumber = this.phoneNumber.replace(/\D/g, '');

                        // Expect 10-digit Indian mobile number
                        if (this.phoneNumber.length !== 10) {
                            this.otpErrorMessage = 'Please enter a valid 10-digit Indian phone number';
                            return;
                        }

                        try {
                            const phoneNumber = libphonenumber.parsePhoneNumber('+91' + this.phoneNumber, 'IN');
                            if (!phoneNumber || !phoneNumber.isValid()) {
                                this.otpErrorMessage = 'Invalid phone number';
                            }
                        } catch (error) {
                            this.otpErrorMessage = 'Invalid phone number';
                        }
                    }
                },

                async openCamera() {
                    try {
                        this.stream = await navigator.mediaDevices.getUserMedia({
                            video: {
                                facingMode: 'user',
                                width: {
                                    ideal: 640
                                },
                                height: {
                                    ideal: 480
                                }
                            }
                        });
                        this.$refs.videoElement.srcObject = this.stream;
                        this.showCamera = true;
                    } catch (error) {
                        console.error('Error accessing camera:', error);
                        this.setError('Unable to access camera. Please check permissions.');
                    }
                },

                closeCamera() {
                    this.showCamera = false;
                    if (this.stream) {
                        this.stream.getTracks().forEach(track => track.stop());
                        this.stream = null;
                    }
                },

                async capturePhoto() {
                    const video = this.$refs.videoElement;
                    const canvas = this.$refs.canvasElement;
                    const context = canvas.getContext('2d');

                    // Set canvas dimensions to match video
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;

                    // Draw the video frame to canvas
                    context.drawImage(video, 0, 0, canvas.width, canvas.height);

                    // Convert canvas to blob
                    canvas.toBlob(async (blob) => {
                        if (blob) {
                            await this.processCapturedPhoto(blob);
                        }
                    }, 'image/jpeg', 0.8);

                    this.closeCamera();
                },

                async processCapturedPhoto(blob) {
                    this.isProcessing = true;
                    this.matchedPhotos = [];
                    this.matchedPhotosPage = 1;
                    this.matchedPhotosHasMore = false;
                    this.showNoMatches = false;
                    this.clearMessages();

                    // Show full-page loader
                    this.isMatchingLoader = true;
                    // Small delay to ensure DOM updates
                    await new Promise(resolve => setTimeout(resolve, 10));

                    try {
                        const formData = new FormData();
                        formData.append('photo', blob, 'captured-photo.jpg');
                        formData.append('event_uuid', this.uuid);

                        const response = await fetch('{{ route('face_finder.public.find_photos') }}', {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: formData,
                            credentials: 'include'
                        });

                        if (!response.ok) {
                            throw new Error('Failed to process photo');
                        }

                        const result = await response.json();

                        if (result.success && result.matched_photos && result.matched_photos.length > 0) {
                            this.showNoMatches = false;
                            await this.loadMatchedPhotos();
                        } else {
                            this.matchedPhotos = [];
                            this.showNoMatches = true;
                            this.setInfo('No matching photos found for this image. Try another angle or better lighting.');
                        }

                    } catch (error) {
                        console.error('Error sending photo:', error);
                        this.matchedPhotos = [];
                        this.showNoMatches = true;
                        this.setError('Error processing photo. Please try again.');
                    } finally {
                        this.isProcessing = false;
                        this.isMatchingLoader = false;
                    }
                },

                async downloadMatchedPhotosZip() {
                    if (!this.matchedPhotos || this.matchedPhotos.length === 0) {
                        this.setError('No matched photos to download');
                        return;
                    }

                    try {
                        this.setInfo('Preparing ZIP file...');

                        const photoIds = this.matchedPhotos.map(photo => photo.id);

                        // Start ZIP preparation
                        const response = await fetch('{{ route('face_finder.public.download_matched_photos_zip', ['uuid' => $uuid]) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                photo_ids: photoIds
                            })
                        });

                        if (!response.ok) {
                            throw new Error('Failed to start ZIP preparation');
                        }

                        const result = await response.json();

                        if (result.status === 'processing') {
                            this.setInfo('ZIP file is being prepared. Please wait a moment and try downloading again.');
                        }

                    } catch (error) {
                        console.error('Error starting ZIP preparation:', error);
                        this.setError('Failed to start ZIP preparation. Please try again.');
                    }
                },

                isDesktopDevice() {
                    return window.matchMedia("(min-width: 1025px)").matches;
                },

                downloadAllPhotos() {
                    if (!this.matchedPhotos || this.matchedPhotos.length === 0) {
                        this.setError('No matched photos to download');
                        return;
                    }

                    // Create a temporary download link for each photo and click it.
                    this.matchedPhotos.forEach((photo, index) => {
                        setTimeout(() => {
                            try {
                                const link = document.createElement('a');
                                link.href = photo.src;
                                link.download = photo.filename || `photo-${index + 1}.jpg`;
                                link.style.display = 'none';
                                document.body.appendChild(link);
                                link.click();
                                document.body.removeChild(link);
                            } catch (e) {
                                console.warn('Failed to trigger download for a photo', e);
                            }
                        }, index * 300);
                    });
                },

                shareOnWhatsApp() {
                    if (!this.matchedPhotos || this.matchedPhotos.length === 0) {
                        this.setError('No matched photos to share');
                        return;
                    }

                    const checkUrl = `{{ route('face_finder.public.whatsapp_request_check', ['uuid' => $uuid]) }}`;

                    fetch(checkUrl, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        credentials: 'include',
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.allowed) {
                            this.setError(data.message || 'Cannot make request now.');
                            return;
                        }

                        const phoneNumber = '919429533400';
                        const code = @json(env('WHATSAPP_CODE_FOR_REQUEST_PHOTOS'));
                        const message =
                            `Please send the following message as it is to request your photos:\n` +
                            `code: ${code}`;
                        const whatsappUrl = `https://wa.me/${phoneNumber}?text=${encodeURIComponent(message)}`;

                        window.open(whatsappUrl, '_blank');
                    })
                    .catch(() => {
                        this.setError('Cannot make request now.');
                    });
                },

                // Message helpers
                clearMessages() {
                    this.successMessage = '';
                    this.errorMessage = '';
                    this.infoMessage = '';
                },
                clearOtpMessages() {
                    this.otpErrorMessage = '';
                    // this.otpSuccessMessage = ''; // Commented out - OTP flow disabled
                },
                setSuccess(msg) {
                    this.successMessage = msg;
                    setTimeout(() => {
                        if (this.successMessage === msg) this.successMessage = '';
                    }, 4000);
                },
                setError(msg) {
                    this.errorMessage = msg;
                },
                setInfo(msg) {
                    this.infoMessage = msg;
                    setTimeout(() => {
                        if (this.infoMessage === msg) this.infoMessage = '';
                    }, 10000);
                },

            };
        }
    </script>
@endsection
