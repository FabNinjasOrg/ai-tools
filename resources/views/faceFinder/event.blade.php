@extends('faceFinder.layout.sidebar-layout')

@section('page-title', 'Event Details')

@section('content')
    <div x-data="eventPage('{{ $uuid }}')" x-init="init()" x-cloak class="max-w-7xl mx-auto px-6">
        <div class="mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4">
            <div class="flex items-center gap-3 text-xl md:text-xl">
                <a href="{{ route('face_finder.events.index') }}" class="text-slate-600 hover:text-green-600 transition-colors">
                    Event
                </a>
                >
                <h1 x-text="eventName"></h1>
            </div>
            <div class="flex items-center gap-2">
                <form action="{{ route('face_finder.events.delete', ['uuid' => $uuid]) }}" method="POST"
                    onsubmit="return confirm('Are you sure you want to delete this event? This cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="h-10 px-4 rounded-xl bg-red-600 text-white hover:bg-red-700 inline-flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>

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

        <!-- Analytics Section -->
        <div class="mb-6 rounded-2xl border border-slate-200 bg-white shadow-sm">
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

        <!-- Uppy Modal Container -->
        <div x-ref="uppyModalContainer"></div>

        <!-- Floating Upload Photos Button - Bottom Right Corner -->
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
            <form action="{{ route('face_finder.events.bulk_delete_photos', ['uuid' => $uuid]) }}" method="POST"
                  onsubmit="return confirm('Are you sure you want to delete the selected photos? This cannot be undone.');">
                @csrf
                @method('DELETE')
                <input type="hidden" name="photo_ids" :value="JSON.stringify(selectedPhotos)">
                <button type="submit"
                        class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white inline-flex items-center gap-2 transition-all">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete
                </button>
            </form>
            <button @click="selectedPhotos = []"
                    class="px-4 py-2 rounded-xl bg-slate-700 hover:bg-slate-600 text-white transition-all">
                Cancel
            </button>
        </div>

		<div x-cloak class="rounded-2xl border border-slate-200 bg-white px-4 py-4">
			<!-- Empty State -->
			<template x-if="!loading && photos.length === 0">
				<div class="w-full flex flex-col items-center justify-center text-center py-16">
					<div class="h-20 w-20 rounded-2xl bg-slate-100 inline-flex items-center justify-center mb-4">
						<svg class="h-10 w-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
							<path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 012-2h3l2-2h4l2 2h3a2 2 0 012 2v11a2 2 0 01-2 2H5a2 2 0 01-2-2V7z" />
							<path stroke-linecap="round" stroke-linejoin="round" d="M8 13l2.293 2.293a1 1 0 001.414 0L16 11" />
						</svg>
					</div>
					<h3 class="text-lg font-semibold text-slate-900 mb-2">No photos for this event</h3>
					<p class="text-slate-500 text-sm mb-6 max-w-md">Please upload photos to get started.</p>
					<button type="button" @click="openUploadModal()"
						class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-green-600 to-emerald-600 text-white hover:from-green-700 hover:to-emerald-700 transition-all inline-flex items-center gap-2 shadow-md hover:shadow-lg">
						<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
							<path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
						</svg>
						Upload Photos
					</button>
				</div>
			</template>

			<!-- Photos Grid -->
			<div x-show="photos.length > 0" class="grid grid-cols-2 xs:grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-3">
                <template x-for="photo in photos" :key="photo.id">
                    <div
                        class="group rounded-xl overflow-hidden border border-slate-200 bg-white w-full shadow-sm hover:shadow-md transition duration-200"
                        :class="selectedPhotos.includes(photo.id) ? 'ring-2 ring-green-500' : ''">
                        <div class="relative aspect-square w-full">
                            <img :src="photo.src" loading="lazy"
                                class="object-cover w-full h-full group-hover:scale-[1.01] transition-transform duration-200"
                                alt="" />
                            <div
                                class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/20 via-black/0 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                            </div>

                            <!-- Action Buttons - Top Right Corner -->
                            <div class="absolute top-2 right-2 flex gap-1.5">
                                <!-- View Button -->
                                <button @click="window.open(photo.src, '_blank')"
                                    class="h-8 w-8 rounded-lg bg-white/95 backdrop-blur-sm text-slate-700 hover:bg-green-600 hover:text-white inline-flex items-center justify-center shadow-md transition-all opacity-0 group-hover:opacity-100"
                                    title="View Photo">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Selection Checkbox - Top Left Corner -->
                            <div class="absolute top-2 left-2">
                                <label class="cursor-pointer">
                                    <input type="checkbox"
                                           :value="photo.id"
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
                <button @click="loadMore()" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200"
                    :disabled="loading">
                    <span x-show="!loading">Load more</span>
                    <span x-show="loading">Loading…</span>
                </button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        function eventPage(uuid) {
            let uppyManager = null;

            // Alpine component for event detail page
            return {
                // state
                uuid,
                eventName: '',
                photos: [],
                page: 0, // Start at 0, will be updated by pagination response
                perPage: 24,
                hasMore: true,
                loading: false,
                publicUrl: '',
                loadingUrl: false,
                selectedPhotos: [],

                // lifecycle
                async init() {
                    await this.loadMore();
                    // Auto-generate public URL if it doesn't exist
                    if (!this.publicUrl) {
                        await this.generatePublic();
                    }
                    // Initialize Uppy
                    this.initUppy();
                },

                initUppy() {
                    if (!window.UppyUploadManager) {
                        console.error('Uppy manager not loaded');
                        return;
                    }
                    // Prevent double initialization
                    if (uppyManager) {
                        return;
                    }

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

                    // Separate zip files and photo files
                    files.forEach((file) => {
                        if (!file) return;

                        const fileName = file.name || 'file';
                        const fileExtension = fileName.toLowerCase().split('.').pop();

                        // Check if it's a zip file
                        if (fileExtension === 'zip') {
                            formData.append('zips[]', file.data, fileName);
                        } else {
                            // It's a photo file
                            formData.append('photos[]', file.data, fileName);
                        }
                    });

                    try {
                        const response = await fetch('{{ route('face_finder.events.upload_photos', ['uuid' => $uuid]) }}', {
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
                        this.$store.messages.showSuccess(data.message || 'Photos uploaded successfully. It will take some time to process them. Kindly, wait.');

                        // Close modal and reset
                        uppyManager.closeModal();
                        uppyManager.reset();

                        // Reload photos
                        this.photos = [];
                        this.page = 0;
                        this.hasMore = true;
                        await this.loadMore();
                    } catch (error) {
                        console.error('Upload error:', error);
                        this.$store.messages.showError(error.message || 'Failed to upload photos. Please try again.');

                        uppyManager.reset();
                    }
                },

                // actions
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
                        this.eventName = payload?.album?.name || this.eventName;
                        if (payload?.album?.public_url) this.publicUrl = payload.album.public_url;
                        if (Array.isArray(payload.photos)) this.photos.push(...payload.photos);

                        // Handle new pagination structure
                        if (payload.pagination) {
                            this.hasMore = Boolean(payload.pagination.has_more_pages);
                            this.page = payload.pagination.current_page;
                        }
                    } catch (error) {
                        console.error(error);
                        this.$store.messages.showError('Failed to load photos. Please try again.');
                    } finally {
                        this.loading = false;
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
                }
            };
        }
    </script>
@endsection
