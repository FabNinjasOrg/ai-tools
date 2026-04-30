@extends('faceFinder.layout.sidebar-layout')

@section('page-title', 'Album Details')

@section('content')
    <div x-data="albumPage({{ $albumId }}, '{{ $eventUuid }}', '{{ $albumName }}')" x-init="init()" x-cloak class="max-w-7xl mx-auto px-6">
        @include('faceFinder.components.loader-overlay')
        <div class="mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4">
            <div class="flex items-center gap-3 text-lg md:text-lg">
                <a href="{{ route('face_finder.events.index') }}"
                    class="flex items-center gap-2 text-slate-600 hover:text-green-600 transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Events
                </a>
                <span class="text-slate-400">></span>
                <a :href="'{{ route('face_finder.events.show', ['uuid' => 'UUID']) }}'.replace('UUID', eventUuid)"
                    class="flex items-center gap-2 text-slate-600 hover:text-green-600 transition-colors" x-text="eventName"></a>
                <span class="text-slate-400">></span>
                <h1 class="flex items-center gap-2 text-slate-900">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                    </svg>
                    <span x-text="albumName"></span>
                </h1>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('face_finder.albums.index') }}"
                    class="px-4 py-2 rounded-xl bg-white border-2 border-blue-200 hover:border-blue-300 hover:bg-blue-50 text-blue-600 font-medium text-sm inline-flex items-center gap-2 transition-all">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    All Albums
                </a>
            </div>
        </div>

		@if(userHasAccessibility())
		<!-- Share Uploader Link Modal -->
		<div x-show="showShareLinkModal" x-cloak
			class="fixed inset-0 z-50 flex items-center justify-center">
			<div class="absolute inset-0 bg-slate-900/50" @click="showShareLinkModal = false"></div>
			<div class="relative w-full max-w-xl mx-auto bg-white rounded-2xl shadow-2xl">
				<div class="p-5 border-b border-slate-200 flex items-center justify-between">
					<h3 class="text-base font-semibold text-slate-900">Share Uploader Link</h3>
					<button class="h-8 w-8 rounded-lg hover:bg-slate-100 inline-flex items-center justify-center"
						@click="showShareLinkModal = false" aria-label="Close modal">
						<svg class="h-4 w-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
							stroke-width="2">
							<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
						</svg>
					</button>
				</div>
				<form class="p-5" @submit.prevent="handleShareLinkSubmit">
					<div class="space-y-4">
						<div>
							<label class="block text-sm font-medium text-slate-700 mb-1">Emails</label>
							<input type="text" x-model="shareEmails" placeholder="Enter emails, separated by commas"
								class="w-full rounded-xl border-slate-300 focus:border-green-500 focus:ring-green-500">
							<p class="text-xs text-slate-500 mt-1">Example: john@example.com, jane@example.com</p>
							<template x-if="shareErrors">
								<p class="text-xs text-red-600 mt-1" x-text="shareErrors"></p>
							</template>
						</div>
					</div>
					<div class="mt-6 flex items-center justify-end gap-3">
						<button type="button" @click="showShareLinkModal = false"
							class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200">
							Cancel
						</button>
                        <button type="submit" :disabled="sendingEmail"
                            class="px-4 py-2 rounded-xl bg-gradient-to-r from-green-600 to-emerald-600 text-white hover:from-green-700 hover:to-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!sendingEmail">Send</span>
                            <span x-show="sendingEmail">Sending...</span>
                        </button>
					</div>
				</form>
			</div>
		</div>
		@endif

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
                    Uploader Link
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
                            <button type="button" data-force-consent @click="openUploadModal()"
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
                            class="px-4 py-2 rounded-xl bg-gradient-to-r from-green-600 to-emerald-600 text-white hover:from-green-700 hover:to-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed shadow-sm hover:shadow-md"
                            :disabled="loading">
                            <span x-show="!loading">Load more</span>
                            <span x-show="loading">Loading…</span>
                        </button>
                    </div>
                </div>
            </div>

            <div x-show="activeTab === 'links'" class="p-6">
                @if(userHasAccessibility())
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="p-5 border-b border-slate-200">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-10 w-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white inline-flex items-center justify-center">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13 7h6m0 0v6m0-6l-8 8-4-4-6 6" />
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-lg font-semibold text-slate-900">Uploader Link</h2>
                                    <p class="text-xs text-slate-500">Create and manage an uploader link for this album.</p>
                                </div>
                            </div>
                            @if (!empty($uploaderLink))
                                <div class="flex items-center">
                                     <button type="button" @click="showShareLinkModal = true"
                                         class="px-3 py-1.5 rounded-lg text-sm bg-gradient-to-r from-green-600 to-emerald-600 text-white hover:from-green-700 hover:to-emerald-700 transition">
                                        Share uploader link
                                     </button>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="p-5">
                        @if (empty($uploaderLink))
                            <div class="w-full flex flex-col items-center justify-center text-center py-12">
                                <div
                                    class="h-20 w-20 rounded-2xl bg-slate-100 inline-flex items-center justify-center mb-4">
                                    <svg class="h-10 w-10 text-slate-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-slate-900 mb-2">No uploader link yet</h3>
                                <p class="text-slate-500 text-sm mb-6 max-w-md">Create an uploader link to allow selected
                                    people to upload photos to this album.</p>
                                <button type="button" @click="showCreateLinkModal = true"
                                    class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-green-600 to-emerald-600 text-white hover:from-green-700 hover:to-emerald-700 transition-all inline-flex items-center gap-2 shadow-md hover:shadow-lg">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Create uploader link
                                </button>
                            </div>
                        @else
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Uploader link URL</label>
                                    <input type="text"
                                        class="w-full rounded-xl border-slate-300 bg-slate-50 text-slate-700"
                                        value="{{ $uploaderLink->url }}" disabled>
                                </div>
                                <form method="POST"
                                    action="{{ route('face_finder.albums.update_uploader_link', ['id' => $albumId]) }}"
                                    class="space-y-4">
                                    @csrf
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 mb-1">Start date &
                                                time <span class="text-red-500">*</span></label>
                                            <input type="datetime-local" name="start_at"
                                                value="{{ old('start_at', $startAtLocal ?? ($uploaderLink->start ? \Carbon\Carbon::parse($uploaderLink->start)->format('Y-m-d\TH:i') : '')) }}"
                                                class="w-full rounded-xl border-slate-300 focus:border-green-500 focus:ring-green-500 @error('start_at') border-red-500 @enderror">
                                            @error('start_at')
                                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 mb-1">End date &
                                                time <span class="text-red-500">*</span></label>
                                            <input type="datetime-local" name="end_at"
                                                value="{{ old('end_at', $endAtLocal ?? ($uploaderLink->end ? \Carbon\Carbon::parse($uploaderLink->end)->format('Y-m-d\TH:i') : '')) }}"
                                                class="w-full rounded-xl border-slate-300 focus:border-green-500 focus:ring-green-500 @error('end_at') border-red-500 @enderror">
                                            @error('end_at')
                                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Pass code <span class="text-red-500">*</span></label>
                                        <input type="text" name="passcode"
                                            placeholder="Enter a pass code required to access the link"
                                            value="{{ old('passcode', $uploaderLink->passcode) }}"
                                            class="w-full rounded-xl border-slate-300 focus:border-green-500 focus:ring-green-500 @error('passcode') border-red-500 @enderror">
                                        @error('passcode')
                                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Status</label>
                                        <div class="flex items-center gap-6">
                                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                                <input type="radio" name="status" value="active"
                                                    class="text-green-600 focus:ring-green-500"
                                                    {{ old('status', $uploaderLink->status) === 'active' ? 'checked' : '' }}>
                                                <span class="text-sm text-slate-700">Active</span>
                                            </label>
                                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                                <input type="radio" name="status" value="inactive"
                                                    class="text-green-600 focus:ring-green-500"
                                                    {{ old('status', $uploaderLink->status) === 'inactive' ? 'checked' : '' }}>
                                                <span class="text-sm text-slate-700">Inactive</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="pt-2 flex items-center justify-end">
                                        <button type="submit"
                                            class="px-4 py-2 rounded-xl bg-gradient-to-r from-green-600 to-emerald-600 text-white hover:from-green-700 hover:to-emerald-700">
                                            Save changes
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
                @else
                <div class="rounded-2xl border border-amber-200 bg-gradient-to-r from-amber-50 to-orange-50 shadow-lg p-8">
                    <div class="flex flex-col items-center justify-center text-center">
                        <div class="h-20 w-20 rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 inline-flex items-center justify-center mb-4">
                            <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-orange-900 mb-2">Access Restricted</h3>
                        <p class="text-amber-800 text-sm mb-6 max-w-md">Uploader link feature is not available for trial users. Please upgrade your subscription to unlock this feature.</p>
                        <a href="{{ route('face_finder.buy_subscription') }}" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-green-600 to-emerald-600 text-white hover:from-green-700 hover:to-emerald-700 transition-all inline-flex items-center gap-2 shadow-md hover:shadow-lg">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            Upgrade Now
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>

        @if(userHasAccessibility())
        <!-- Create Uploader Link Modal -->
        <div x-show="showCreateLinkModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-slate-900/50" @click="showCreateLinkModal = false"></div>
            <div class="relative w-full max-w-xl mx-auto bg-white rounded-2xl shadow-2xl">
                <div class="p-5 border-b border-slate-200 flex items-center justify-between">
                    <h3 class="text-base font-semibold text-slate-900">Create Uploader Link</h3>
                    <button class="h-8 w-8 rounded-lg hover:bg-slate-100 inline-flex items-center justify-center"
                        @click="showCreateLinkModal = false" aria-label="Close modal">
                        <svg class="h-4 w-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form method="POST" action="{{ route('face_finder.albums.create_uploader_link', ['id' => $albumId]) }}"
                    class="p-5" onsubmit="setTimezone(this)">
                    @csrf
                    <input type="hidden" name="album_id" :value="albumId">
                    <input type="hidden" name="timezone" id="timezone-create">
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Start date & time <span class="text-red-500">*</span></label>
                                <input type="datetime-local" name="start_at" value="{{ old('start_at') }}"
                                    class="w-full rounded-xl border-slate-300 focus:border-green-500 focus:ring-green-500 @error('start_at') border-red-500 @enderror">
                                @error('start_at')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">End date & time <span class="text-red-500">*</span></label>
                                <input type="datetime-local" name="end_at" value="{{ old('end_at') }}"
                                    class="w-full rounded-xl border-slate-300 focus:border-green-500 focus:ring-green-500 @error('end_at') border-red-500 @enderror">
                                @error('end_at')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Pass code <span class="text-red-500">*</span></label>
                            <input type="text" name="passcode" value="{{ old('passcode') }}"
                                placeholder="Enter a pass code required to access the link"
                                class="w-full rounded-xl border-slate-300 focus:border-green-500 focus:ring-green-500 @error('passcode') border-red-500 @enderror">
                            @error('passcode')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Status</label>
                            <div class="flex items-center gap-6">
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="status" value="active"
                                        class="text-green-600 focus:ring-green-500" {{ old('status', 'active') == 'active' ? 'checked' : '' }}>
                                    <span class="text-sm text-slate-700">Active</span>
                                </label>
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="status" value="inactive"
                                        class="text-green-600 focus:ring-green-500" {{ old('status') == 'inactive' ? 'checked' : '' }}>
                                    <span class="text-sm text-slate-700">Inactive</span>
                                </label>
                            </div>
                        </div>
                        <input type="hidden" name="event_uuid" :value="eventUuid">
                    </div>
                    <div class="mt-6 flex items-center justify-end gap-3">
                        <button type="button" @click="showCreateLinkModal = false"
                            class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 rounded-xl bg-gradient-to-r from-green-600 to-emerald-600 text-white hover:from-green-700 hover:to-emerald-700">
                            Create link
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        <div x-ref="uppyModalContainer"></div>

        <!-- Floating Upload Photos Button - Only on Album page -->
        <div class="fixed bottom-6 right-6 z-50">
            <button type="button" data-force-consent @click="openUploadModal()"
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
            <button @click="deleteSelectedPhotos()" :disabled="deletingPhotos"
                class="px-4 py-2 rounded-xl bg-red-500 hover:bg-red-600 disabled:opacity-60 disabled:cursor-not-allowed transition-all inline-flex items-center gap-2">
                <span x-text="deletingPhotos ? 'Deleting…' : 'Delete'"></span>
            </button>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        function setTimezone(form) {
            const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            const timezoneInput = form.querySelector('input[name="timezone"]');
            if (timezoneInput) {
                timezoneInput.value = timezone;
            }
        }

        function albumPage(albumId, eventUuid, albumName) {
            let uppyManager = null;
            return {
                albumId,
                eventUuid,
                albumName,
                eventName: '{{ $eventName ?? '' }}',
                showCreateLinkModal: false,
                showShareLinkModal: false,
                shareEmails: '',
                shareErrors: '',
                sendingEmail: false,
                photos: [],
                page: 0,
                perPage: 24,
                hasMore: true,
                loading: false,
                selectedPhotos: [],
                activeTab: '{{ request('tab', 'photos') }}',
                pendingReload: false,
                deletingPhotos: false,
                isUploading: false,

                async init() {
                    await this.loadMore();
                    this.initUppy();

                    this.$watch('$store.uploading_data.polling', (value) => {
                        if (value === false && this.pendingReload) {
                            const hasSessions = Array.isArray(this.$store.uploading_data.upload_session_ids) && this.$store.uploading_data.upload_session_ids.length > 0;
                            if (!hasSessions) {
                                this.pendingReload = false;
                                this.refreshPhotosGrid();
                            }
                        }
                    });

                    @if($errors->any() && empty($uploaderLink))
                        this.showCreateLinkModal = true;
                    @endif
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

                    this.isUploading = true;

                    if (uppyManager) {
                        uppyManager.closeModal();
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

                        const uploadSessionIds = data.results.map(item => item.upload_session_id);

                        // Collect data for polling
                        this.$store.uploading_data.polling = true;
                        this.$store.uploading_data.upload_session_ids = uploadSessionIds;
                        this.pendingReload = true;

                        uppyManager.reset();
                    } catch (error) {
                        this.$store.messages.showError(error.message || 'Failed to upload photos. Please try again.');
                        uppyManager.reset();
                    } finally {
                        this.isUploading = false;
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
                },

                async refreshPhotosGrid() {
                    this.photos = [];
                    this.page = 0;
                    this.hasMore = true;
                    await this.loadMore();
                },

                async handleShareLinkSubmit() {
                    const raw = this.shareEmails || '';
                    const emails = raw.split(',').map(e => e.trim()).filter(Boolean);
                    if (emails.length === 0) {
                        this.shareErrors = 'Please enter at least one email.';
                        return;
                    }
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    const invalid = emails.filter(e => !emailRegex.test(e));
                    if (invalid.length > 0) {
                        this.shareErrors = `Invalid email(s): ${invalid.join(', ')}`;
                        return;
                    }
                    this.shareErrors = '';
                    this.sendingEmail = true;

                    try {
                        const response = await fetch(
                            '{{ route('face_finder.albums.share_uploader_link') }}',
                            {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    emails: emails,
                                    album_id: this.albumId
                                })
                            }
                        );

                        if (response.ok) {
                            this.$store.messages.showSuccess('Uploader link shared successfully.');
                            this.showShareLinkModal = false;
                            this.shareEmails = '';
                        } else {
                            this.shareErrors = 'Failed to share uploader link. Please try again.';
                        }
                    } catch (error) {
                        this.shareErrors = 'An error occurred. Please try again.';
                    } finally {
                        this.sendingEmail = false;
                    }
                },

                async deleteSelectedPhotos() {
                    if (this.selectedPhotos.length === 0 || this.deletingPhotos) return;
                    const confirmed = confirm('Delete selected photos? This cannot be undone.');
                    if (!confirmed) return;

                    this.deletingPhotos = true;
                    try {
                        const response = await fetch(
                            '{{ route('face_finder.events.bulk_delete_photos', ['uuid' => ':uuid']) }}'.replace(':uuid', this.eventUuid),
                            {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    photo_ids: JSON.stringify(this.selectedPhotos),
                                    album_id: this.albumId
                                })
                            }
                        );

                        const data = await response.json();

                        if (!response.ok) {
                            throw new Error(data.message || 'Failed to delete photos.');
                        }

                        this.$store.messages.showSuccess(data.message || 'Photos deleted successfully.');
                        this.selectedPhotos = [];
                        await this.refreshPhotosGrid();
                    } catch (error) {
                        this.$store.messages.showError(error.message || 'Failed to delete photos. Please try again.');
                    } finally {
                        this.deletingPhotos = false;
                    }
                },
            }
        }
    </script>
@endsection
