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
        Route::get('upload-album', [FaceFinderController::class, 'uploadAlbumPage'])->name('face_finder.upload_album');
        Route::post('update-country-code', [FaceFinderController::class, 'updateCountryCode'])->name('face_finder.update_country_code');
        Route::get('albums', [FaceFinderController::class, 'index'])->name('face_finder.albums.index');
        Route::post('albums', [FaceFinderController::class, 'storeZip'])->name('face_finder.albums.store');
        Route::get('albums/{uuid}', [FaceFinderController::class, 'show'])->name('face_finder.albums.show');
        Route::get('albums/{uuid}/photos', [FaceFinderController::class, 'photos'])->name('face_finder.albums.photos');
        Route::get('albums/{uuid}/upload-status', [FaceFinderController::class, 'zipfileUploadStatus'])->name('zipfileUploadStatus');
        Route::post('albums/{uuid}/generate-public', [FaceFinderController::class, 'generatePublic'])->name('face_finder.albums.generate_public');
        Route::delete('albums/{uuid}', [FaceFinderController::class, 'deleteAlbum'])->name('face_finder.albums.delete');
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