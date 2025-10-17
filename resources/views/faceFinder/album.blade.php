@extends('faceFinder.app')

@section('content')
    <div x-data="albumPage('{{ $uuid }}')" x-init="init()" x-cloak class="max-w-7xl mx-auto px-6 py-12">
        <!-- Global page messages (top of page) -->
        <div class="mb-4">
            <template x-if="successMessage">
                <div class="w-full">
                    <div
                        class="rounded-xl border border-green-200 border-l-4 border-l-green-500 bg-green-50 px-4 py-3 text-sm shadow-sm text-green-800">
                        <div class="flex items-start gap-3">
                            <div
                                class="shrink-0 h-5 w-5 rounded-full bg-green-100 text-green-700 inline-flex items-center justify-center">
                                ✓</div>
                            <div class="flex-1" x-text="successMessage"></div>
                            <button @click="successMessage=''" class="text-green-700/70 hover:text-green-800">✕</button>
                        </div>
                    </div>
                </div>
            </template>
            <template x-if="errorMessage">
                <div class="w-full">
                    <div
                        class="rounded-xl border border-red-200 border-l-4 border-l-red-500 bg-red-50 px-4 py-3 text-sm shadow-sm text-red-800">
                        <div class="flex items-start gap-3">
                            <div
                                class="shrink-0 h-5 w-5 rounded-full bg-red-100 text-red-700 inline-flex items-center justify-center">
                                !</div>
                            <div class="flex-1" x-text="errorMessage"></div>
                            <button @click="errorMessage=''" class="text-red-700/70 hover:text-red-800">✕</button>
                        </div>
                    </div>
                </div>
            </template>
            <template x-if="infoMessage">
                <div class="w-full">
                    <div
                        class="rounded-xl border border-slate-200 border-l-4 border-l-slate-400 bg-slate-50 px-4 py-3 text-sm shadow-sm text-slate-800">
                        <div class="flex items-start gap-3">
                            <div
                                class="shrink-0 h-5 w-5 rounded-full bg-slate-100 text-slate-700 inline-flex items-center justify-center">
                                i</div>
                            <div class="flex-1" x-text="infoMessage"></div>
                            <button @click="infoMessage=''" class="text-slate-600/70 hover:text-slate-800">✕</button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
        <div class="mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4">
            <div class="flex items-center gap-3">
                <h1 class="text-3xl md:text-5xl font-bold bg-gradient-to-r from-green-600 via-emerald-600 to-teal-600 bg-clip-text text-transparent"
                    x-text="albumName"></h1>
            </div>
            <div class="flex items-center gap-2">
                <form action="{{ route('face_finder.albums.delete', ['uuid' => $uuid]) }}" method="POST"
                    onsubmit="return confirm('Are you sure you want to delete this album? This cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="h-10 px-4 rounded-xl bg-red-600 text-white hover:bg-red-700 inline-flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete album
                    </button>
                </form>
                <a href="{{ route('face_finder.upload_album') }}"
                    class="h-10 px-4 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 inline-flex items-center">Back</a>
            </div>
        </div>

        <div x-cloak x-show="publicUrl || !loading" class="mb-8 rounded-2xl border border-slate-200 bg-white px-4 py-4">
            <div class="flex flex-col sm:flex-row items-start sm:items-start justify-between gap-3 sm:gap-4">
                <div class="w-full sm:w-auto">
                    <div class="text-sm font-medium text-slate-900">Public share link</div>
                    <div class="text-[13px] text-slate-600 mt-1">Click Generate to create a public link for this album that
                        you can share.</div>

                    <!-- Admin OTP stats inside the same card -->
                    <div class="mt-4 pt-3 border-t border-slate-200">
                        <div class="flex items-center gap-2 mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <span class="text-sm font-medium text-slate-700">Public URL Analytics</span>
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            <div
                                class="bg-gradient-to-r from-emerald-50 to-green-50 rounded-lg p-3 border border-emerald-200">
                                <div class="flex items-center gap-2">
                                    <div>
                                        <div class="text-xs text-emerald-700 font-medium">Number Of Successful OTP Verifications</div>
                                        <div class="text-lg font-bold text-emerald-800 tabular-nums">{{ $attemptTotal }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-3 border border-blue-200">
                                <div class="flex items-center gap-2">
                                    <div>
                                        <div class="text-xs text-blue-700 font-medium">Number Of Unique Users visited</div>
                                        <div class="text-lg font-bold text-blue-800 tabular-nums">{{ $attemptUniquePhones }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gradient-to-r from-red-50 to-red-50 rounded-lg p-3 border border-red-200">
                                <div class="flex items-center gap-2">
                                    <div>
                                        <div class="text-xs text-red-700 font-medium">Number of attempts with no matches found</div>
                                        <div class="text-lg font-bold text-red-800 tabular-nums">{{ $attemptUniquePhones }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2 sm:gap-3 shrink-0 w-full sm:w-auto sm:justify-end">
                    <template x-if="publicUrl">
                        <div x-cloak
                            class="inline-flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 w-full sm:w-auto">
                            <input type="text" :value="publicUrl" readonly
                                class="bg-transparent text-sm text-slate-700 w-full sm:w-56 border-none">
                            <button @click="copyPublic()"
                                class="text-emerald-700 text-sm hover:underline cursor-pointer whitespace-nowrap">Copy</button>
                        </div>
                    </template>
                    <button x-cloak x-show="!publicUrl" @click="generatePublic()"
                        class="h-9 px-4 rounded-xl bg-gradient-to-r from-green-600 to-emerald-600 text-white hover:from-green-700 hover:to-emerald-700 w-full sm:w-auto">Generate</button>
                </div>
            </div>
        </div>

        <!-- Photos wrapped in a bordered card like the public link card -->
        <div x-cloak class="rounded-2xl border border-slate-200 bg-white px-4 py-4">
            <div class="grid grid-cols-2 xs:grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-3">
                <template x-for="photo in photos" :key="photo.id">
                    <div
                        class="group rounded-xl overflow-hidden border border-slate-200 bg-white w-full shadow-sm hover:shadow-md transition duration-200">
                        <div class="relative aspect-square w-full">
                            <img :src="photo.src" loading="lazy"
                                class="object-cover w-full h-full group-hover:scale-[1.01] transition-transform duration-200"
                                alt="" />
                            <div
                                class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/20 via-black/0 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
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
    <script>
        function albumPage(uuid) {
            // Alpine component for album detail page
            return {
                // state
                uuid,
                albumName: '',
                photos: [],
                page: 1,
                perPage: 24,
                hasMore: true,
                loading: false,
                publicUrl: '',
                // global messages
                successMessage: '',
                errorMessage: '',
                infoMessage: '',
                timers: {},

                // lifecycle
                async init() {
                    await this.loadMore();
                },

                // actions
                async loadMore() {
                    if (this.loading || !this.hasMore) return;
                    this.loading = true;
                    // clear any previous messages when starting a new action
                    this.clearMessages();
                    try {
                        const response = await fetch(this.getPhotosApiUrl(), {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        if (!response.ok) throw new Error('Failed to load photos');

                        const payload = await response.json();
                        this.albumName = payload?.album?.name || this.albumName;
                        if (payload?.album?.public_url) this.publicUrl = payload.album.public_url;
                        if (Array.isArray(payload.photos)) this.photos.push(...payload.photos);
                        this.hasMore = Boolean(payload.has_more);
                        this.page += 1;
                    } catch (error) {
                        console.error(error);
                        this.setError('Failed to load photos. Please try again.');
                    } finally {
                        this.loading = false;
                    }
                },

                async generatePublic() {
                    // clear previous messages
                    this.clearMessages();
                    try {
                        const endpoint =
                            `{{ route('face_finder.albums.generate_public', ['uuid' => 'UUID_PLACEHOLDER']) }}`.replace(
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
                        if (this.publicUrl) this.setSuccess('Public URL generated successfully.');
                        else this.setInfo('Public URL not available yet. Please try again later.');
                    } catch (error) {
                        console.error(error);
                        this.setError('Could not generate public URL. Please try again.');
                    }
                },

                async copyPublic() {
                    // clear previous messages
                    this.clearMessages();
                    try {
                        await navigator.clipboard.writeText(this.publicUrl);
                        this.setSuccess('Copied public URL to clipboard.');
                    } catch (_) {
                        this.setError('Failed to copy. Please copy manually.');
                    }
                },

                // helpers
                getPhotosApiUrl() {
                    const base = `{{ route('face_finder.albums.photos', ['uuid' => 'UUID_PLACEHOLDER']) }}`.replace(
                        'UUID_PLACEHOLDER', this.uuid);
                    const params = new URLSearchParams({
                        page: String(this.page),
                        per_page: String(this.perPage)
                    });
                    return `${base}?${params.toString()}`;
                },
                clearMessages() {
                    this.successMessage = '';
                    this.errorMessage = '';
                    this.infoMessage = '';
                    if (this.timers.successMessage) clearTimeout(this.timers.successMessage);
                    if (this.timers.infoMessage) clearTimeout(this.timers.infoMessage);
                },
                setSuccess(message) {
                    this.successMessage = message;
                    this.autoClear('successMessage');
                },
                setInfo(message) {
                    this.infoMessage = message;
                    this.autoClear('infoMessage');
                },
                setError(message) {
                    this.errorMessage = message;
                    // do not auto clear errors
                },
                autoClear(key) {
                    if (this.timers[key]) clearTimeout(this.timers[key]);
                    this.timers[key] = setTimeout(() => {
                        this[key] = '';
                    }, 3000);
                }
            };
        }
    </script>
@endsection
