@extends('app')

@section('content')
        <div x-data="llmConversation()" class="w-full max-w-3xl mx-auto flex flex-col h-[85vh] mb-10 rounded-3xl overflow-hidden shadow-[0_10px_30px_-10px_rgba(0,0,0,0.25)] border border-[#e3e3e0] bg-white">
           <!-- Chat Header -->
           <div class="p-4 sm:p-5 border-b border-[#e3e3e0] bg-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-full bg-gradient-to-br from-blue-500 to-indigo-500 text-white inline-flex items-center justify-center shadow-md">AI</div>
                        <div>
                            <h1 class="text-base font-semibold text-[#1b1b18]">Chat Assistant</h1>
                            <div class="text-[13px] leading-[20px] text-[#706f6c]">{{ $llmModel }}</div>
                        </div>
                    </div>
                    <div class="hidden sm:flex items-center gap-2 text-[13px] text-[#706f6c]">
                        <span class="inline-block h-2 w-2 rounded-full bg-emerald-500"></span>
                        <span>Online</span>
                    </div>
                </div>
            </div>

            <!-- Chat Messages -->
            <div x-ref="scrollContainer" class="flex-1 overflow-y-auto p-4 space-y-4">
                <template x-for="(message, index) in messages" :key="index">
                    <div>
                        <!-- Bot message -->
                        <div x-show="message.role === 'llm'" class="text-left flex items-start gap-2">
                            <div class="h-7 w-7 rounded-full bg-[#dbdbd7] shrink-0 inline-flex items-center justify-center">🤖</div>
                            <div class="bg-gray-200 text-gray-900 rounded-2xl rounded-bl-none inline-block px-4 py-2 max-w-[75%] whitespace-pre-line break-words"
                                x-text="message.content"></div>
                        </div>

                        <!-- User message -->
                        <div x-show="message.role === 'user'" class="text-right flex items-start justify-end gap-2">
                            <div class="bg-blue-500 text-white rounded-2xl rounded-br-none inline-block px-4 py-2 max-w-[75%] whitespace-pre-line break-words"
                                x-text="message.content"></div>
                            <div class="h-7 w-7 rounded-full bg-[#dbdbd7] shrink-0 inline-flex items-center justify-center">🧑🏻</div>
                        </div>
                    </div>
                </template>

                <!-- Typing indicator -->
                <div x-show="isLoading" class="text-left flex items-start gap-2">
                    <div class="h-7 w-7 rounded-full bg-[#dbdbd7] shrink-0 inline-flex items-center justify-center">🤖</div>
                    <div class="bg-gray-200 text-gray-900 rounded-2xl rounded-bl-none inline-block px-4 py-2 max-w-[75%]">
                        <div class="h-4 w-4 border-2 border-gray-400 border-t-gray-600 rounded-full animate-spin"></div>
                    </div>
                </div>
            </div>

            <!-- Input Box -->
            <form @submit.prevent="sendQuery()" class="p-4 flex items-center space-x-2">
                <textarea x-model="newQuery" type="text" placeholder="Type a message..."
                    @keydown.enter="if (!$event.shiftKey) { $event.preventDefault(); sendQuery() }"
                    class="flex-1 px-4 py-2 border border-grey-100 rounded-xl"></textarea>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5">
                        <path d="M22 2L11 13"/>
                        <path d="M22 2l-7 20-4-9-9-4 20-7z"/>
                    </svg>
                </button>
            </form>
        </div>
@endsection

@section('scripts')
    <script>
        let conversation = [
            { role: 'llm', content: 'Hello! How can I help you today?' },
        ];

        function llmConversation() {
            return {
                init() {
                    this.$nextTick(() => this.scrollToBottom());
                    this.$watch('messages.length', () => this.scrollToBottom());
                    this.$watch('isLoading', () => this.scrollToBottom());
                },
                messages: conversation,
                newQuery: '',
                isLoading: false,
                async sendQuery() {
                    let userInput = this.newQuery.trim();
                    if (userInput === '') return;

                    // Add user message to conversation
                    this.messages.push({role: 'user', content: userInput});
                    this.newQuery = '';
                    this.scrollToBottom();

                    // Send the conversation to the server
                    try {
                        this.isLoading = true;
                        let response = await fetch("{{ route('chatbot')}}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                            },
                            body: JSON.stringify({ query: userInput }),
                        });

                        let responseData = await response.json();

                        if (response.ok) {
                            // Add bot response to conversation
                            this.messages.push({ role: 'llm', content: responseData.response });
                            this.scrollToBottom();
                        } else {
                            console.error('Error:', responseData);
                            this.messages.push({ role: 'llm', content: "⚠️ Something went wrong. Please try again" });
                            this.scrollToBottom();
                        }

                    }catch (error) {
                        console.error('Error:', error);
                        this.messages.push({ role: 'llm', content: "⚠️ Something went wrong. Please try again" });
                        this.scrollToBottom();
                    } finally {
                        this.isLoading = false;
                    }
                },
                scrollToBottom() {
                    this.$nextTick(() => {
                        const scroller = this.$refs.scrollContainer;
                        if (scroller) scroller.scrollTop = scroller.scrollHeight;
                    });
                }
            }
        }
    </script>
@endsection
