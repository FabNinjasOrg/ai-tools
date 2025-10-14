@extends('app')

@section('content')
<div x-data="albumPage('{{ $uuid }}')" x-init="init()" x-cloak class="max-w-7xl mx-auto px-6 py-12">
    <div class="mb-6">
        <h1 class="text-3xl md:text-5xl font-bold bg-gradient-to-r from-green-600 via-emerald-600 to-teal-600 bg-clip-text text-transparent">{{ $albumName }}</h1>
        <p class="text-slate-600 mt-2 text-sm">Public view | <span class="font-mono" x-text="uuid"></span></p>
        <div class="mt-2 text-[13px] text-slate-600 inline-flex items-center gap-2">
            <span class="h-5 w-5 rounded-full bg-emerald-100 text-emerald-700 inline-flex items-center justify-center font-semibold">i</span>
            <span>Images will show only when the match is 50% or higher.</span>
        </div>
    </div>
    <div x-cloak class="mb-8 relative rounded-2xl h-[40vh] md:h-[50vh]">
        <!-- Blurred placeholder image shapes when locked -->
        <div x-show="!matchedPhotos.length" class="absolute inset-0">
            <div class="h-full w-full p-4">
                <div class="grid grid-cols-2 grid-rows-2 gap-4 h-full w-full filter blur-sm opacity-80">
                    <div class="bg-slate-300/80 rounded-xl"></div>
                    <div class="bg-slate-400/80 rounded-xl"></div>
                    <div class="bg-slate-400/80 rounded-xl"></div>
                    <div class="bg-slate-300/80 rounded-xl"></div>
                </div>
            </div>
            <div class="absolute inset-0 bg-white/40"></div>
        </div>
        <div class="relative z-10 h-full flex flex-col items-center justify-center space-y-4">
            <!-- Message banners -->
            <template x-if="successMessage">
                <div class="w-full max-w-xl z-20">
                    <div class="rounded-xl border border-green-200 border-l-4 border-l-green-500 bg-green-50 px-4 py-3 text-sm shadow-sm text-green-800">
                        <div class="flex items-start gap-3">
                            <div class="shrink-0 h-5 w-5 rounded-full bg-green-100 text-green-700 inline-flex items-center justify-center">✓</div>
                            <div class="flex-1" x-text="successMessage"></div>
                            <button @click="successMessage=''" class="text-green-700/70 hover:text-green-800">✕</button>
                        </div>
                    </div>
                </div>
            </template>
            <template x-if="errorMessage">
                <div class="w-full max-w-xl z-20">
                    <div class="rounded-xl border border-red-200 border-l-4 border-l-red-500 bg-red-50 px-4 py-3 text-sm shadow-sm text-red-800">
                        <div class="flex items-start gap-3">
                            <div class="shrink-0 h-5 w-5 rounded-full bg-red-100 text-red-700 inline-flex items-center justify-center">!</div>
                            <div class="flex-1" x-text="errorMessage"></div>
                            <button @click="errorMessage=''" class="text-red-700/70 hover:text-red-800">✕</button>
                        </div>
                    </div>
                </div>
            </template>
            <template x-if="infoMessage">
                <div class="w-full max-w-xl z-20">
                    <div class="rounded-xl border border-slate-200 border-l-4 border-l-slate-400 bg-slate-50 px-4 py-3 text-sm shadow-sm text-slate-800">
                        <div class="flex items-start gap-3">
                            <div class="shrink-0 h-5 w-5 rounded-full bg-slate-100 text-slate-700 inline-flex items-center justify-center">i</div>
                            <div class="flex-1" x-text="infoMessage"></div>
                            <button @click="infoMessage=''" class="text-slate-600/70 hover:text-slate-800">✕</button>
                        </div>
                    </div>
                </div>
            </template>
            <!-- Results grid in the same area -->
            <div x-show="matchedPhotos.length > 0" x-cloak class="w-full border border-slate-200 rounded-xl p-4 bg-white/70 backdrop-blur">
                <div class="mb-3 text-center text-sm text-slate-800" x-text="`${matchedPhotos.length} matched ${matchedPhotos.length === 1 ? 'photo' : 'photos'}`"></div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                    <template x-for="photo in matchedPhotos" :key="photo.id">
                        <div class="rounded-lg overflow-hidden border border-slate-200 bg-white w-full">
                            <div class="w-full aspect-square">
                                <img :src="photo.src" :alt="photo.filename" class="w-full h-full object-cover" />
                            </div>
                            <div class="text-center text-[11px] text-slate-700 py-1" x-text="`${Math.round(((photo.similarity || 0) * 100))}% Matched`"></div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- No matches message in the same area -->
            <div x-show="showNoMatches" x-cloak class="w-full text-center">
                <div class="bg-white/70 backdrop-blur rounded-xl px-4 py-6 border border-slate-200">
                    <h3 class="text-base font-semibold text-slate-900 mb-1">No Matching Photos Found</h3>
                    <p class="text-slate-600 text-sm">Try taking another photo or check if the person appears in this album.</p>
                </div>
            </div>

            <!-- Unlock/Camera controls (hidden once results or no-matches are shown) -->
            <div x-show="!matchedPhotos.length && !showNoMatches" class="flex flex-col items-center space-y-4">
                <!-- Step 1: Unlock My Photos Button -->
                <template x-if="!showUnlockStep">
                    <button @click="showUnlockStep = true" class="px-6 py-4 rounded-xl bg-gradient-to-r from-green-600 to-emerald-600 text-white font-medium shadow hover:from-green-700 hover:to-emerald-700">
                        Unlock My Photos
                    </button>
                </template>

                <!-- Step 2: Camera Button and Info -->
                <template x-if="showUnlockStep">
                    <div class="flex flex-col items-center space-y-4">
                        <button @click="openCamera()" class="px-6 py-4 rounded-xl bg-gradient-to-r from-green-600 to-emerald-600 text-white font-medium shadow hover:from-green-700 hover:to-emerald-700 inline-flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Camera
                        </button>
                        <p class="text-slate-600 text-sm text-center max-w-md">Click your image to find your photos from the album</p>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Camera Modal -->
    <div x-show="showCamera" x-cloak class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-slate-900">Take Photo</h3>
                <button @click="closeCamera()" class="text-slate-400 hover:text-slate-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="mb-4">
                <video x-ref="videoElement" class="w-full h-64 bg-slate-100 rounded-lg object-cover" autoplay></video>
                <canvas x-ref="canvasElement" class="hidden"></canvas>
            </div>

            <div class="flex gap-3">
                <button @click="closeCamera()" class="flex-1 px-4 py-2 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50">Cancel</button>
                <button @click="capturePhoto()" :disabled="isProcessing" class="flex-1 px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:from-green-700 hover:to-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center justify-center gap-2">
                    <svg x-show="isProcessing" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10" class="opacity-25"/>
                        <path d="M4 12a8 8 0 018-8" class="opacity-75"/>
                    </svg>
                    <span x-text="isProcessing ? 'Processing...' : 'Capture'"></span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function albumPage(uuid) {
    return {
        uuid,
        showUnlockStep: false,
        showCamera: false,
        stream: null,
        matchedPhotos: [],
        showNoMatches: false,
        isProcessing: false,
        successMessage: '',
        errorMessage: '',
        infoMessage: '',

        init() {
            //
        },

        async openCamera() {
            try {
                this.stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'user',
                        width: { ideal: 640 },
                        height: { ideal: 480 }
                    }
                });
                this.$refs.videoElement.srcObject = this.stream;
                this.showCamera = true;
            } catch (error) {
                console.error('Error accessing camera:', error);
                this.setError('Unable to access camera. Please check permissions.');
            }
        },

        closeCamera() {
            this.showCamera = false;
            if (this.stream) {
                this.stream.getTracks().forEach(track => track.stop());
                this.stream = null;
            }
        },

        async capturePhoto() {
            const video = this.$refs.videoElement;
            const canvas = this.$refs.canvasElement;
            const context = canvas.getContext('2d');

            // Set canvas dimensions to match video
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;

            // Draw the video frame to canvas
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            // Convert canvas to blob
            canvas.toBlob(async (blob) => {
                if (blob) {
                    await this.processCapturedPhoto(blob);
                }
            }, 'image/jpeg', 0.8);

            this.closeCamera();
        },

        async processCapturedPhoto(blob) {
            this.isProcessing = true;
            this.matchedPhotos = [];
            this.showNoMatches = false;
            this.clearMessages();
            this.setInfo('Processing your photo…');

            try {
                const formData = new FormData();
                formData.append('photo', blob, 'captured-photo.jpg');
                formData.append('album_uuid', this.uuid);

                const response = await fetch('{{ route("face_finder.public.find_photos") }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });

                if (!response.ok) {
                    throw new Error('Failed to process photo');
                }

                const result = await response.json();

                if (result.success && result.matched_photos && result.matched_photos.length > 0) {
                    this.matchedPhotos = result.matched_photos;
                    this.showNoMatches = false;
                    this.setSuccess(`Found ${this.matchedPhotos.length} matching photo${this.matchedPhotos.length !== 1 ? 's' : ''}.`);
                } else {
                    this.matchedPhotos = [];
                    this.showNoMatches = true;
                    this.setInfo('No matching photos found for this image. Try another angle or better lighting.');
                }

            } catch (error) {
                console.error('Error sending photo:', error);
                this.matchedPhotos = [];
                this.showNoMatches = true;
                this.setError('Error processing photo. Please try again.');
            } finally {
                this.isProcessing = false;
                // Clear "processing" info once finished
                if (this.infoMessage && this.infoMessage.startsWith('Processing')) {
                    this.infoMessage = '';
                }
            }
        },

        // Message helpers
        clearMessages() {
            this.successMessage = '';
            this.errorMessage = '';
            this.infoMessage = '';
        },
        setSuccess(msg) {
            this.successMessage = msg;
            setTimeout(() => { if (this.successMessage === msg) this.successMessage = ''; }, 4000);
        },
        setError(msg) {
            this.errorMessage = msg;
        },
        setInfo(msg) {
            this.infoMessage = msg;
            setTimeout(() => { if (this.infoMessage === msg) this.infoMessage = ''; }, 3000);
        }
    };
}
</script>
@endsection
