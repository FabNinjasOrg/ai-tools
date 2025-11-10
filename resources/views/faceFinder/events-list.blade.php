@extends('faceFinder.layout.sidebar-layout')

@section('page-title', 'Your Events')

@section('content')
    <div class="max-w-7xl mx-auto px-6">
        <div x-data="eventsListApp()" x-init="loadEvents()" x-cloak>
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden flex flex-col shadow-sm">
                <div class="p-5 border-b border-slate-200 flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 text-white inline-flex items-center justify-center">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">All Events</h2>
                            <p class="text-xs text-slate-500" x-text="events.length + ' event' + (events.length !== 1 ? 's' : '')"></p>
                        </div>
                    </div>
                    <div class="text-[12px] text-slate-600 hidden lg:inline-flex items-center gap-2">
                        <span class="h-6 w-6 rounded-full bg-emerald-100 text-emerald-700 inline-flex items-center justify-center font-semibold text-xs">i</span>
                        <span>Click on any event to view details, generate a public link, or manage photos.</span>
                    </div>
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
                            <a href="{{ route('face_finder.events.create') }}"
                               class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-green-600 to-emerald-600 text-white hover:from-green-700 hover:to-emerald-700 transition-all inline-flex items-center gap-2 shadow-md hover:shadow-lg">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                                Create Your First Event
                            </a>
                        </div>
                    </template>

                    <!-- Events Grid -->
                    <div x-show="!isLoading && events.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        <template x-for="event in events" :key="event.id">
                            <div class="rounded-xl border border-slate-200 bg-white shadow-sm hover:shadow-md transition-shadow">
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
                                            <span x-text="event.count + ' photos'"></span>
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
        function eventsListApp() {
            return {
                events: [],
                isLoading: true,

                async loadEvents() {
                    this.isLoading = true;

                    // Check for pending upload before loading events
                        try {
                            const pending = JSON.parse(localStorage.getItem('ff_uploading_event') || 'null');
                            if (pending && pending.uuid) {
                                this.$store.messages.showInfo(`Event "${pending.name || ''}" is uploading. Please wait...`, 0);
                            }
                        } catch (e) {}

                    try {
                        const res = await fetch("{{ route('face_finder.load_events') }}", {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        if (!res.ok) throw new Error('Failed to load events');
                        const data = await res.json();
                        this.events = Array.isArray(data.albums) ? data.albums : [];

                        // Check for upload completion message in localStorage
                        try {
                            const pending = JSON.parse(localStorage.getItem('ff_uploading_event') || 'null');
                            if (pending && pending.uuid) {
                                this.checkUploadStatus(pending.uuid, pending.name);
                            }
                        } catch (e) {}
                    } catch (e) {
                        console.error(e);
                        this.$store.messages.showError('Failed to load events. Please refresh the page.');
                    } finally {
                        this.isLoading = false;
                    }
                },

                async checkUploadStatus(uuid, name) {
                    const url = "{{ route('eventUploadStatus', ['uuid' => 'UUID_PLACEHOLDER']) }}".replace('UUID_PLACEHOLDER', uuid);
                    try {
                        const res = await fetch(url, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        if (!res.ok) return;
                        const data = await res.json();
                        const status = data && data.upload_status ? data.upload_status : null;

                        if (status === 'completed') {
                            localStorage.removeItem('ff_uploading_event');
                            await this.loadEvents();
                            this.$store.messages.showSuccess(`Event "${name}" has been successfully processed!`, 5000);
                        } else if (status === 'inprogress') {
                            this.$store.messages.showInfo(`Event "${name}" is still processing...`, 0);
                            setTimeout(() => this.checkUploadStatus(uuid, name), 5000);
                        } else if (status === 'failed') {
                            localStorage.removeItem('ff_uploading_event');
                            this.$store.messages.showError(`Event "${name}" processing failed. Please try uploading again.`);
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

