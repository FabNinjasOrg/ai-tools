@extends('faceFinder.layout.sidebar-layout')

@section('page-title', 'Album Details')

@section('content')
    <div x-data="albumPage({{ $albumId }}, '{{ $eventUuid }}', '{{ $albumName }}')" x-init="init()" x-cloak class="max-w-7xl mx-auto px-6">
        <div class="mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4">
            <div class="flex items-center gap-3 text-xl md:text-xl">
                <a href="{{ route('face_finder.events.index') }}"
                    class="text-slate-600 hover:text-green-600 transition-colors">
                    Events
                </a>
                >
                <a :href="'{{ route('face_finder.events.show', ['uuid' => 'UUID']) }}'.replace('UUID', eventUuid)"
                    class="text-slate-600 hover:text-green-600 transition-colors" x-text="eventName"></a>
                >
                <h1 x-text="albumName"></h1>
            </div>
        </div>

        <div class="mb-6 rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex border-b border-slate-200">
                <button @click="activeTab = 'photos'"
                    :class="activeTab === 'photos' ? 'text-green-600 border-b-2 border-green-600' :
                        'text-slate-600 hover:text-slate-900'"
                    class="px-6 py-4 font-medium text-sm transition-colors">
                    All Photos
                </button>
                <button @click="activeTab = 'links'"
                    :class="activeTab === 'links' ? 'text-green-600 border-b-2 border-green-600' :
                        'text-slate-600 hover:text-slate-900'"
                    class="px-6 py-4 font-medium text-sm transition-colors">
                    Links
                </button>
            </div>

            <div x-show="activeTab === 'photos'" class="p-6">
                <div x-cloak class="rounded-2xl border border-slate-200 bg-white px-4 py-4">
                    <template x-if="!loading && photos.length === 0">
                        <div class="w-full flex flex-col items-center justify-center text-center py-16">
                            <div class="h-20 w-20 rounded-2xl bg-slate-100 inline-flex items-center justify-center mb-4">
                                <svg class="h-10 w-10 text-slate-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 7a2 2 0 012-2h3l2-2h4l2 2h3a2 2 0 012 2v11a2 2 0 01-2 2H5a2 2 0 01-2-2V7z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8 13l2.293 2.293a1 1 0 001.414 0L16 11" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-slate-900 mb-2">No photos in this album</h3>
                            <p class="text-slate-500 text-sm mb-6 max-w-md">Upload photos to get started.</p>
                            <button type="button" @click="openUploadModal()"
                                class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-green-600 to-emerald-600 text-white hover:from-green-700 hover:to-emerald-700 transition-all inline-flex items-center gap-2 shadow-md hover:shadow-lg">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                                Upload Photos
                            </button>
                        </div>
                    </template>

                    <div x-show="photos.length > 0"
                        class="grid grid-cols-2 xs:grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-3">
                        <template x-for="photo in photos" :key="photo.id">
                            <div class="group rounded-xl overflow-hidden border border-slate-200 bg-white w-full shadow-sm hover:shadow-md transition duration-200"
                                :class="selectedPhotos.includes(photo.id) ? 'ring-2 ring-green-500' : ''">
                                <div class="relative aspect-square w-full">
                                    <img :src="photo.src" loading="lazy"
                                        class="object-cover w-full h-full group-hover:scale-[1.01] transition-transform duration-200"
                                        alt="" />
                                    <div
                                        class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/20 via-black/0 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                                    </div>
                                    <div class="absolute top-2 right-2 flex gap-1.5">
                                        <button @click="window.open(photo.src, '_blank')"
                                            class="h-8 w-8 rounded-lg bg-white/95 backdrop-blur-sm text-slate-700 hover:bg-green-600 hover:text-white inline-flex items-center justify-center shadow-md transition-all opacity-0 group-hover:opacity-100"
                                            title="View Photo">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="absolute top-2 left-2">
                                        <label class="cursor-pointer">
                                            <input type="checkbox" :value="photo.id"
                                                @change="toggleSelection(photo.id)"
                                                :checked="selectedPhotos.includes(photo.id)"
                                                class="h-5 w-5 rounded border-2 border-white shadow-lg text-green-600 focus:ring-2 focus:ring-green-500 focus:ring-offset-0 cursor-pointer">
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div x-cloak class="flex justify-center mt-6" x-show="hasMore">
                        <button @click="loadMore()"
                            class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200"
                            :disabled="loading">
                            <span x-show="!loading">Load more</span>
                            <span x-show="loading">Loading…</span>
                        </button>
                    </div>
                </div>
            </div>

            <div x-show="activeTab === 'links'" class="p-6">
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="p-5 border-b border-slate-200">
                        <div class="flex items-center gap-3">
                            <div
                                class="h-10 w-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white inline-flex items-center justify-center">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900">Album Links</h2>
                                <p class="text-xs text-slate-500">Uses event-level links for now</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-5 text-sm text-slate-600">
                        Links are shared at the event level. Use the event page to generate public/uploader links.
                    </div>
                </div>
            </div>
        </div>

        <div x-ref="uppyModalContainer"></div>

        <!-- Floating Upload Photos Button - Only on Album page -->
        <div class="fixed bottom-6 right-6 z-50">
            <button type="button" @click="openUploadModal()"
                class="h-14 w-14 rounded-full bg-gradient-to-r from-green-600 to-emerald-600 text-white hover:from-green-700 hover:to-emerald-700 inline-flex items-center justify-center shadow-lg hover:shadow-xl transition-all transform hover:scale-110"
                title="Upload Photos">
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
            </button>
        </div>

        <!-- Bulk Delete Action Bar -->
        <div x-show="selectedPhotos.length > 0"
            class="fixed bottom-20 left-1/2 transform -translate-x-1/2 z-50 bg-slate-900 text-white rounded-2xl shadow-2xl px-6 py-4 flex items-center gap-4">
            <span class="font-medium" x-text="`${selectedPhotos.length} photo(s) selected`"></span>
            <button @click="selectedPhotos = []"
                class="px-4 py-2 rounded-xl bg-slate-700 hover:bg-slate-600 text-white transition-all">
                Cancel
            </button>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        function albumPage(albumId, eventUuid, albumName) {
            let uppyManager = null;
            return {
                albumId,
                eventUuid,
                albumName,
                eventName: '{{ $eventName ?? '' }}',
                photos: [],
                page: 0,
                perPage: 24,
                hasMore: true,
                loading: false,
                selectedPhotos: [],
                activeTab: 'photos',

                async init() {
                    await this.loadMore();
                    this.initUppy();
                },

                initUppy() {
                    if (!window.UppyUploadManager) return;

                    if (uppyManager) return;

                    uppyManager = new window.UppyUploadManager({
                        container: this.$refs.uppyModalContainer,
                        inline: false,
                        onUpload: (files) => {
                            this.uploadPhotos(files);
                        },
                        onError: (message) => {
                            this.$store.messages.showError(message);
                        }
                    });
                },

                openUploadModal() {
                    if (uppyManager) {
                        uppyManager.openModal();
                    }
                },

                async uploadPhotos(files) {
                    if (!files || files.length === 0) {
                        this.$store.messages.showError('Please select at least one photo to upload.');
                        return;
                    }
                    const formData = new FormData();
                    files.forEach((file) => {
                        if (!file) return;
                        const fileName = file.name || 'file';
                        const fileExtension = (fileName.toLowerCase().split('.').pop() || '').trim();
                        if (fileExtension === 'zip') {
                            formData.append('zips[]', file.data, fileName);
                        } else {
                            formData.append('photos[]', file.data, fileName);
                        }
                    });
                    // Add album_id when uploading from album page
                    formData.append('album_id', this.albumId);
                    try {
                        const response = await fetch(
                            '{{ route('face_finder.events.upload_photos', ['uuid' => ':uuid']) }}'.replace(':uuid',
                                this.eventUuid), {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: formData
                            });
                        if (!response.ok) {
                            const data = await response.json().catch(() => ({}));
                            throw new Error(data.message || 'Upload failed');
                        }
                        const data = await response.json();
                        this.$store.messages.showSuccess(data.message || 'Photos uploaded successfully.');
                        uppyManager.closeModal();
                        uppyManager.reset();
                        this.photos = [];
                        this.page = 0;
                        this.hasMore = true;
                        await this.loadMore();
                    } catch (error) {
                        this.$store.messages.showError(error.message || 'Failed to upload photos. Please try again.');
                        uppyManager.reset();
                    }
                },

                async loadMore() {
                    if (this.loading || !this.hasMore) return;
                    this.loading = true;
                    try {
                        const response = await fetch(this.getPhotosApiUrl(), {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        if (!response.ok) throw new Error('Failed to load photos');
                        const payload = await response.json();
                        if (payload?.album?.event_uuid) this.eventUuid = payload.album.event_uuid;
                        if (Array.isArray(payload.photos)) this.photos.push(...payload.photos);
                        if (payload.pagination) {
                            this.hasMore = Boolean(payload.pagination.has_more_pages);
                            this.page = payload.pagination.current_page;
                        }
                    } catch (e) {
                        this.$store.messages.showError('Failed to load photos. Please try again.');
                    } finally {
                        this.loading = false;
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

                getPhotosApiUrl() {
                    const base = `{{ route('face_finder.albums.photos', ['id' => ':id']) }}`.replace(':id', this.albumId);
                    const params = new URLSearchParams({
                        page: String(this.page + 1),
                        per_page: String(this.perPage)
                    });
                    return `${base}?${params.toString()}`;
                }
            }
        }
    </script>
@endsection
