<div
    x-show="isUploading"
    x-cloak
    class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/40 backdrop-blur-sm"
>
    <div class="bg-white/95 rounded-2xl px-8 py-6 shadow-2xl border border-white/40 text-center">
        <div class="inline-flex items-center justify-center mb-4">
            <svg class="animate-spin h-10 w-10 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
        </div>
        <p class="text-base font-medium text-slate-800">
            Loading, please wait…
        </p>
    </div>
</div>


