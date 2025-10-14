@extends('app')

@section('content')

<div x-data="albumPage('{{ $uuid }}')" x-init="init()" x-cloak class="max-w-7xl mx-auto px-6 py-12">
    <div class="mb-6 flex items-start justify-between gap-4">
        <div>
            <h1 class="text-3xl md:text-5xl font-bold bg-gradient-to-r from-green-600 via-emerald-600 to-teal-600 bg-clip-text text-transparent" x-text="albumName"></h1>
        </div>
        <a href="{{ route('face_finder') }}" class="h-10 px-4 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 inline-flex items-center">Back</a>
    </div>

    <div x-cloak x-show="publicUrl || !loading" class="mb-8 rounded-2xl border border-slate-200 bg-white px-4 py-4">
        <div class="flex items-start justify-between gap-4">
            <div>
                <div class="text-sm font-medium text-slate-900">Public share link</div>
                <div class="text-[13px] text-slate-600 mt-1">Click Generate to create a public link for this album that you can share.</div>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <template x-if="publicUrl">
                    <div x-cloak class="inline-flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                        <input type="text" :value="publicUrl" readonly class="bg-transparent text-sm text-slate-700 w-56 truncate focus:outline-none">
                        <button @click="copyPublic()" class="text-emerald-700 text-sm hover:underline">Copy</button>
                    </div>
                </template>
                <button x-cloak x-show="!publicUrl" @click="generatePublic()" class="h-9 px-4 rounded-xl bg-gradient-to-r from-green-600 to-emerald-600 text-white hover:from-green-700 hover:to-emerald-700">Generate</button>
            </div>
        </div>
    </div>

    <div x-cloak class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-3">
        <template x-for="photo in photos" :key="photo.id">
            <div class="rounded-lg overflow-hidden border border-slate-200 bg-white hover:shadow-md transition-shadow w-40 h-45 flex-shrink-0 block">
                <img :src="photo.src" loading="lazy" class="object-cover w-full h-full" alt="" />
            </div>
        </template>
    </div>

    <div x-cloak class="flex justify-center mt-6" x-show="hasMore">
        <button @click="loadMore()" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200" :disabled="loading">
            <span x-show="!loading">Load more</span>
            <span x-show="loading">Loading…</span>
        </button>
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

        // lifecycle
        async init() {
            await this.loadMore();
        },

        // actions
        async loadMore() {
            if (this.loading || !this.hasMore) return;
            this.loading = true;
            try {
                const response = await fetch(this.getPhotosApiUrl(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                if (!response.ok) throw new Error('Failed to load photos');

                const payload = await response.json();
                this.albumName = payload?.album?.name || this.albumName;
                if (payload?.album?.public_url) this.publicUrl = payload.album.public_url;
                if (Array.isArray(payload.photos)) this.photos.push(...payload.photos);
                this.hasMore = Boolean(payload.has_more);
                this.page += 1;
            } catch (error) {
                console.error(error);
            } finally {
                this.loading = false;
            }
        },

        async generatePublic() {
            try {
                const endpoint = `{{ route('face_finder.albums.generate_public', ['uuid' => 'UUID_PLACEHOLDER']) }}`.replace('UUID_PLACEHOLDER', this.uuid);
                const res = await fetch(endpoint, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
                if (!res.ok) throw new Error('Failed to generate URL');
                const data = await res.json();
                this.publicUrl = data.public_url || '';
            } catch (error) {
                console.error(error);
            }
        },

        async copyPublic() {
            try { await navigator.clipboard.writeText(this.publicUrl); } catch (_) {}
        },

        // helpers
        getPhotosApiUrl() {
            const base = `{{ route('face_finder.albums.photos', ['uuid' => 'UUID_PLACEHOLDER']) }}`.replace('UUID_PLACEHOLDER', this.uuid);
            const params = new URLSearchParams({ page: String(this.page), per_page: String(this.perPage) });
            return `${base}?${params.toString()}`;
        }
    };
}
</script>
@endsection


