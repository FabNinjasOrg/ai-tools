@extends('app')

@section('content')
        <div x-data="llmConversation()" class="w-full max-w-5xl mx-auto flex flex-col h-[85vh] mb-10 rounded-3xl overflow-hidden shadow-[0_10px_30px_-10px_rgba(0,0,0,0.25)] border border-[#e3e3e0] bg-white">
            <!-- Header -->
            <div class="p-4 sm:p-5 border-b border-[#e3e3e0] bg-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-full bg-gradient-to-br from-indigo-500 to-violet-500 text-white inline-flex items-center justify-center shadow-md">AI</div>
                        <div>
                            <h1 class="text-base font-semibold text-[#1b1b18]">AI Summarizer</h1>
                            <div class="text-[13px] leading-[20px] text-[#706f6c]">{{ $llmModel }}</div>
                        </div>
                    </div>
                    <div class="hidden sm:flex items-center gap-3">
                        <div class="hidden md:flex items-center gap-2 text-[13px] text-[#706f6c]">
                            <span class="inline-block h-2 w-2 rounded-full bg-emerald-500"></span>
                            <span>Ready</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Split -->
            <div class="flex-1 flex h-full min-h-0">
                <!-- Left: Input Panel (Fixed Width) -->
                <div class="h-full w-[45%] p-4 sm:p-5 bg-white flex flex-col min-h-0">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-sm font-medium text-[#1b1b18]">Paste content to summarize</h2>
                    </div>

                    <form @submit.prevent="sendQuery()" class="relative flex flex-col flex-1 min-h-0">
                        <div class="flex-1 min-h-0">
                            <textarea x-ref="input" x-model="newQuery" placeholder="Paste or type your text here..."
                                    @keydown.enter="if (!$event.shiftKey) { $event.preventDefault(); sendQuery() }"
                                    class="h-full w-full resize-none px-4 py-3 border border-[#e3e3e0] focus:outline-none focus:ring-2 focus:ring-indigo-200 rounded-2xl text-[14px] leading-6 overflow-y-auto"></textarea>
                        </div>

                        <div class="mt-3 flex items-center justify-between">
                            <div class="inline-flex items-center gap-2">
                                <button type="button" @click="clearInput()" class="inline-flex items-center gap-2 bg-white border border-[#e3e3e0] hover:bg-gray-50 text-[#1b1b18] px-4 py-2 rounded-xl">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5">
                                        <path d="M3 6h18"/>
                                        <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                        <path d="M10 11v6"/>
                                        <path d="M14 11v6"/>
                                    </svg>
                                    <span>Clear</span>
                                </button>
                                <button type="submit" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5">
                                        <path d="M22 2L11 13"/>
                                        <path d="M22 2l-7 20-4-9-9-4 20-7z"/>
                                    </svg>
                                    <span>Summarize</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Right: Output Panel (Scrollable) -->
                <div class="h-full w-[55%] border-t md:border-t-0 md:border-l border-[#e3e3e0] bg-[#fbfbfa] flex flex-col min-h-0">
                    <div class="p-4 sm:p-5 border-b border-[#e3e3e0]">
                        <div class="flex items-center justify-between">
                            <h2 class="text-sm font-medium text-[#1b1b18]">Summary</h2>
                            <div class="flex items-center gap-4 text-[12px] text-[#706f6c]">
                                <div x-cloak x-show="latencySeconds !== null">Time: <span x-text="latencySeconds"></span> s</div>
                                <div x-cloak x-show="tokens !== null">
                                    Tokens: <span x-text="tokens"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-4">
                        <template x-for="(message, index) in messages" :key="index">
                            <div>
                                <!-- LLM Summary -->
                                <div x-show="message.role === 'llm'" class="text-left flex items-start gap-2">
                                    <div class="h-7 w-7 rounded-full bg-[#dbdbd7] shrink-0 inline-flex items-center justify-center">🤖</div>
                                    <div class="bg-gray-200 text-gray-900 rounded-2xl rounded-bl-none inline-block px-4 py-2 max-w-[90%] whitespace-pre-line break-words"
                                        x-text="message.content"></div>
                                </div>
                            </div>
                        </template>

                        <!-- Typing indicator -->
                        <div x-show="isLoading" class="flex items-start gap-3">
                            <div class="h-7 w-7 rounded-full bg-[#dbdbd7] shrink-0 inline-flex items-center justify-center">🤖</div>
                            <div class="bg-white border border-[#e3e3e0] text-[#1b1b18] rounded-2xl px-4 py-3">
                                <div class="h-4 w-4 border-2 border-gray-400 border-t-gray-600 rounded-full animate-spin"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
@endsection

@section('scripts')
    <script>
        let conversation = [
            { role: 'llm', content: "Paste your text on the left and I'll summarize it." },
        ];

        function llmConversation() {
            return {
                messages: conversation,
                newQuery: '',
                isLoading: false,
                latencySeconds: null,
                tokens: null,
                clearInput() {
                    this.newQuery = '';
                    this.$nextTick(() => this.$refs.input && this.$refs.input.focus());
                },
                async sendQuery() {
                    let userInput = this.newQuery.trim();
                    if (userInput === '') return;

                    // Clear previous response
                    this.messages = [];
                    this.latencySeconds = null;
                    this.tokens = null;

                    // Send the conversation to the server
                    try {
                        this.isLoading = true;
                        let response = await fetch("{{ route('summarybot')}}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                            },
                            body: JSON.stringify({ query: userInput }),
                        });

                        let responseData = await response.json();

                        if (response.ok) {
                            // Replace with the latest bot response only
                            this.messages = [{ role: 'llm', content: responseData.response }];
                            this.latencySeconds = (typeof responseData.duration_s !== 'undefined') ? responseData.duration_s : null;
                            this.tokens = (typeof responseData.total_tokens !== 'undefined') ? responseData.total_tokens : null;
                        } else {
                            console.error('Error:', responseData);
                            this.messages = [{ role: 'llm', content: "⚠️ Something went wrong. Please try again" }];
                        }

                    }catch (error) {
                        console.error('Error:', error);
                        this.messages = [{ role: 'llm', content: "⚠️ Something went wrong. Please try again" }];
                    } finally {
                        this.isLoading = false;
                    }
                }
            }
        }
    </script>
@endsection
