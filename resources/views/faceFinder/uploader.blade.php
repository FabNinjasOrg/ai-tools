@extends('faceFinder.layout.base')

@section('body')
    <nav class="relative px-6 py-4">
        <div class="relative max-w-7xl mx-auto flex items-center justify-center">
            <div class="flex items-center space-x-2">
                <a href="{{ route('face_finder') }}" class="flex items-center gap-2">
                    <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-green-500 to-emerald-600 text-white inline-flex items-center justify-center font-bold text-sm">FF</div>
                    <span class="text-xl font-bold bg-gradient-to-r from-green-600 via-emerald-600 to-teal-600 bg-clip-text text-transparent">Face Finder</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="relative z-10 flex-1 px-6 py-12" x-data="{ message: '', messageType: '' }" x-ref="messageContainer">
        <div class="w-full max-w-7xl mx-auto space-y-6">
            <!-- Message Display -->
            <div x-show="message"
                 x-transition
                 class="rounded-lg px-4 py-3"
                 :class="messageType === 'success' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200'"
                 style="display: none;">
                <div class="flex items-center justify-between">
                    <span x-text="message"></span>
                    <button @click="message = ''" class="ml-4 text-xl leading-none">&times;</button>
                </div>
            </div>

            @if($data['isUserValidated'] && ($data['isLinkExpired'] || $data['isStorageFull']))
                <!-- Error Messages Section -->
                <div class="w-full max-w-3xl mx-auto">
                    <div class="bg-white rounded-2xl shadow-xl border-2 overflow-hidden">
                        <div class="bg-gradient-to-r from-slate-50 to-slate-100 px-8 py-6 border-b border-slate-200">
                            <h1 class="text-2xl font-bold text-slate-900 text-center">Upload Photos</h1>
                            <p class="text-base text-slate-600 mt-2 text-center">Sorry, Currently you can't upload photos. Please contact administration.</p>
                        </div>
                        <div class="p-8">
                            <div class="space-y-6">
                                @if($data['isLinkExpired'])
                                    <div class="flex items-start gap-6 p-6 rounded-xl bg-orange-50 border-2 border-orange-300">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-lg font-bold text-orange-900 mb-2">Upload Link Status</p>
                                            <p class="text-base text-orange-700 leading-relaxed">
                                                @if($data['startTime'] && $data['endTime'])
                                                    This upload link is not currently active. The active period is from <strong>{{ $data['startTime'] }}</strong> to <strong>{{ $data['endTime'] }}</strong>.
                                                @elseif($data['startTime'])
                                                    This upload link is not yet active. It will be available starting from <strong>{{ $data['startTime'] }}</strong>.
                                                @elseif($data['endTime'])
                                                    This upload link has expired. It was valid until <strong>{{ $data['endTime'] }}</strong>.
                                                @else
                                                    This upload link is not currently active.
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                @endif

                                @if($data['isStorageFull'])
                                    <div class="flex items-start gap-6 p-6 rounded-xl bg-red-50 border-2 border-red-300">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-lg font-bold text-red-900 mb-2">Storage Limit Reached</p>
                                            <p class="text-base text-red-700 leading-relaxed">Your storage limit has been reached. Please upgrade your subscription to upload more photos or delete existing events to free up space.</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Information Section -->
                    <div class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-lg bg-white/20 backdrop-blur-sm flex items-center justify-center">
                                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <h2 class="text-lg font-bold text-white">Event Details</h2>
                        </div>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-start gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200">
                            <div class="h-10 w-10 rounded-lg bg-blue-500 flex items-center justify-center flex-shrink-0">
                                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Event Name</p>
                                <p class="text-base font-bold text-slate-900 break-words">{{ $data['eventName'] ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200">
                            <div class="h-10 w-10 rounded-lg bg-blue-500 flex items-center justify-center flex-shrink-0">
                                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Album Name</p>
                                <p class="text-base font-bold text-slate-900 break-words">{{ $data['albumName'] ?? 'N/A' }}</p>
                            </div>
                        </div>
                        @if($data['startTime'] || $data['endTime'])
                        <div class="flex items-start gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Active Period For This Uploader Link</p>
                                <div class="space-y-2.5">
                                    @if($data['startTime'])
                                        <div class="flex items-center gap-3">
                                            <div class="h-8 w-8 rounded-lg bg-emerald-500 flex items-center justify-center flex-shrink-0 shadow-sm">
                                                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-xs font-medium text-slate-500 mb-0.5">Start Time</p>
                                                <p class="text-sm font-bold text-slate-900">{{ $data['startTime'] }}</p>
                                            </div>
                                        </div>
                                    @endif
                                    @if($data['endTime'])
                                        <div class="flex items-center gap-3">
                                            <div class="h-8 w-8 rounded-lg bg-rose-500 flex items-center justify-center flex-shrink-0 shadow-sm">
                                                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-xs font-medium text-slate-500 mb-0.5">End Time</p>
                                                <p class="text-sm font-bold text-slate-900">{{ $data['endTime'] }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    </div>

                @if(!$data['isUserValidated'])
                <!-- Passcode Form Card -->
                <div class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-slate-50 to-slate-100 px-6 py-4 border-b border-slate-200">
                        <h1 class="text-xl font-bold text-slate-900">Upload Photos</h1>
                        <p class="text-sm text-slate-600 mt-1">Enter pass code to continue, which is share in the email.</p>
                    </div>
                    <div class="p-6">
                        <form method="POST" action="{{ route('face_finder.uploader.check_passcode') }}" class="space-y-5" onsubmit="setTimezone(this)">
                            @csrf
                            <input type="hidden" name="timezone" id="timezone-passcode">
                            <div>
                                <label for="passcode" class="block text-sm font-semibold text-slate-700 mb-3">
                                    Pass Code
                                </label>
                                <input type="hidden" name="albumId" value="{{ $data['albumId'] }}">
                                <input
                                    type="text"
                                    id="passcode"
                                    name="passcode"
                                    value="{{ old('passcode') }}"
                                    placeholder="Enter your pass code"
                                    class="w-full px-4 py-3.5 rounded-xl border-2 border-slate-300 focus:border-green-500 focus:ring-0 focus:outline-none outline-none transition-all text-slate-900 placeholder:text-slate-400 @error('passcode') border-red-400 focus:border-red-500 focus:ring-0 @enderror"
                                >
                                @error('passcode')
                                    <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="pt-4">
                                <button
                                    type="submit"
                                    class="w-full px-6 py-4 text-lg rounded-xl bg-gradient-to-r from-green-600 to-emerald-600 text-white font-bold hover:from-green-700 hover:to-emerald-700 transition-all shadow-lg hover:shadow-xl transform hover:scale-[1.02] active:scale-[0.98]"
                                >
                                    Continue
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                @else
                    <!-- Uploader Section -->
                    <div class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-slate-50 to-slate-100 px-6 py-4 border-b border-slate-200">
                            <h1 class="text-xl font-bold text-slate-900">Upload Photos</h1>
                            <p class="text-sm text-slate-600 mt-1">Select photos or ZIP files to upload</p>
                        </div>
                        <div class="p-6" x-data="manageUploader()" x-clock>
                                <div x-ref="uppyContainer"></div>
                        </div>
                    </div>
                @endif
                </div>
            @endif
        </div>
    </main>
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

        function showSuccess(message) {
            const main = document.querySelector('[x-ref="messageContainer"]');
            if (main && Alpine.$data(main)) {
                const data = Alpine.$data(main);
                data.message = message;
                data.messageType = 'success';
                setTimeout(() => { data.message = ''; }, 5000);
            }
        }

        function showError(message) {
            const main = document.querySelector('[x-ref="messageContainer"]');
            if (main && Alpine.$data(main)) {
                const data = Alpine.$data(main);
                data.message = message;
                data.messageType = 'error';
            }
        }

        function manageUploader() {
            let uppyManager = null;
            const eventUuid = '{{ $data['eventUuid'] }}';

            return {
                async init(){
                    uppyManager = new window.UppyUploadManager({
                        container: this.$refs.uppyContainer,
                        inline: true,
                        onUpload: (files) => {
                            this.uploadPhotos(files);
                        },
                        onError: (message) => {
                            showError(message);
                        },
                    });
                },

                async uploadPhotos(files) {
                    if(!files || files.length === 0){
                        showError('Please select at least one photo to upload.');
                        return;
                    }

                    const formData = new FormData();
                    files.forEach((file) => {
                        if(!file) return;

                        const fileName = file.name || 'file';
                        const fileExtension = (fileName.toLowerCase().split('.').pop() || '').trim();

                        if(fileExtension == 'zip'){
                            formData.append('zips[]', file.data, fileName);
                        }else{
                            formData.append('photos[]', file.data, fileName);
                        }
                    });

                    formData.append('album_id', {{ $data['albumId'] }});

                    try {
                        const response = await fetch(
                            '{{ route('face_finder.events.upload_photos', ['uuid' => ':uuid']) }}'.replace(':uuid', eventUuid), {
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
                        showSuccess(data.message || 'Photos uploaded successfully.');

                        uppyManager.closeModal();
                        uppyManager.reset();

                    } catch (error) {
                        showError(error.message || 'Failed to upload photos. Please try again.');
                        uppyManager.reset();
                    }
                },
            }
        }
    </script>
@endsection
