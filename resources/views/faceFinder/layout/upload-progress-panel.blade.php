<div
    x-data="manageUploadProgressPanel()"
    x-cloak
    class="pointer-events-none fixed bottom-4 right-4 z-[1100] flex w-full max-w-sm justify-end"
>
    <div
        x-show="openProgressBar"
        x-transition
        class="pointer-events-auto w-full rounded-2xl border border-slate-200 bg-white shadow-2xl"
    >
        <div class="flex items-center justify-between border-slate-100 px-4 py-3">
            <div>
                <p class="text-sm font-medium text-slate-900">Your Recent Upload Progress</p>
            </div>
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    @click="openProgressBar = false"
                    class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-slate-200 text-slate-500 transition hover:bg-slate-50"
                    aria-label="Hide upload progress panel"
                >
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="max-h-80 space-y-4 overflow-y-auto px-4 py-4" x-show="activeBatches.length > 0">
            <template x-for="batch in activeBatches" :key="batch.name">
                <div class="rounded-xl border border-slate-100 bg-slate-50/60 p-3 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3">
                            <div>
                                <p class="text-sm text-slate-900" x-text="batch.name"></p>
                            </div>
                        </div>
                        <span class="text-xs font-semibold text-slate-600" x-text="batch.progress + '%'">
                        </span>
                    </div>
                    <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-slate-200">
                        <div
                            class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-green-500 transition-all" :style="`width: ${batch.progress}%`"
                        ></div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
    function manageUploadProgressPanel()
    {
        return {
            openProgressBar: false,
            activeBatches: [],
            timer: null,

            async init() {
                this.$watch('$store.uploading_data.polling', (value) => {
                    if (value === true) {
                        if (this.hasUploadSessionIds()) {
                            this.openProgressBar = true;
                            this.startPolling();
                        }
                    } else {
                        this.stopPolling();
                        if (!this.hasUploadSessionIds()) {
                            this.openProgressBar = false;
                            this.activeBatches = [];
                        }
                    }
                });

                if (this.$store.uploading_data.polling === true && this.hasUploadSessionIds()) {
                    this.openProgressBar = true;
                    this.startPolling();
                    this.checkBatchStatus();
                }
            },

            startPolling()
            {
                if(this.timer !== null) return;

                this.timer = setInterval(() => {
                    this.checkBatchStatus();
                }, 2000);
            },

            stopPolling()
            {
                if(this.timer !== null)
                {
                    clearInterval(this.timer);
                    this.timer = null;
                }
            },

            hasUploadSessionIds()
            {
                const ids = Alpine.store('uploading_data').upload_session_ids;
                return Array.isArray(ids) && ids.length > 0;
            },

            async checkBatchStatus()
            {
                if(!this.hasUploadSessionIds())
                {
                    this.stopPolling();
                    this.openProgressBar = false;
                    this.activeBatches = [];
                    return;
                }

                const response = await fetch('{{ route('face_finder.check_batch_status') }}', {
                    method: "POST",
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        upload_session_ids: Alpine.store('uploading_data').upload_session_ids
                    })
                });

                if(!response.ok){
                    throw new Error('Failed to check the batch status');
                }

                const data = await response.json();

                if(data){
                    this.activeBatches = Array.isArray(data) ? data : [data];

                    this.openProgressBar = true;

                    // Check if all batches are finished
                    const allFinished = this.activeBatches.every(batch => batch.finished === true);

                    if (allFinished) {
                        Alpine.store('uploading_data').polling = false;
                        Alpine.store('uploading_data').upload_session_ids = [];
                        this.openProgressBar = false;
                        this.activeBatches = [];
                    }
                }
            },
        };
    }
</script>
