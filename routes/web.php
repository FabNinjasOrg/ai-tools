<?php

use App\Http\Controllers\ModelController;
use App\Http\Controllers\FaceFinderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/text-generation', [ModelController::class, 'textGeneration'])->name('text_generation');
Route::get('/summary-generation', [ModelController::class, 'summaryGeneration'])->name('summary_generation');
Route::get('/face-finder', [ModelController::class, 'faceFinder'])->name('face_finder');
// Route::get('/media-generation', [ModelController::class, 'mediaGeneration'])->name('media_generation');
Route::post('/chatbot', [ModelController::class, 'chatBot'])->name('chatbot');
Route::post('/summarybot', [ModelController::class, 'summaryBot'])->name('summarybot');
// Route::post('/mediaAnalysis', [ModelController::class, 'mediaAnalysis'])->name('mediaAnalysis');

Route::get('/face-finder/albums', [FaceFinderController::class, 'index'])->name('face_finder.albums.index');
Route::post('/face-finder/albums', [FaceFinderController::class, 'storeZip'])->name('face_finder.albums.store');
Route::get('/face-finder/albums/{uuid}', [FaceFinderController::class, 'show'])->name('face_finder.albums.show');
Route::get('/face-finder/albums/{uuid}/photos', [FaceFinderController::class, 'photos'])->name('face_finder.albums.photos');
Route::post('/face-finder/albums/{uuid}/generate-public', [FaceFinderController::class, 'generatePublic'])->name('face_finder.albums.generate_public');

Route::get('/album/{name}/{uuid}', [FaceFinderController::class, 'publicAlbumPage'])->name('face_finder.public.show');
Route::post('/face-finder/public/find-photos', [FaceFinderController::class, 'findPhotos'])->name('face_finder.public.find_photos');
