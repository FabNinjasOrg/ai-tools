@extends('faceFinder.layout.base')

@section('body-class', 'bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 text-slate-900 min-h-screen flex overflow-hidden')

@section('body')
    <div x-data="{ sidebarOpen: false }" class="flex w-full h-screen overflow-hidden">

        <!-- Mobile Sidebar Backdrop -->
        <div x-show="sidebarOpen"
             @click="sidebarOpen = false"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 lg:hidden"></div>

        <!-- Sidebar -->
        <aside class="fixed lg:static inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200 flex flex-col transform transition-transform duration-300 ease-in-out lg:translate-x-0 shadow-lg"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            <!-- Logo / Brand -->
            <div class="p-6 border-b border-slate-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 text-white inline-flex items-center justify-center font-bold text-lg shadow-lg">
                            FF
                        </div>
                        <div>
                            <h1 class="text-lg font-bold bg-gradient-to-r from-green-600 via-emerald-600 to-teal-600 bg-clip-text text-transparent">
                                Face Finder
                            </h1>
                            <p class="text-xs text-slate-500">Photo Management</p>
                        </div>
                    </div>
                    <!-- Mobile Close Button -->
                    <button @click="sidebarOpen = false"
                            class="lg:hidden p-2 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                <a href="{{ route('face_finder.upload_photos') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all {{ request()->routeIs('face_finder.upload_photos') ? 'bg-gradient-to-r from-green-600 to-emerald-600 text-white shadow-md' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    <span class="font-medium">Quick Upload</span>
                </a>

                <!-- Manage Events - Single Link -->
                <a href="{{ route('face_finder.events.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all {{ request()->routeIs('face_finder.events.index') || request()->routeIs('face_finder.events.show') ? 'bg-gradient-to-r from-green-600 to-emerald-600 text-white shadow-md' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="font-medium">Manage Events</span>
                </a>

                <!-- Albums - Single Link -->
                <a href="{{ route('face_finder.albums.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all {{ request()->routeIs('face_finder.albums.index') || request()->routeIs('face_finder.albums.show') ? 'bg-gradient-to-r from-green-600 to-emerald-600 text-white shadow-md' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <span class="font-medium">Albums</span>
                </a>

                <a href="{{ route('face_finder.storage') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all {{ request()->routeIs('face_finder.storage') ? 'bg-gradient-to-r from-green-600 to-emerald-600 text-white shadow-md' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                    </svg>
                    <span class="font-medium">Storage</span>
                </a>

                <a href="{{ route('face_finder.manage_subscription') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all {{ request()->routeIs('face_finder.manage_subscription') ? 'bg-gradient-to-r from-green-600 to-emerald-600 text-white shadow-md' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    <span class="font-medium">Manage Subscription</span>
                </a>

                <a href="{{ route('face_finder.billing') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all {{ request()->routeIs('face_finder.billing') ? 'bg-gradient-to-r from-green-600 to-emerald-600 text-white shadow-md' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="font-medium">Billing</span>
                </a>

                <a href="{{ route('face_finder.faq') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all {{ request()->routeIs('face_finder.faq') ? 'bg-gradient-to-r from-green-600 to-emerald-600 text-white shadow-md' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-medium">FAQs</span>
                </a>
            </nav>

            <!-- Profile Section (Bottom) -->
            <div class="border-t border-slate-200 p-4">
                <a href="{{ route('face_finder.profile.edit') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all group {{ request()->routeIs('face_finder.profile.*') ? 'bg-gradient-to-r from-green-600 to-emerald-600 shadow-md' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                    <div class="h-9 w-9 rounded-full bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center text-white font-semibold shadow-md text-sm">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold truncate {{ request()->routeIs('face_finder.profile.*') ? 'text-white' : 'text-slate-900' }}">{{ Auth::user()->name }}</p>
                        <p class="text-xs truncate {{ request()->routeIs('face_finder.profile.*') ? 'text-white/80' : 'text-slate-500' }}">View Profile</p>
                    </div>
                    <svg class="h-4 w-4 transition-opacity {{ request()->routeIs('face_finder.profile.*') ? 'text-white' : 'text-slate-400 opacity-0 group-hover:opacity-100' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <header class="bg-white border-b border-slate-200 px-4 lg:px-6 py-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <!-- Mobile Menu Button -->
                        <button @click="sidebarOpen = !sidebarOpen"
                                class="lg:hidden p-2 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-colors">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <div>
                            <h2 class="text-lg lg:text-xl font-bold text-slate-900">@yield('page-title', 'Dashboard')</h2>
                            <p class="text-xs lg:text-sm text-slate-600 hidden sm:block">@yield('page-subtitle', 'Welcome back, ' . Auth::user()->name . '!')</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('face_finder.buy_subscription') }}" data-force-consent
                           class="px-4 py-2 rounded-xl inline-flex items-center gap-2 transition-all {{ request()->routeIs('face_finder.buy_subscription') ? 'bg-gradient-to-r from-green-600 to-emerald-600 text-white shadow-md' : 'border border-slate-300 text-slate-700 hover:bg-slate-50' }}">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <span class="hidden sm:inline font-semibold">{{ userSubscriptionActivated() ? 'Upgrade Plan' : 'Buy Subscription' }}</span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit"
                                    class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors inline-flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H9" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7V5a2 2 0 00-2-2H6a2 2 0 00-2 2v14a2 2 0 002 2h5a2 2 0 002-2v-2" />
                                </svg>
                                <span class="hidden sm:inline">Log Out</span>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
                <!-- Centralized Messages -->
                <div class="px-6 lg:px-8 pt-6 lg:pt-8">
                    <!-- Success Message -->
                    <div x-show="$store.messages.success"
                         class="mb-4 rounded-xl border border-green-200 border-l-4 border-l-green-400 bg-green-50 px-4 py-3 shadow-sm"
                         style="display: none;">
                        <div class="flex items-start gap-3 text-green-700">
                            <div class="shrink-0 h-5 w-5 rounded-full bg-green-100 text-green-700 inline-flex items-center justify-center text-sm">
                                ✓
                            </div>
                            <div class="flex-1 text-sm" x-text="$store.messages.success"></div>
                            <button @click="$store.messages.success = ''" class="shrink-0 text-green-500 hover:text-green-700 text-xl leading-none">
                                ×
                            </button>
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div x-show="$store.messages.error"
                         class="mb-4 rounded-xl border border-red-200 border-l-4 border-l-red-400 bg-red-50 px-4 py-3 shadow-sm"
                         style="display: none;">
                        <div class="flex items-start gap-3 text-red-700">
                            <div class="shrink-0 h-5 w-5 rounded-full bg-red-100 text-red-700 inline-flex items-center justify-center text-sm">
                                !
                            </div>
                            <div class="flex-1 text-sm" x-text="$store.messages.error"></div>
                            <button @click="$store.messages.error = ''" class="shrink-0 text-red-500 hover:text-red-700 text-xl leading-none">
                                ×
                            </button>
                        </div>
                    </div>

                    <!-- Info Message -->
                    <div x-show="$store.messages.info"
                         class="mb-4 rounded-xl border border-blue-200 border-l-4 border-l-blue-400 bg-blue-50 px-4 py-3 shadow-sm"
                         style="display: none;">
                        <div class="flex items-start gap-3 text-blue-700">
                            <div class="shrink-0 h-5 w-5 rounded-full bg-blue-100 text-blue-700 inline-flex items-center justify-center text-sm">
                                i
                            </div>
                            <div class="flex-1 text-sm" x-text="$store.messages.info"></div>
                            <button @click="$store.messages.info = ''" class="shrink-0 text-blue-500 hover:text-blue-700 text-xl leading-none">
                                ×
                            </button>
                        </div>
                    </div>
                </div>

                @yield('content')
            </main>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var serverError = @json(session('error'));
                if (!serverError) return;

                (function waitForAlpine() {
                    try {
                        if (window.Alpine && Alpine.store && Alpine.store('messages')) {
                            Alpine.store('messages').showError(serverError, 0);
                            return;
                        }
                    } catch (e) {}
                    setTimeout(waitForAlpine, 50);
                })();
            });
        </script>
    @endif
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var serverSuccess = @json(session('success'));
                if (!serverSuccess) return;

                (function waitForAlpine() {
                    try {
                        if (window.Alpine && Alpine.store && Alpine.store('messages')) {
                            Alpine.store('messages').showSuccess(serverSuccess);
                            return;
                        }
                    } catch (e) {}
                    setTimeout(waitForAlpine, 50);
                })();
            });
        </script>
    @endif
    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var serverError = @json($errors->first());
                if (!serverError) return;

                (function waitForAlpine() {
                    try {
                        if (window.Alpine && Alpine.store && Alpine.store('messages')) {
                            Alpine.store('messages').showError(serverError, 0);
                            return;
                        }
                    } catch (e) {}
                    setTimeout(waitForAlpine, 50);
                })();
            });
        </script>
    @endif
@endsection
