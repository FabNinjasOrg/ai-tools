@extends('faceFinder.layout.sidebar-layout')

@section('page-title', 'Your Albums')

@section('content')
    <div class="max-w-7xl mx-auto px-6">
        <div x-data="albumsListApp()" x-init="loadAlbums()" x-cloak>
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden flex flex-col shadow-sm">
                <div class="p-5 border-b border-slate-200 flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white inline-flex items-center justify-center">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">All Albums</h2>
                            <p class="text-xs text-slate-500" x-text="albums.length + ' album' + (albums.length !== 1 ? 's' : '')"></p>
                        </div>
                    </div>
                    <div class="text-[12px] text-slate-600 hidden lg:inline-flex items-center gap-2">
                        <span class="h-6 w-6 rounded-full bg-emerald-100 text-emerald-700 inline-flex items-center justify-center font-semibold text-xs">i</span>
                        <span>Click on any album to view details, generate a public link, or manage photos.</span>
                    </div>
                </div>

                <div class="p-6 flex-1 overflow-y-auto">
                    <!-- Loading State -->
                    <template x-if="isLoading">
                        <div class="w-full flex flex-col items-center justify-center text-center py-16">
                            <div class="h-12 w-12 rounded-full border-4 border-slate-200 border-t-emerald-600 animate-spin mb-4"></div>
                            <p class="text-slate-500 text-sm">Loading your albums...</p>
                        </div>
                    </template>

                    <!-- Empty State -->
                    <template x-if="!isLoading && albums.length === 0">
                        <div class="w-full flex flex-col items-center justify-center text-center py-16">
                            <div class="h-20 w-20 rounded-2xl bg-slate-100 inline-flex items-center justify-center mb-4">
                                <svg class="h-10 w-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-slate-900 mb-2">No albums yet</h3>
                            <p class="text-slate-500 text-sm mb-6 max-w-md">You haven't created any albums yet. Upload a ZIP file to get started!</p>
                            <a href="{{ route('face_finder.upload_album') }}"
                               class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-green-600 to-emerald-600 text-white hover:from-green-700 hover:to-emerald-700 transition-all inline-flex items-center gap-2 shadow-md hover:shadow-lg">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                Upload Your First Album
                            </a>
                        </div>
                    </template>

                    <!-- Albums Grid -->
                    <div x-show="!isLoading && albums.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        <template x-for="album in albums" :key="album.id">
                            <div class="rounded-xl border border-slate-200 bg-white shadow-sm hover:shadow-md transition-shadow">
                                <a :href="'{{ route('face_finder.albums.show', ['uuid' => 'UUID_PLACEHOLDER']) }}'.replace('UUID_PLACEHOLDER', album.uuid)"
                                   class="p-5 block w-full hover:bg-slate-50 rounded-xl transition-colors">
                                    <div class="flex flex-col items-center text-center">
                                        <!-- Folder Icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" fill="none" class="h-20 w-20 mb-4">
                                            <defs>
                                                <linearGradient id="ff-folder" x1="0%" y1="0%" x2="100%" y2="100%">
                                                    <stop offset="0%" stop-color="#60A5FA" />
                                                    <stop offset="100%" stop-color="#2563EB" />
                                                </linearGradient>
                                            </defs>
                                            <path d="M5 14c0-2.209 1.791-4 4-4h10.343c.53 0 1.039.211 1.414.586l2.828 2.828c.375.375.884.586 1.414.586H39c2.209 0 4 1.791 4 4v18c0 2.209-1.791 4-4 4H9c-2.209 0-4-1.791-4-4V14z"
                                                  fill="url(#ff-folder)" />
                                            <path d="M9 12h10.343c.53 0 1.039.211 1.414.586l2.828 2.828c.375.375.884.586 1.414.586H39c1.105 0 2 .895 2 2v2H7v-6c0-1.105.895-2 2-2z"
                                                  fill="#93C5FD" />
                                            <path d="M7 20h34v12c0 1.657-1.343 3-3 3H10c-1.657 0-3-1.343-3-3V20z" fill="#EFF6FF" />
                                            <path d="M7 20h34" stroke="#1D4ED8" stroke-width="2" />
                                        </svg>
                                        <!-- Album Name -->
                                        <div class="text-base font-semibold text-slate-900 leading-snug break-words w-full mb-2" x-text="album.name"></div>
                                        <!-- Album Info -->
                                        <div class="text-xs text-slate-500 w-full">
                                            <span x-text="album.count + ' photos'"></span>
                                            <span class="mx-1">•</span>
                                            <span x-text="formatFileSize(album.size)"></span>
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
@endsection

@section('scripts')
    <script>
        function albumsListApp() {
            return {
                albums: [],
                isLoading: true,

                async loadAlbums() {
                    this.isLoading = true;

                    // Check for pending upload before loading albums
                    try {
                        const pending = JSON.parse(localStorage.getItem('ff_uploading_album') || 'null');
                        if (pending && pending.uuid) {
                            this.$store.messages.showInfo(`Album "${pending.name || ''}" is uploading. Please wait...`, 0);
                        }
                    } catch (e) {}

                    try {
                        const res = await fetch("{{ route('face_finder.albums.index') }}", {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        if (!res.ok) throw new Error('Failed to load albums');
                        const data = await res.json();
                        this.albums = Array.isArray(data.albums) ? data.albums : [];

                        // Check for upload completion message in localStorage
                        try {
                            const pending = JSON.parse(localStorage.getItem('ff_uploading_album') || 'null');
                            if (pending && pending.uuid) {
                                this.checkUploadStatus(pending.uuid, pending.name);
                            }
                        } catch (e) {}
                    } catch (e) {
                        console.error(e);
                        this.$store.messages.showError('Failed to load albums. Please refresh the page.');
                    } finally {
                        this.isLoading = false;
                    }
                },

                async checkUploadStatus(uuid, name) {
                    const url = "{{ route('zipfileUploadStatus', ['uuid' => 'UUID_PLACEHOLDER']) }}".replace('UUID_PLACEHOLDER', uuid);
                    try {
                        const res = await fetch(url, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        if (!res.ok) return;
                        const data = await res.json();
                        const status = data && data.upload_status ? data.upload_status : null;

                        if (status === 'completed') {
                            localStorage.removeItem('ff_uploading_album');
                            await this.loadAlbums();
                            this.$store.messages.showSuccess(`Album "${name}" has been successfully processed!`, 5000);
                        } else if (status === 'processing' || status === 'pending') {
                            this.$store.messages.showInfo(`Album "${name}" is still processing...`, 0);
                            setTimeout(() => this.checkUploadStatus(uuid, name), 5000);
                        }
                    } catch (e) {
                        console.error(e);
                    }
                },

                formatFileSize(bytes) {
                    if (bytes < 1024) return bytes + ' B';
                    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
                    return (bytes / 1024 / 1024).toFixed(1) + ' MB';
                }
            }
        }
    </script>
@endsection

