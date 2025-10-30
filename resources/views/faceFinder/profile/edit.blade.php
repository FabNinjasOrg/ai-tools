@extends('faceFinder.layout.sidebar-layout')

@section('page-title', 'Profile Settings')
@section('page-subtitle', 'Manage your account settings and preferences')

@section('content')
    <div class="max-w-7xl mx-auto px-6">

        <div class="space-y-6 max-w-6xl mx-auto">
            <!-- Profile and Password Side by Side -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="p-6 sm:p-8 bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                    @include('faceFinder.profile.partials.update-profile-information-form')
                </div>

                <div class="p-6 sm:p-8 bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                    @include('faceFinder.profile.partials.update-password-form')
                </div>
            </div>

            <!-- Delete Account Below -->
            <div class="p-6 sm:p-8 bg-white rounded-2xl border border-slate-200 shadow-sm">
                @include('faceFinder.profile.partials.delete-user-form')
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Enhanced save button feedback
        document.addEventListener('DOMContentLoaded', function() {
            const profileBtn = document.getElementById('profile-save-btn');
            const passwordBtn = document.getElementById('password-save-btn');

            if (profileBtn) {
                const profileForm = profileBtn.closest('form');
                if (profileForm) {
                    profileForm.addEventListener('submit', function(e) {
                        profileBtn.style.borderColor = '#10b981';
                        profileBtn.style.borderWidth = '2px';
                        profileBtn.classList.add('ring-2', 'ring-green-500', 'ring-offset-2');
                        setTimeout(() => {
                            profileBtn.style.borderColor = '';
                            profileBtn.style.borderWidth = '';
                            profileBtn.classList.remove('ring-2', 'ring-green-500', 'ring-offset-2');
                        }, 1500);
                    });

                    // Also add click feedback
                    profileBtn.addEventListener('click', function() {
                        profileBtn.style.borderColor = '#10b981';
                        profileBtn.style.borderWidth = '2px';
                        profileBtn.classList.add('ring-2', 'ring-green-500', 'ring-offset-2');
                        setTimeout(() => {
                            profileBtn.style.borderColor = '';
                            profileBtn.style.borderWidth = '';
                            profileBtn.classList.remove('ring-2', 'ring-green-500', 'ring-offset-2');
                        }, 1500);
                    });
                }
            }

            if (passwordBtn) {
                const passwordForm = passwordBtn.closest('form');
                if (passwordForm) {
                    passwordForm.addEventListener('submit', function(e) {
                        passwordBtn.style.borderColor = '#10b981';
                        passwordBtn.style.borderWidth = '2px';
                        passwordBtn.classList.add('ring-2', 'ring-green-500', 'ring-offset-2');
                        setTimeout(() => {
                            passwordBtn.style.borderColor = '';
                            passwordBtn.style.borderWidth = '';
                            passwordBtn.classList.remove('ring-2', 'ring-green-500', 'ring-offset-2');
                        }, 1500);
                    });

                    // Also add click feedback
                    passwordBtn.addEventListener('click', function() {
                        passwordBtn.style.borderColor = '#10b981';
                        passwordBtn.style.borderWidth = '2px';
                        passwordBtn.classList.add('ring-2', 'ring-green-500', 'ring-offset-2');
                        setTimeout(() => {
                            passwordBtn.style.borderColor = '';
                            passwordBtn.style.borderWidth = '';
                            passwordBtn.classList.remove('ring-2', 'ring-green-500', 'ring-offset-2');
                        }, 1500);
                    });
                }
            }
        });
    </script>
@endsection

