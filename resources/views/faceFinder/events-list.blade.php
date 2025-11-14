@extends('faceFinder.layout.sidebar-layout')

@section('page-title', 'Your Events')

@section('content')
    <div class="max-w-7xl mx-auto px-6">
        <div x-data="eventsListApp()" x-init="init()" x-cloak>

            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden flex flex-col shadow-sm">
                <div class="p-5 border-b border-slate-200 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 text-white inline-flex items-center justify-center">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-3 flex-wrap">
                                <h2 class="text-lg font-semibold text-slate-900">All Events</h2>
                                <span class="text-xs px-2 py-1 rounded-full bg-slate-100 text-slate-600 font-medium"
                                      x-text="events.length + ' total'"></span>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">Click any event card to manage albums, generate links, or check analytics.</p>
                        </div>
                    </div>
                    <button @click="openCreateModal()"
                            class="px-4 py-2 rounded-xl bg-gradient-to-r from-purple-600 to-pink-600 text-white shadow hover:from-purple-700 hover:to-pink-700 text-sm inline-flex items-center gap-2 self-start lg:self-auto">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Create Event
                    </button>
                </div>

                <div class="p-6 flex-1 overflow-y-auto">
                    <!-- Loading State -->
                    <template x-if="isLoading">
                        <div class="w-full flex flex-col items-center justify-center text-center py-16">
                            <div class="h-12 w-12 rounded-full border-4 border-slate-200 border-t-emerald-600 animate-spin mb-4"></div>
                            <p class="text-slate-500 text-sm">Loading your events...</p>
                        </div>
                    </template>

                    <!-- Empty State -->
                    <template x-if="!isLoading && events.length === 0">
                        <div class="w-full flex flex-col items-center justify-center text-center py-16">
                            <div class="h-20 w-20 rounded-2xl bg-slate-100 inline-flex items-center justify-center mb-4">
                                <svg class="h-10 w-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-slate-900 mb-2">No events yet</h3>
                            <p class="text-slate-500 text-sm mb-6 max-w-md">You haven't created any events yet. Upload a ZIP file to get started!</p>
                        </div>
                    </template>

                    <!-- Events Grid -->
                    <div x-show="!isLoading && events.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        <template x-for="event in events" :key="event.id">
                            <div class="group relative rounded-xl border border-slate-200 bg-white shadow-sm hover:shadow-md transition-shadow">
                                <form
                                    :action="'{{ route('face_finder.events.delete', ['uuid' => 'UUID_PLACEHOLDER']) }}'.replace('UUID_PLACEHOLDER', event.uuid)"
                                    method="POST"
                                    onsubmit="return confirm('Are you sure you want to delete this event? This cannot be undone.');"
                                    class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 focus-within:opacity-100 transition-opacity duration-200 z-10"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            @click.stop
                                            class="p-2 rounded-full bg-white/90 text-red-600 border border-red-100 shadow-sm hover:bg-red-50 hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-red-200"
                                            title="Delete event">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                                <a :href="'{{ route('face_finder.events.show', ['uuid' => 'UUID_PLACEHOLDER']) }}'.replace('UUID_PLACEHOLDER', event.uuid)"
                                   class="p-5 block w-full hover:bg-slate-50 rounded-xl transition-colors">
                                    <div class="flex flex-col items-center text-center">
                                        <!-- Event Icon -->
                                        <div class="h-20 w-20 rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 inline-flex items-center justify-center mb-4">
                                            <svg class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <!-- Event Name -->
                                        <div class="text-base font-semibold text-slate-900 leading-snug break-words w-full mb-2" x-text="event.name"></div>
                                        <!-- Event Info -->
                                        <div class="text-xs text-slate-500 w-full">
                                            <span x-text="(event.albums_count || 0) + ' album' + ((event.albums_count||0) === 1 ? '' : 's')"></span>
                                            <span x-text="event.created_at ? ' • ' + (new Date(event.created_at)).toLocaleDateString() : ''"></span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            <!-- Create Event Modal -->
            <div x-show="showCreateModal" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center">
                <div @click="closeCreateModal()" class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"></div>
                <div class="relative w-full max-w-lg mx-4 bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
                    <div class="p-5 border-b border-slate-200 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 text-white inline-flex items-center justify-center">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-base font-semibold text-slate-900">Create New Event</h2>
                                <p class="text-xs text-slate-500">Upload photos for your event</p>
                            </div>
                        </div>
                        <button @click="closeCreateModal()" class="p-2 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="p-6">
                        <form method="POST" action="{{ route('face_finder.events.store_name') }}" class="space-y-5">
                            @csrf
                            <div>
                                <label for="event_name" class="block text-sm font-medium text-slate-700">Event name <span class="text-red-500">*</span></label>
                                <input
                                    type="text"
                                    id="event_name"
                                    name="name"
                                    value="{{ old('name') }}"
                                    class="mt-2 block w-full rounded-xl border {{ $errors->has('name') ? 'border-red-300' : 'border-slate-300' }} px-3 py-2 text-slate-900 placeholder-slate-400 focus:border-purple-500 focus:ring-purple-500"
                                    placeholder="e.g. John & Jane Wedding"
                                >
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="album_name" class="block text-sm font-medium text-slate-700">Album name <span class="text-red-500">*</span></label>
                                <input
                                    type="text"
                                    id="album_name"
                                    name="album_name"
                                    value="{{ old('album_name') }}"
                                    class="mt-2 block w-full rounded-xl border {{ $errors->has('album_name') ? 'border-red-300' : 'border-slate-300' }} px-3 py-2 text-slate-900 placeholder-slate-400 focus:border-purple-500 focus:ring-purple-500"
                                    placeholder="e.g. Ceremony Highlights"
                                >
                                @error('album_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="pt-2">
                                <button
                                    type="submit"
                                    class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-purple-600 to-pink-600 px-4 py-2 text-sm font-semibold text-white shadow hover:from-purple-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Create event
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function eventsListApp() {
            return {
                events: [],
                isLoading: true,
                showCreateModal: false,

                init() {
                    this.loadEvents();
                    if (window.__openCreateModal) {
                        this.openCreateModal();
                    }
                },

                openCreateModal() {
                    this.showCreateModal = true;
                },

                closeCreateModal() {
                    this.showCreateModal = false;
                },

                async loadEvents() {
                    this.isLoading = true;

                    try {
                        const res = await fetch("{{ route('face_finder.load_events') }}", {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        if (!res.ok) throw new Error('Failed to load events');
                        const data = await res.json();
                        this.events = Array.isArray(data.albums) ? data.albums : [];

                    } catch (e) {
                        console.error(e);
                        this.$store.messages.showError('Failed to load events. Please refresh the page.');
                    } finally {
                        this.isLoading = false;
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
    @if ($errors->any())
        <script>
            window.__openCreateModal = true;
        </script>
    @endif
@endsection

