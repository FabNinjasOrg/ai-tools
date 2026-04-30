<!-- Create Album Modal -->
<div x-show="showCreateAlbumModal"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto"
     @keydown.escape.window="showCreateAlbumModal = false">
    <div @click="showCreateAlbumModal = false" class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"></div>
    <div class="relative w-full max-w-lg mx-4 bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
        <!-- Modal Header -->
        <div class="p-5 border-b border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white inline-flex items-center justify-center">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Create New Album</h2>
                    <p class="text-xs text-slate-500">Add a new album to your event</p>
                </div>
            </div>
            <button @click="showCreateAlbumModal = false"
                    class="p-2 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-colors">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
            <form method="POST" action="{{ route('face_finder.albums.store') }}" class="space-y-5">
                @csrf

                <!-- Event Selection (only show when not on event detail page) -->
                @if(!request()->routeIs('face_finder.events.show'))
                    <div>
                        <label for="event_id" class="block text-sm font-medium text-slate-700">
                            Select Event <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="event_id"
                            name="event_id"
                            class="mt-2 block w-full rounded-xl border {{ $errors->has('event_id') ? 'border-red-300' : 'border-slate-300' }} px-3 py-2 text-slate-900 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Choose an event</option>
                            <template x-for="event in availableEvents" :key="event.id">
                                <option :value="event.id" x-text="event.name" :selected="event.id == '{{ old('event_id') }}'"></option>
                            </template>
                        </select>
                        @error('event_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @else
                    <!-- Hidden field when on event detail page -->
                    <input type="hidden" name="event_id" value="{{ old('event_id', $eventId ?? '') }}">
                @endif

                <!-- Album Name -->
                <div>
                    <label for="album_name" class="block text-sm font-medium text-slate-700">
                        Album Name <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="album_name"
                        name="album_name"
                        value="{{ old('album_name') }}"
                        class="mt-2 block w-full rounded-xl border {{ $errors->has('album_name') ? 'border-red-300' : 'border-slate-300' }} px-3 py-2 text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:ring-blue-500"
                        placeholder="e.g. Reception Photos"
                    >
                    @error('album_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Create Album
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

