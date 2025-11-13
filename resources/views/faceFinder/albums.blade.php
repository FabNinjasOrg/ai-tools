@extends('faceFinder.layout.sidebar-layout')

@section('page-title', 'All Albums')

@section('content')
    <div x-data="albumsPage()" x-init="init()" x-cloak class="max-w-7xl mx-auto px-6">
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-slate-900 mb-4">All Albums</h1>
                <button @click="showCreateAlbumModal = true"
                    class="px-4 py-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white hover:from-blue-700 hover:to-indigo-700 font-medium text-sm inline-flex items-center gap-2 transition-all shadow-md hover:shadow-lg">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Create Album
                </button>
            </div>

            <!-- Filter Section -->
                <div class="flex items-center gap-3">
                    <select x-model="selectedEventId" @change="loadAlbums()"
                        class="flex-1 max-w-xs px-4 py-2 rounded-xl border-2 border-slate-200 focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">All Events</option>
                        <template x-for="event in events" :key="event.id">
                            <option :value="event.id" x-text="event.name"></option>
                        </template>
                    </select>
                    <button x-show="selectedEventId" @click="clearFilter()"
                        class="px-4 py-2 rounded-xl border-2 border-red-200 hover:border-red-300 hover:bg-red-50 text-red-600 font-medium text-sm inline-flex items-center gap-2 transition-all">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6">
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
                    <h3 class="text-lg font-semibold text-slate-900 mb-2">No albums found</h3>
                    <p class="text-slate-500 text-sm max-w-md">No albums match the selected filter.</p>
                </div>
            </template>

            <div x-show="!albumsLoading && albums.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <template x-for="album in albums" :key="album.id">
                    <div class="group bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden">
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
                            <p class="text-sm text-slate-500 truncate mb-2" x-text="album.event_name" :title="album.event_name"></p>
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
                                    View Album
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

        <!-- Include Create Album Modal -->
        @include('faceFinder.partials.create-album-modal')
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        function albumsPage() {
            return {
                albums: [],
                events: [],
                selectedEventId: '',
                albumsLoading: false,
                showCreateAlbumModal: {{ $errors->has('event_id') || $errors->has('album_name') ? 'true' : 'false' }},
                availableEvents: [],

                async init() {
                    await this.loadAlbums();
                },

                async loadAlbums() {
                    this.albumsLoading = true;
                    try {
                        let loadAlbumsUrl = '{{ route('face_finder.load_albums') }}';

                        // Add event_id parameter if selected
                        if (this.selectedEventId) {
                            loadAlbumsUrl += '?event_id=' + this.selectedEventId;
                        }

                        const response = await fetch(loadAlbumsUrl, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (!response.ok) throw new Error('Failed to load albums');
                        const data = await response.json();
                        this.albums = Array.isArray(data.albums) ? data.albums : [];
                        this.events = Array.isArray(data.events) ? data.events : [];
                        this.availableEvents = Array.isArray(data.events) ? data.events : [];
                    } catch (e) {
                        console.error(e);
                        this.$store.messages.showError('Failed to load albums. Please try again.');
                    } finally {
                        this.albumsLoading = false;
                    }
                },

                clearFilter() {
                    this.selectedEventId = '';
                    this.loadAlbums();
                }
            };
        }
    </script>
@endsection

