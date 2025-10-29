@extends('faceFinder.app')

@section('content')
    <div class="max-w-7xl mx-auto px-6 py-12">
        <div class="mb-10 text-center">
            <h1
                class="text-3xl md:text-5xl font-bold bg-gradient-to-r from-green-600 via-emerald-600 to-teal-600 bg-clip-text text-transparent">
                Face Finder</h1>
            <p class="text-slate-600 mt-4 max-w-2xl mx-auto">Upload the photos and create the public url that helps peopple
                to find there photos easily !</p>
        </div>

        @if($isUserOnTrial)
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

        <div x-data="faceFinderApp()" x-init="loadAlbums()" x-cloak class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div>
                <div class="bg-white rounded-2xl border border-slate-200">
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
                    <div class="p-5 space-y-4">
                        <input x-ref="zipInput" type="file" accept=".zip" @change="handleZipSelected($event)"
                            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-[15px] focus:outline-none focus:ring-2 focus:ring-emerald-400 bg-white" />
                        <div class="text-[12px] text-slate-500">Max 1 GB. Upload a ZIP file containing photos (png, jpg, jpeg, webp).</div>

                        <template x-if="fileErrorMessage">
                            <div class="text-[12px] text-red-600" x-text="fileErrorMessage"></div>
                        </template>

                        <template x-if="selectedZip">
                            <div class="border border-slate-200 rounded-xl overflow-hidden">
                                <div class="flex items-center justify-between px-4 py-2 bg-slate-50 rounded-xl">
                                    <div class="flex items-center gap-4 min-w-0">
                                        <div
                                            class="h-8 w-8 rounded-lg bg-slate-200 inline-flex items-center justify-center shrink-0">
                                            🗜️</div>
                                        <div class="min-w-0">
                                            <div class="text-sm font-medium text-slate-800 truncate"
                                                x-text="selectedZip.name"></div>
                                            <div class="text-[12px] text-slate-500"
                                                x-text="formatFileSize(selectedZip.size)"></div>
                                        </div>
                                    </div>
                                    <button class="text-slate-500 hover:text-red-600 ml-2"
                                        @click="clearSelectedZip()">Remove</button>
                                </div>
                            </div>
                        </template>

                        <div class="flex items-center justify-end gap-3">
                            <button type="button" @click="resetForm()" :disabled="isBusy"
                                class="px-4 py-2 rounded-xl transition-colors"
                                :class="isBusy ? 'bg-gray-100 border border-gray-200 text-gray-400 cursor-not-allowed' :
                                    'border border-slate-300 text-slate-700 hover:bg-slate-50'">Reset</button>
                            <button type="button" @click="proceedAlbum()" :disabled="isBusy || !canProceed"
                                class="px-5 py-2.5 rounded-xl font-medium inline-flex items-center gap-2 transition-colors disabled:cursor-not-allowed"
                                :class="(isBusy || !canProceed) ? 'bg-slate-200 text-slate-500' :
                                'bg-gradient-to-r from-green-600 to-emerald-600 text-white hover:from-green-700 hover:to-emerald-700'">
                                <svg x-cloak x-show="!isBusy" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 19V5m0 0l-5 5m5-5l5 5" />
                                </svg>
                                <svg x-cloak x-show="isBusy" class="h-5 w-5 animate-spin" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10" class="opacity-25" />
                                    <path d="M4 12a8 8 0 018-8" class="opacity-75" />
                                </svg>
                                <span x-text="isBusy ? 'Processing…' : 'Proceed'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden flex flex-col">
                    <div class="p-4 border-b border-slate-200 flex items-center justify-between shrink-0">
                        <div class="flex items-center gap-3">
                            <h3 class="text-sm font-semibold text-slate-900">Albums</h3>
                            <div class="text-[12px] text-slate-600 inline-flex items-center gap-2">
                                <span
                                    class="h-5 w-5 rounded-full bg-emerald-100 text-emerald-700 inline-flex items-center justify-center font-semibold">i</span>
                                <span class="hidden sm:inline">Navigate to the folder to generate a public link for sharing
                                    or viewing your photos.</span>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 flex-1 overflow-y-auto overscroll-contain">
                        <template x-if="successMessage">
                            <div class="mb-3 rounded-xl border border-green-200 border-l-4 border-l-green-400 bg-green-50 px-4 py-3 text-sm shadow-sm"
                                style="border-color: #4ade80;">
                                <div class="flex items-start gap-3 text-green-700">
                                    <div
                                        class="shrink-0 h-5 w-5 rounded-full bg-green-100 text-green-700 inline-flex items-center justify-center">
                                        ✓</div>
                                    <div class="flex-1" x-text="successMessage"></div>
                                </div>
                            </div>
                        </template>
                        <template x-if="errorMessage">
                            <div class="mb-3 rounded-xl border border-red-200 border-l-4 border-l-red-400 bg-red-50 px-4 py-3 text-sm shadow-sm relative"
                                style="border-color: #f87171;">
                                <button type="button" @click="errorMessage = ''" aria-label="Dismiss error"
                                    class="absolute top-2 right-2 text-red-500 hover:text-red-700">
                                    ×
                                </button>
                                <div class="flex items-start gap-3 text-red-700">
                                    <div
                                        class="shrink-0 h-5 w-5 rounded-full bg-red-100 text-red-700 inline-flex items-center justify-center">
                                        !</div>
                                    <div class="flex-1" x-text="errorMessage"></div>
                                </div>
                            </div>
                        </template>
                        <template x-if="albums.length === 0">
                            <div
                                class="w-full flex items-center justify-center text-center text-slate-500 text-sm py-10">
                                No albums yet. Upload a ZIP and click Proceed to add one.</div>
                        </template>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            <template x-for="album in albums" :key="album.id">
                                <div
                                    class="rounded-xl border border-slate-200 bg-white shadow-[0_1px_2px_rgba(0,0,0,0.04)] hover:shadow-md transition-shadow">
                                    <a :href="'{{ route('face_finder.albums.show', ['uuid' => 'UUID_PLACEHOLDER']) }}'.replace(
                                        'UUID_PLACEHOLDER', album.uuid)"
                                        class="p-4 block w-full hover:bg-slate-50 rounded-xl transition-colors">
                                        <div class="flex flex-col items-center text-center">
                                            <!-- Bigger, prettier folder icon -->
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" fill="none"
                                                class="h-16 w-16 mb-3">
                                                <defs>
                                                    <linearGradient id="ff-folder" x1="0%" y1="0%"
                                                        x2="100%" y2="100%">
                                                        <stop offset="0%" stop-color="#60A5FA" />
                                                        <stop offset="100%" stop-color="#2563EB" />
                                                    </linearGradient>
                                                </defs>
                                                <path
                                                    d="M5 14c0-2.209 1.791-4 4-4h10.343c.53 0 1.039.211 1.414.586l2.828 2.828c.375.375.884.586 1.414.586H39c2.209 0 4 1.791 4 4v18c0 2.209-1.791 4-4 4H9c-2.209 0-4-1.791-4-4V14z"
                                                    fill="url(#ff-folder)" />
                                                <path
                                                    d="M9 12h10.343c.53 0 1.039.211 1.414.586l2.828 2.828c.375.375.884.586 1.414.586H39c1.105 0 2 .895 2 2v2H7v-6c0-1.105.895-2 2-2z"
                                                    fill="#93C5FD" />
                                                <path d="M7 20h34v12c0 1.657-1.343 3-3 3H10c-1.657 0-3-1.343-3-3V20z"
                                                    fill="#EFF6FF" />
                                                <path d="M7 20h34" stroke="#1D4ED8" stroke-width="2" />
                                            </svg>
                                            <!-- Full file name below icon (wraps) -->
                                            <div class="text-[15px] font-semibold text-slate-900 leading-snug break-words w-full"
                                                x-text="album.name"></div>
                                            <!-- Size and count below name -->
                                            <div class="mt-1 text-[12px] text-slate-600 w-full">
                                                <span
                                                    x-text="album.count + ' photos | ' + formatFileSize(album.size)"></span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function faceFinderApp() {
            return {
                selectedZip: null,
                fileErrorMessage: '',
                errorMessage: '',

                canProceed: false,
                isBusy: false,
                maxBytes: 1024 * 1024 * 1024, // 1 GB
                albums: [],
                successMessage: '',

                handleZipSelected(event) {
                    const file = (event.target.files && event.target.files[0]) ? event.target.files[0] : null;
                    this.clearMessages();
                    this.canProceed = false;
                    this.selectedZip = null;

                    if (!file) return;

                    // Basic validation only - server will handle the rest
                    if (!/\.zip$/i.test(file.name)) {
                        this.fileErrorMessage = 'Only .zip files are allowed.';
                        if (this.$refs.zipInput) this.$refs.zipInput.value = '';
                        return;
                    }

                    if (file.size > this.maxBytes) {
                        this.fileErrorMessage = 'ZIP too large. Maximum allowed size is 1 GB.';
                        if (this.$refs.zipInput) this.$refs.zipInput.value = '';
                        return;
                    }

                    // File passed basic checks, allow proceed
                    this.selectedZip = file;
                    this.canProceed = true;
                },
                clearSelectedZip() {
                    this.selectedZip = null;
                    if (this.$refs.zipInput) this.$refs.zipInput.value = '';
                    this.clearMessages();
                    this.canProceed = false;
                },
                resetForm() {
                    this.selectedZip = null;
                    this.clearMessages();
                    this.canProceed = false;
                    this.isBusy = false;
                    this.$nextTick(() => {
                        if (this.$refs.zipInput) this.$refs.zipInput.value = '';
                    });
                },
                clearMessages() {
                    this.fileErrorMessage = '';
                    this.errorMessage = '';

                },
                formatFileSize(bytes) {
                    if (bytes < 1024) return bytes + ' B';
                    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
                    return (bytes / 1024 / 1024).toFixed(1) + ' MB';
                },
                async proceedAlbum() {
                    if (!this.canProceed || !this.selectedZip) return;
                    this.isBusy = true;
                    try {
                        const form = new FormData();
                        form.append('zip', this.selectedZip);
                        form.append('name', this.selectedZip.name.replace(/\.zip$/i, ''));

                        const res = await fetch("{{ route('face_finder.albums.store') }}", {
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
                            // await this.loadAlbums();
                            this.successMessage = `Album "${data.album.name || ''}" is uploading. Please wait...`;
                            if (data.album.uuid) {
                                localStorage.setItem('ff_uploading_album', JSON.stringify({ uuid: data.album.uuid, name: data.album.name || '' }));
                                this.pollUploadStatus(data.album.uuid);
                            }
                        }
                        this.resetForm();
                    } catch (e) {
                        this.errorMessage = e.message || 'Something went wrong.';
                    } finally {
                        this.isBusy = false;
                    }
                },
                async loadAlbums() {
                    try {
                        const res = await fetch("{{ route('face_finder.albums.index') }}", {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        if (!res.ok) throw new Error('Failed to load albums');
                        const data = await res.json();
                        this.albums = Array.isArray(data.albums) ? data.albums : [];
                        // Restore persistent uploading banner if present
                        try {
                            const pending = JSON.parse(localStorage.getItem('ff_uploading_album') || 'null');
                            if (pending && pending.uuid) {
                                this.successMessage = `Album "${pending.name || ''}" is uploading. Please wait...`;
                                this.pollUploadStatus(pending.uuid);
                            }
                        } catch (e) {}
                    } catch (e) {
                        console.error(e);
                    }
                },
                async getUploadStatus(uuid) {
                    const url = "{{ route('zipfileUploadStatus', ['uuid' => 'UUID_PLACEHOLDER']) }}".replace('UUID_PLACEHOLDER', uuid);
                    try {
                        const res = await fetch(url, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        if (!res.ok) return null;
                        const data = await res.json();
                        return data && data.upload_status ? data.upload_status : null;
                    } catch (e) {
                        return null;
                    }
                },
                async pollUploadStatus(uuid) {
                    const status = await this.getUploadStatus(uuid);

                    if (status === 'completed') {
                        localStorage.removeItem('ff_uploading_album');

                        await this.loadAlbums();

                        this.successMessage = 'Album processing completed.';

                        setTimeout(() => { this.successMessage = ''; }, 5000);
                        return;
                    }
                    setTimeout(() => this.pollUploadStatus(uuid), 5000);
                }
            }
        }
    </script>
@endsection
