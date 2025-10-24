<?php

use App\Http\Controllers\ModelController;
use App\Http\Controllers\FaceFinderController;
use App\Http\Controllers\GoogleAuthController;
use Illuminate\Support\Facades\Route;

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
    Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirectToGoogle'])->name('google.redirect');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('google.callback');

    Route::get('/', [FaceFinderController::class, 'faceFinder'])->name('face_finder');

    Route::middleware('auth')->group(function () {
        Route::get('upload-album', [FaceFinderController::class, 'uploadAlbumPage'])->name('face_finder.upload_album');
        Route::get('albums', [FaceFinderController::class, 'index'])->name('face_finder.albums.index');
        Route::post('albums', [FaceFinderController::class, 'storeZip'])->name('face_finder.albums.store');
        Route::get('albums/{uuid}', [FaceFinderController::class, 'show'])->name('face_finder.albums.show');
        Route::get('albums/{uuid}/photos', [FaceFinderController::class, 'photos'])->name('face_finder.albums.photos');
        Route::post('albums/{uuid}/generate-public', [FaceFinderController::class, 'generatePublic'])->name('face_finder.albums.generate_public');
        Route::delete('albums/{uuid}', [FaceFinderController::class, 'deleteAlbum'])->name('face_finder.albums.delete');
    });
});

Route::prefix('face-finder/public')->group(function () {
    Route::get('{name}/{uuid}', [FaceFinderController::class, 'publicAlbumPage'])->name('face_finder.public.show');
    Route::post('find-photos', [FaceFinderController::class, 'findPhotos'])->name('face_finder.public.find_photos');
    Route::post('{uuid}/otp-attempt', [FaceFinderController::class, 'logOtpAttempt'])->name('face_finder.public.otp_attempt');
    Route::post('{uuid}/otp-verified', [FaceFinderController::class, 'checkOtpSession'])->name('face_finder.public.otp_verified');
});

// Authentication routes (added by Laravel Breeze) with face-finder URL prefix
Route::prefix('face-finder')->group(function () {
    require __DIR__.'/auth.php';
});