@extends('faceFinder.layout.sidebar-layout')

@section('page-title', 'Add Event')

@section('content')
    <div class="max-w-7xl mx-auto px-6">
        <div>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
                <div class="p-5 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 text-white inline-flex items-center justify-center">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-slate-900">Create New Event</h2>
                            <p class="text-xs text-slate-500">Upload photos for your event</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    @if ($errors->any())
                        <div class="mb-4 rounded-xl bg-red-50 border border-red-200 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Please correct the following errors:</h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <ul class="list-disc list-inside space-y-1">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('face_finder.events.store_name') }}" class="space-y-5">
                        @csrf
                        <div>
                            <label for="event_name" class="block text-sm font-medium text-slate-700">Event name</label>
                            <input
                                type="text"
                                id="event_name"
                                name="name"
                                value="{{ old('name') }}"
                                required
                                class="mt-2 block w-full rounded-xl border {{ $errors->has('name') ? 'border-red-300' : 'border-slate-300' }} px-3 py-2 text-slate-900 placeholder-slate-400 focus:border-purple-500 focus:ring-purple-500"
                                placeholder="e.g. John & Jane Wedding"
                            >
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="pt-2">
                            <button
                                type="submit"
                                class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-purple-600 to-pink-600 px-4 py-2 text-sm font-semibold text-white shadow hover:from-purple-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                                Create event
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
    </script>
@endsection

