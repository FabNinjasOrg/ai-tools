@extends('faceFinder.layout.sidebar-layout')

@section('page-title', 'Event Details')

@section('content')
    <div x-data="eventPage('{{ $uuid }}')" x-cloak class="max-w-7xl mx-auto px-6">
        <div class="mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4">
            <div class="flex items-center gap-3 text-lg md:text-lg">
                <a href="{{ route('face_finder.events.index') }}" class="flex items-center gap-2 text-slate-600 hover:text-green-600 transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Events
                </a>
                <span class="text-slate-400">></span>
                <h1 class="flex items-center gap-2 text-slate-900" x-text="eventName"></h1>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="mb-6 rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex border-b border-slate-200">
                <button @click="activeTab = 'albums'"
                    :class="activeTab === 'albums' ? 'text-green-600 border-b-2 border-green-600' : 'text-slate-600 hover:text-slate-900'"
                    class="px-6 py-4 font-medium text-sm transition-colors">
                    Albums
                </button>
                <button @click="activeTab = 'links'"
                    :class="activeTab === 'links' ? 'text-green-600 border-b-2 border-green-600' : 'text-slate-600 hover:text-slate-900'"
                    class="px-6 py-4 font-medium text-sm transition-colors">
                    Public Link
                </button>
                <button @click="activeTab = 'analytics'"
                    :class="activeTab === 'analytics' ? 'text-green-600 border-b-2 border-green-600' : 'text-slate-600 hover:text-slate-900'"
                    class="px-6 py-4 font-medium text-sm transition-colors">
                    Analytics
                </button>
            </div>

            <!-- Tab: Albums -->
            <div x-show="activeTab === 'albums'" class="p-6">
                    <div>
                        <!-- Create Album Button -->
                        <div class="mb-4 flex justify-end">
                            <button @click="showCreateAlbumModal = true"
                                class="px-4 py-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white hover:from-blue-700 hover:to-indigo-700 font-medium text-sm inline-flex items-center gap-2 transition-all shadow-md hover:shadow-lg">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                                Create Album
                            </button>
                        </div>

                        <template x-if="albumsLoading">
                            <div class="w-full flex items-center gap-3 text-slate-600">
                                <div class="h-5 w-5 rounded-full border-2 border-slate-200 border-t-emerald-600 animate-spin"></div>
                                <span class="text-sm">Loading albums...</span>
                            </div>
                        </template>
                        <template x-if="!albumsLoading && albums.length === 0">
                            <div class="w-full flex flex-col items-center justify-center text-center py-16">
                                <div class="h-20 w-20 rounded-2xl bg-slate-100 inline-flex items-center justify-center mb-4">
                                    <svg class="h-10 w-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h4l2-2h6l2 2h4v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-slate-900 mb-2">No albums yet</h3>
                                <p class="text-slate-500 text-sm max-w-md">Create albums for better organization.</p>
                            </div>
                        </template>
                        <div x-show="!albumsLoading && albums.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                            <template x-for="album in albums" :key="album.id">
                                <div class="group relative bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden">
                                    <form
                                        :action="'{{ route('face_finder.albums.delete', ['id' => 'ALBUM_ID']) }}'.replace('ALBUM_ID', album.id)"
                                        method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this album? This cannot be undone.');"
                                        class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 focus-within:opacity-100 transition-opacity duration-200 z-10"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                @click.stop
                                                class="p-2 rounded-full bg-white/90 text-red-600 border border-red-100 shadow-sm hover:bg-red-50 hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-red-200"
                                                title="Delete album">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                    <!-- Album Header with Folder Icon -->
                                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-8 flex flex-col items-center">
                                        <div class="relative">
                                            <div class="absolute inset-0 bg-blue-400 rounded-2xl blur-xl opacity-20 group-hover:opacity-30 transition-opacity"></div>
                                            <svg class="relative h-24 w-24 text-blue-500 drop-shadow-lg" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M10 4H4c-1.11 0-2 .89-2 2v12c0 1.097.903 2 2 2h16c1.097 0 2-.903 2-2V8c0-1.11-.9-2-2-2h-8l-2-2z"/>
                                            </svg>
                                        </div>
                                    </div>

                                    <!-- Album Info -->
                                    <div class="p-5">
                                        <h3 class="text-lg font-bold text-slate-900 truncate mb-1" x-text="album.name" :title="album.name"></h3>
                                        <div class="flex items-center gap-1.5 text-sm text-slate-500 mb-4">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span x-text="album.created_at ? new Date(album.created_at).toLocaleDateString() : ''"></span>
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="flex flex-col gap-2">
                                            <a :href="'{{ route('face_finder.albums.show', ['id' => 'ALBUM_ID']) }}'.replace('ALBUM_ID', album.id)"
                                                class="w-full px-4 py-2.5 rounded-xl bg-white border-2 border-blue-200 hover:border-blue-300 hover:bg-blue-50 text-blue-600 font-medium text-sm inline-flex items-center justify-center gap-2 transition-all">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                Show Album
                                            </a>

                                            <a :href="'{{ route('face_finder.albums.show', ['id' => 'ALBUM_ID']) }}?tab=links'.replace('ALBUM_ID', album.id)"
                                                class="w-full px-4 py-2.5 rounded-xl bg-white border-2 border-blue-200 hover:border-blue-300 hover:bg-blue-50 text-blue-600 font-medium text-sm inline-flex items-center justify-center gap-2 transition-all">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                                </svg>
                                                Manage Uploader Link
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
            </div>
            <!-- Tab 2: Links -->
            <div x-show="activeTab === 'links'" class="p-6">
                <!-- Public Share Link Section -->
                <div class="mb-6 rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="p-5 border-b border-slate-200">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 text-white inline-flex items-center justify-center">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900">Public Share Link</h2>
                                <p class="text-xs text-slate-500">Share this link to allow users to find their photos</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-5">
                        <template x-if="loadingUrl">
                            <div class="flex items-center gap-3 text-slate-600">
                                <div class="h-5 w-5 rounded-full border-2 border-slate-200 border-t-emerald-600 animate-spin"></div>
                                <span class="text-sm">Generating public URL...</span>
                            </div>
                        </template>
                        <template x-if="!loadingUrl && publicUrl">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                                <div class="flex-1 w-full">
                                    <input type="text" :value="publicUrl" readonly
                                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-700 font-mono">
                                </div>
                                <button @click="copyPublic()"
                                    class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-green-600 to-emerald-600 text-white hover:from-green-700 hover:to-emerald-700 transition-all inline-flex items-center gap-2 shadow-sm hover:shadow-md whitespace-nowrap">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                    Copy Link
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Tab 2: Analytics -->
            <div x-show="activeTab === 'analytics'" class="p-6">
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="p-5 border-b border-slate-200">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white inline-flex items-center justify-center">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900">Public URL Analytics</h2>
                                <p class="text-xs text-slate-500">Track how users interact with your shared event</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-gradient-to-r from-emerald-50 to-green-50 rounded-xl p-4 border border-emerald-200">
                                <div class="flex items-start gap-3">
                                    <div class="h-10 w-10 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0">
                                        <svg class="h-5 w-5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-xs text-emerald-700 font-medium mb-1">Successful Verifications</div>
                                        <div class="text-2xl font-bold text-emerald-800 tabular-nums">{{ $attemptTotal }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4 border border-blue-200">
                                <div class="flex items-start gap-3">
                                    <div class="h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center shrink-0">
                                        <svg class="h-5 w-5 text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-xs text-blue-700 font-medium mb-1">Unique Visitors</div>
                                        <div class="text-2xl font-bold text-blue-800 tabular-nums">{{ $attemptUniquePhones }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gradient-to-r from-red-50 to-orange-50 rounded-xl p-4 border border-red-200">
                                <div class="flex items-start gap-3">
                                    <div class="h-10 w-10 rounded-lg bg-red-100 flex items-center justify-center shrink-0">
                                        <svg class="h-5 w-5 text-red-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-xs text-red-700 font-medium mb-1">No Matches Found</div>
                                        <div class="text-2xl font-bold text-red-800 tabular-nums">{{ $noMatchCount }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Uppy Modal Container -->
        {{-- <div x-ref="uppyModalContainer"></div> --}}

        <!-- Include Create Album Modal -->
        @include('faceFinder.partials.create-album-modal')
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        function eventPage(uuid) {
            let uppyManager = null;

            return {
                // state
                uuid,
                eventName: '',
                photos: [],
                page: 0, // Start at 0, will be updated by pagination response
                perPage: 24,
                hasMore: true,
                loading: false,
                albums: [],
                albumsLoading: false,
                publicUrl: '',
                loadingUrl: false,
                loadingUploaderUrl: false,
                selectedPhotos: [],
                activeTab: 'albums', // Tab state: 'albums', 'links', or 'analytics'
                showCreateAlbumModal: {{ $errors->has('event_id') || $errors->has('album_name') ? 'true' : 'false' }},

                // lifecycle
                async init() {
                    // Load albums for this event
                    await this.loadAlbums();
                    // Auto-generate public URL if it doesn't exist
                    if (!this.publicUrl) {
                        await this.generatePublic();
                    }
                },

                openUploadModal() {
                    if (uppyManager) {
                        uppyManager.openModal();
                    }
                },

                async loadAlbums() {
                    this.albumsLoading = true;
                    try {
                        const loadAlbumUrl = `{{ route('face_finder.events.albums', ['uuid' => 'UUID_PLACEHOLDER']) }}`.replace('UUID_PLACEHOLDER', this.uuid);

                        const response = await fetch(loadAlbumUrl, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (!response.ok) throw new Error('Failed to load albums');
                        const data = await response.json();
                        this.albums = Array.isArray(data.albums) ? data.albums : [];
                        if (data.event_name) this.eventName = data.event_name;
                    } catch (e) {
                        console.error(e);
                        this.$store.messages.showError('Failed to load albums. Please try again.');
                    } finally {
                        this.albumsLoading = false;
                    }
                },

                async generatePublic() {
                    this.loadingUrl = true;
                    try {
                        const endpoint =
                            `{{ route('face_finder.events.generate_public', ['uuid' => 'UUID_PLACEHOLDER']) }}`.replace(
                                'UUID_PLACEHOLDER', this.uuid);
                        const res = await fetch(endpoint, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        if (!res.ok) throw new Error('Failed to generate URL');
                        const data = await res.json();
                        this.publicUrl = data.public_url || '';
                    } catch (error) {
                        console.error(error);
                        this.$store.messages.showError('Could not generate public URL. Please try again.');
                    } finally {
                        this.loadingUrl = false;
                    }
                },

                async copyPublic() {
                    try {
                        await navigator.clipboard.writeText(this.publicUrl);
                        this.$store.messages.showSuccess('Copied public URL to clipboard!');
                    } catch (_) {
                        this.$store.messages.showError('Failed to copy. Please copy manually.');
                    }
                },

                toggleSelection(photoId) {
                    const index = this.selectedPhotos.indexOf(photoId);
                    if (index > -1) {
                        this.selectedPhotos.splice(index, 1);
                    } else {
                        this.selectedPhotos.push(photoId);
                    }
                },

                // helpers
                getPhotosApiUrl() {
                    const base = `{{ route('face_finder.events.photos', ['uuid' => 'UUID_PLACEHOLDER']) }}`.replace(
                        'UUID_PLACEHOLDER', this.uuid);
                    const params = new URLSearchParams({
                        page: String(this.page + 1), // Next page for load more
                        per_page: String(this.perPage)
                    });
                    return `${base}?${params.toString()}`;
                },
            };
        }
    </script>
@endsection
