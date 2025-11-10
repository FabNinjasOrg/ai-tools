@extends('faceFinder.layout.sidebar-layout')

@section('page-title', 'Upload Photos')

@section('content')
    <div class="max-w-7xl mx-auto px-6">
        @if(isUserOnTrial())
            <div class="mb-8 rounded-2xl border border-amber-200 bg-gradient-to-r from-amber-50 to-orange-50 shadow-lg p-6 relative">
                <button class="absolute top-6 right-6 px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg font-semibold text-sm hover:from-green-700 hover:to-emerald-700 transition-all shadow-md hover:shadow-lg">
                    Upgrade Now
                </button>
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
                            <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 text-xs font-semibold">TRIAL</span>
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
                                    <span class="font-semibold">Single Event</span>
                                    <p class="text-xs text-amber-700 mt-0.5">Only one event creation</p>
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
                                    <p class="text-xs text-amber-700 mt-0.5">Maximum photos per event</p>
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
        @elseif(userSubscribedButPaymentPending())
            <div class="mb-8 rounded-2xl border border-yellow-200 bg-gradient-to-r from-yellow-50 to-yellow-100 shadow-lg p-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 rounded-full bg-gradient-to-br from-yellow-400 to-yellow-500 flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-yellow-900 mb-2">Payment Pending</h3>
                        <p class="text-yellow-800 text-sm">Your subscription is active but payment is pending. Please complete the payment to enjoy uninterrupted access to all features.</p>
                        <div class="mt-4">
                            <a href="{{ route('face_finder.manage_subscription') }}"
                               class="inline-block px-4 py-2 rounded-lg bg-gradient-to-r from-green-600 to-emerald-600 text-white font-semibold text-sm hover:from-green-700 hover:to-emerald-700 transition-colors shadow-md hover:shadow-lg">
                                Pay Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div x-data="manageUploadPhotos()" x-cloak>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
                    <div class="p-5 border-b border-slate-200 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                class="h-10 w-10 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 text-white inline-flex items-center justify-center">
                                FF</div>
                            <div>
                                <h2 class="text-base font-semibold text-slate-900">Upload ZIP</h2>
                            </div>
                        </div>
                    </div>
                    <div class="p-5">
                        <!-- Uppy Dashboard Container -->
                        <div x-ref="uppyContainer"></div>

                        <!-- Upload Button -->
                        <div class="mt-4 flex items-center justify-end">
                            <button type="button" @click="proceedPhotos()"
                                class="px-6 py-3 rounded-xl font-semibold inline-flex items-center gap-2 transition-all bg-gradient-to-r from-green-600 to-emerald-600 text-white hover:from-green-700 hover:to-emerald-700 shadow-md hover:shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 19V5m0 0l-5 5m5-5l5 5" />
                                </svg>
                                <span>Upload Event</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function manageUploadPhotos() {
            let uppyManager = null;

            return {
                init() {
                    const self = this;
                    uppyManager = new window.UppyUploadManager({
                        container: this.$refs.uppyContainer,
                        inline: true,
                        hideUploadButton: true,
                        onError: (message) => {
                            self.$store.messages.showError(message);
                        }
                    });
                },

                async proceedPhotos() {
                    if (!uppyManager) return;

                    const files = uppyManager.getFiles();
                    if (files.length === 0) {
                        this.$store.messages.showError('Please select files to upload.');
                        return;
                    }

                    this.$store.messages.clear();

                    try {
                        const form = new FormData();

                        // Separate zip files and photo files
                        files.forEach((file) => {
                            if (!file) return;

                            let fileData = file.data;

                            if (!fileData && file instanceof File) {
                                fileData = file;
                            }

                            if (fileData) {
                                const fileName = file.name || 'file';
                                const fileExtension = fileName.toLowerCase().split('.').pop();

                                // Check if it's a zip file
                                if (fileExtension === 'zip') {
                                    form.append('zips[]', fileData, fileName);
                                } else {
                                    // It's a photo file
                                    form.append('photos[]', fileData, fileName);
                                }
                            }
                        });

                        const res = await fetch("{{ route('face_finder.events.store') }}", {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: form
                        });

                        if (!res.ok) {
                            const data = await res.json().catch(() => ({}));
                            throw new Error(data.message || 'Upload failed');
                        }

                        const data = await res.json();
                        if (data && data.album) {
                            this.$store.messages.showSuccess('Photos uploaded successfully! Processing photos...', 0);

                            // Store upload status for tracking
                            if (data.album.uuid) {
                                localStorage.setItem('ff_uploading_event', JSON.stringify({
                                    uuid: data.album.uuid,
                                    name: data.album.name || ''
                                }));
                            }

                            setTimeout(() => {
                                window.location.href = "{{ route('face_finder.events.index') }}";
                            }, 2000);
                        }

                        // Reset uppy for next upload
                        uppyManager.reset();
                    } catch (e) {
                        this.$store.messages.showError(e.message || 'Something went wrong.');
                    }
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const hasCountryCode = {{ auth()->user()->country_code ? 'true' : 'false' }};

            if (!hasCountryCode) {
                detectAndSaveCountryCode();
            }

            async function detectAndSaveCountryCode() {
                try {
                    const response = await fetch('https://ipapi.co/json/');
                    const data = await response.json();

                    if (data.country_code) {
                        await saveCountryCode(data.country_code);
                    }
                } catch (error) {
                    console.log('Could not detect country code:', error);
                }
            }

            async function saveCountryCode(countryCode) {
                try {
                    const response = await fetch('{{ route('face_finder.update_country_code') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            country_code: countryCode
                        })
                    });

                    const result = await response.json();
                    console.log('Country code saved:', result);
                } catch (error) {
                    console.error('Failed to save country code:', error);
                }
            }
        });
    </script>
@endsection
