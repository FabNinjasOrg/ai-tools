<?php

use App\Http\Controllers\ModelController;
use App\Http\Controllers\FaceFinderController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StorageController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as MiddlewareVerifyCsrfToken;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/text-generation', [ModelController::class, 'textGeneration'])->name('text_generation');
Route::get('/summary-generation', [ModelController::class, 'summaryGeneration'])->name('summary_generation');
// Route::get('/media-generation', [ModelController::class, 'mediaGeneration'])->name('media_generation');
Route::post('/chatbot', [ModelController::class, 'chatBot'])->name('chatbot');
Route::post('/summarybot', [ModelController::class, 'summaryBot'])->name('summarybot');
// Route::post('/mediaAnalysis', [ModelController::class, 'mediaAnalysis'])->name('mediaAnalysis');

// routes for face finder app
Route::prefix('face-finder')->group(function () {
    Route::withoutMiddleware([MiddlewareVerifyCsrfToken::class])->post('/webhook/razorpay', [SubscriptionController::class, 'handleRazorpayWebhook'])->name('webhook.razorpay');

    Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirectToGoogle'])->name('google.redirect');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('google.callback');

    Route::get('/', [FaceFinderController::class, 'faceFinder'])->name('face_finder');
    Route::get('/pricing', [FaceFinderController::class, 'pricing'])->name('face_finder.pricing');

    Route::middleware('auth')->group(function () {
        Route::get('upload-photos', [FaceFinderController::class, 'uploadPhotosPage'])->name('face_finder.upload_photos');
        Route::post('update-country-code', [FaceFinderController::class, 'updateCountryCode'])->name('face_finder.update_country_code');

        // Events routes
        Route::get('events', [FaceFinderController::class, 'index'])->name('face_finder.events.index');
        Route::get('events/create', [FaceFinderController::class, 'eventsCreate'])->name('face_finder.events.create');
        Route::get('load-events', [FaceFinderController::class, 'loadAlbums'])->name('face_finder.load_events');
        Route::post('events/store-name', [FaceFinderController::class, 'storeEvent'])->name('face_finder.events.store_name');
        // Route::post('events', [FaceFinderController::class, 'storeZip'])->name('face_finder.events.store');
        Route::get('events/{uuid}', [FaceFinderController::class, 'show'])->name('face_finder.events.show');
        Route::get('events/{uuid}/photos', [FaceFinderController::class, 'photos'])->name('face_finder.events.photos');
        Route::get('events/{uuid}/albums', [FaceFinderController::class, 'albums'])->name('face_finder.events.albums');
        Route::delete('events/{uuid}/bulk-delete-photos', [FaceFinderController::class, 'bulkDeletePhotos'])->name('face_finder.events.bulk_delete_photos');
        Route::get('events/{uuid}/upload-status', [FaceFinderController::class, 'zipfileUploadStatus'])->name('eventUploadStatus');
        Route::post('events/{uuid}/generate-public', [FaceFinderController::class, 'generatePublic'])->name('face_finder.events.generate_public');
        Route::delete('events/{uuid}', [FaceFinderController::class, 'deleteAlbum'])->name('face_finder.events.delete');
        // Album detail routes
        Route::get('albums/{id}', [FaceFinderController::class, 'albumShow'])->name('face_finder.albums.show');
        Route::get('albums/{id}/photos', [FaceFinderController::class, 'albumPhotos'])->name('face_finder.albums.photos');
        Route::post('albums/{id}/create-uploader-link', [FaceFinderController::class, 'createUploaderLink'])->name('face_finder.albums.create_uploader_link');
        Route::post('albums/{id}/update-uploader-link', [FaceFinderController::class, 'updateUploaderLink'])->name('face_finder.albums.update_uploader_link');
        Route::get('buy-subscription', [FaceFinderController::class, 'buySubscription'])->name('face_finder.buy_subscription');
        Route::get('manage-subscription', [FaceFinderController::class, 'manageSubscription'])->name('face_finder.manage_subscription');
        Route::get('storage', [StorageController::class, 'index'])->name('face_finder.storage');

        Route::get('profile', [ProfileController::class, 'edit'])->name('face_finder.profile.edit');
        Route::patch('profile', [ProfileController::class, 'update'])->name('face_finder.profile.update');
        Route::delete('profile', [ProfileController::class, 'destroy'])->name('face_finder.profile.destroy');

        Route::post('select-plan', [SubscriptionController::class, 'subscribePlan'])->name('face_finder.subscribe_plan');
        Route::post('cancel-subscription', [SubscriptionController::class, 'cancelSubscription'])->name('face_finder.subscription.cancel');
        Route::get('billing', [SubscriptionController::class, 'billing'])->name('face_finder.billing');
        Route::get('billing-data', [SubscriptionController::class, 'billingData'])->name('face_finder.billing.data');
    });

    Route::post('uploader/check-passcode', [FaceFinderController::class, 'checkPasscode'])->name('face_finder.uploader.check_passcode');
    Route::get('uploader/{uuid}/{albumId}', [FaceFinderController::class, 'uploaderPage'])->name('face_finder.uploader.show');
    Route::post('events/{uuid}/upload-photos', [FaceFinderController::class, 'uploadPhotosForEvent'])->name('face_finder.events.upload_photos');
});

Route::prefix('face-finder/public')->group(function () {
    Route::post('find-photos', [FaceFinderController::class, 'findPhotos'])->name('face_finder.public.find_photos');
    Route::get('{uuid}/matched-photos', [FaceFinderController::class, 'loadMatchedPhotos'])->name('face_finder.public.matched_photos');
    Route::post('{uuid}/otp-attempt', [FaceFinderController::class, 'logOtpAttempt'])->name('face_finder.public.otp_attempt');
    Route::post('{uuid}/otp-verified', [FaceFinderController::class, 'checkOtpSession'])->name('face_finder.public.otp_verified');
    Route::post('{uuid}/download-matched-photos-zip', [FaceFinderController::class, 'downloadMatchedPhotosZip'])->name('face_finder.public.download_matched_photos_zip');
    Route::get('{name}/{uuid}', [FaceFinderController::class, 'publicAlbumPage'])->name('face_finder.public.show');
});

// Authentication routes (added by Laravel Breeze) with face-finder URL prefix
Route::prefix('face-finder')->group(function () {
    require __DIR__.'/auth.php';
});