@extends('app')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-12">
    <div class="mb-10 text-center">
        <h1 class="text-3xl md:text-5xl font-bold bg-gradient-to-r from-green-600 via-emerald-600 to-teal-600 bg-clip-text text-transparent">Analyze Media with AI</h1>
        <p class="text-slate-600 mt-4 max-w-2xl mx-auto">Upload media and get structured insights.</p>
    </div>

    <div x-data="mediaQnaApp()" x-cloak class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Column 1: Uploader -->
        <div>
            <div class="bg-white rounded-2xl border border-slate-200">
                <div class="p-5 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 text-white inline-flex items-center justify-center">AI</div>
                        <div>
                            <h2 class="text-base font-semibold text-slate-900">Upload Media</h2>
                            <p class="text-[13px] leading-[20px] text-slate-500">{{ $llmModel }}</p>
                        </div>
                    </div>
                </div>
                <div class="p-5 space-y-4">
                    <div class="flex flex-col md:flex-row gap-3">
                        <input x-ref="fileInput" type="file" @change="handleFileSelected($event)" accept="image/png,image/jpeg,application/pdf,.doc,.docx" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-[15px] focus:outline-none focus:ring-2 focus:ring-emerald-400 bg-white" />
                    </div>
                    <div class="pt-1">
                        <div class="flex items-start gap-3">
                            <input id="pdf_with_media" type="checkbox" x-model="pdfWithMedia" class="mt-1 h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-400" aria-describedby="pdf_with_media_help" />
                            <div class="leading-5">
                                <div class="flex items-center gap-1.5">
                                    <label for="pdf_with_media" class="text-[14px] font-medium text-slate-800">Also analyze images in PDF</label>
                                    <span class="relative inline-flex items-center group">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-4 w-4 text-slate-400 group-hover:text-slate-600" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.604 2.417a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25h.008v.008H12V8.25z"/>
                                        </svg>
                                        <div class="absolute z-10 hidden group-hover:block left-1/2 -translate-x-1/2 top-5 w-64 px-3 py-2 rounded-lg text-xs leading-normal bg-black text-white shadow-md">
                                            Check this if the PDF has images or scanned pages you want analyzed. Leave it unchecked for text-only PDFs to save time and cost.
                                        </div>
                                    </span>
                                </div>
                                <p id="pdf_with_media_help" class="text-[12px] text-slate-500 mt-0.5">If your PDF contains images, enable this to analyze the images too.</p>
                            </div>
                        </div>
                    </div>
                    <div class="text-[12px] text-slate-500">Max 3 MB. Allowed: PNG, JPG, PDF.</div>
                    <template x-if="fileErrorMessage">
                        <div class="text-[12px] text-red-600" x-text="fileErrorMessage"></div>
                    </template>

                    <template x-if="selectedFile">
                        <div class="border border-slate-200 rounded-xl">
                            <div class="flex items-center justify-between px-4 py-2 bg-slate-50 rounded-t-xl">
                                <div class="flex items-center gap-4 min-w-0">
                                    <div class="h-8 w-8 rounded-lg bg-slate-200 inline-flex items-center justify-center shrink-0" x-text="getFileIcon(selectedFile)"></div>
                                    <div class="min-w-0">
                                        <div class="text-sm font-medium text-slate-800 truncate" x-text="selectedFile.name"></div>
                                        <div class="text-[12px] text-slate-500" x-text="formatFileSize(selectedFile.size)"></div>
                                    </div>
                                </div>
                                <button class="text-slate-500 hover:text-red-600 ml-2" @click="clearSelectedFile()">Remove</button>
                            </div>
                            <template x-if="selectedImagePreview">
                                <div class="p-3 bg-white rounded-b-xl border-t border-slate-200">
                                    <img :src="selectedImagePreview" class="max-h-60 rounded-lg border border-slate-200" alt="preview" />
                                </div>
                            </template>
                        </div>
                    </template>

                    <div class="flex items-center justify-end">
                        <button type="button" @click="resetForm()" :disabled="isAsking" class="px-4 py-2 rounded-xl transition-colors" :class="isAsking ? 'bg-gray-100 border border-gray-200 text-gray-400 cursor-not-allowed' : 'border border-slate-300 text-slate-700 hover:bg-slate-50'">Reset</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columns 2 & 3 merged: Question (top) + Answer (bottom) -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                <!-- Question header -->
                <div class="p-4 border-b border-slate-200">
                    <h3 class="text-sm font-semibold text-slate-900">Ask a question</h3>
                    <p class="text-[13px] text-slate-500">Questions will reference uploaded files.</p>
                </div>
                <!-- Question body -->
                <div class="p-4 space-y-3">
                    <textarea x-model="userQuestion" rows="5" placeholder="e.g., Extract the invoice total and due date." @keydown.enter="if (!$event.shiftKey && selectedFile) { $event.preventDefault(); submitQuestion() }" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-[15px] focus:outline-none focus:ring-2 focus:ring-emerald-400 bg-white"></textarea>
                    <div class="flex items-center justify-end gap-3">
                        <button type="button" @click="userQuestion=''; answerText=''; errorMessage='';" :disabled="isAsking" class="px-4 py-2 rounded-xl transition-colors" :class="isAsking ? 'bg-gray-100 border border-gray-200 text-gray-400 cursor-not-allowed' : 'border border-slate-300 text-slate-700 hover:bg-slate-50'">Clear</button>
                        <button x-cloak type="button" @click="submitQuestion()" :disabled="isAsking || (!userQuestion.trim()) || !selectedFile" class="px-5 py-2.5 rounded-xl font-medium inline-flex items-center gap-2 transition-colors disabled:cursor-not-allowed" :class="(isAsking || (!userQuestion.trim()) || !selectedFile) ? 'bg-slate-200 text-slate-500' : 'bg-gradient-to-r from-green-600 to-emerald-600 text-white hover:from-green-700 hover:to-emerald-700'">
                            <svg x-cloak x-show="!isAsking" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 19V5m0 0l-5 5m5-5l5 5"/></svg>
                            <svg x-cloak x-show="isAsking" class="h-5 w-5 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10" class="opacity-25"/><path d="M4 12a8 8 0 018-8" class="opacity-75"/></svg>
                            <span x-text="isAsking ? 'Asking…' : 'Ask'">Ask</span>
                        </button>
                    </div>
                </div>
                <!-- Divider -->
                <div class="border-t border-slate-200"></div>
                <!-- Answer header -->
                <div class="p-4 border-b border-slate-200 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-slate-900">Answer</h3>
                    <div class="flex items-center gap-4 text-[12px] text-slate-500">
                        <div x-cloak x-show="latencySeconds !== null">Time: <span x-text="latencySeconds"></span> s</div>
                        <div x-cloak x-show="tokens !== null">
                            Tokens: <span x-text="tokens"></span>
                        </div>
                    </div>
                </div>
                <!-- Answer body -->
                <div class="p-4 space-y-3 overflow-y-auto">
                    <template x-if="errorMessage">
                        <div class="px-4 py-3 rounded-xl bg-red-50 text-red-700 border border-red-200" x-text="errorMessage"></div>
                    </template>
                    <template x-if="!errorMessage && !answerText">
                        <div class="text-slate-500 text-sm">Upload files and ask a question to see the answer here.</div>
                    </template>
                    <template x-if="answerText">
                        <div class="text-[15px] leading-6 text-slate-800 whitespace-pre-wrap whitespace-pre-line break-words" x-text="answerText"></div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function mediaQnaApp() {
    return {
        selectedFile: null,
        selectedImagePreview: '',
        userQuestion: '',
        isAsking: false,
        errorMessage: '',
        answerText: '',
        fileErrorMessage: '',
        latencySeconds: null,
        tokens: null,
        pdfWithMedia: false,

        isAllowedFile(file) {
            const maxBytes = 3 * 1024 * 1024;
            if (!file) return false;
            const name = (file.name || '').toLowerCase();
            const type = file.type || '';
            const isImage = type === 'image/png' || type === 'image/jpeg';
            const isPdf = type === 'application/pdf' || name.endsWith('.pdf');
            const okType = isImage || isPdf;
            const okSize = file.size <= maxBytes;
            return okType && okSize;
        },

        handleFileSelected(event) {
            const f = (event.target.files && event.target.files[0]) ? event.target.files[0] : null;
            if (!f) return;
            if (!this.isAllowedFile(f)) {
                this.fileErrorMessage = 'Unsupported file. Allowed: PNG, JPG, PDF. Max 3 MB.';
                this.selectedFile = null;
                this.selectedImagePreview = '';
                if (this.$refs.fileInput) this.$refs.fileInput.value = '';
                return;
            }
            this.fileErrorMessage = '';
            this.selectedFile = f;
            this.selectedImagePreview = f.type && f.type.startsWith('image/') ? URL.createObjectURL(f) : '';
        },
        resetForm() {
            this.selectedFile = null;
            this.selectedImagePreview = '';
            this.userQuestion = '';
            this.answerText = '';
            this.errorMessage = '';
            this.fileErrorMessage = '';
            this.latencySeconds = null;
            this.tokens = null;
            this.pdfWithMedia = false;
            this.$nextTick(() => {
                if (this.$refs.fileInput) {
                    this.$refs.fileInput.value = '';
                }
            });
        },
        clearSelectedFile() {
            this.selectedFile = null;
            this.selectedImagePreview = '';
            if (this.$refs.fileInput) this.$refs.fileInput.value = '';
        },
        async submitQuestion() {
            if (this.isAsking) return;

            this.errorMessage = '';
            this.answerText = '';
            this.latencySeconds = null;
            this.tokens = null;
            try {
                this.isAsking = true;
                if (this.selectedFile && !this.isAllowedFile(this.selectedFile)) {
                    this.fileErrorMessage = 'Unsupported file. Allowed: PNG, JPG, PDF. Max 3 MB.';
                    this.isAsking = false;
                    return;
                }
                const form = new FormData();
                form.append('question', this.userQuestion || '');
                if (this.selectedFile) {
                    form.append('file', this.selectedFile);
                }
                form.append('pdf_with_media', this.pdfWithMedia ? '1' : '0');

                const res = await fetch("{{ route('mediaAnalysis') }}", {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: form,
                });

                const data = await res.json();
                if (!res.ok) {
                    this.errorMessage = data.message || 'Failed to get answer.';
                    return;
                }
                this.answerText = data.response || '';
                this.latencySeconds = (typeof data.duration_s !== 'undefined') ? data.duration_s : null;
                this.tokens = (typeof data.total_tokens !== 'undefined') ? data.total_tokens : null;
            } catch (e) {
                console.error(e);
                this.errorMessage = 'Something went wrong. Please try again.';
            } finally {
                this.isAsking = false;
            }
        },
        getFileIcon(file) {
            if (!file) return '📁';
            if (file.type && file.type.startsWith('image/')) return '🖼️';
            if (file.name && file.name.match(/\.(pdf)$/i)) return '📄';
            return '📁';
        },
        formatFileSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024*1024) return (bytes/1024).toFixed(1) + ' KB';
            return (bytes/1024/1024).toFixed(1) + ' MB';
        }
    }
}
</script>
@endsection


