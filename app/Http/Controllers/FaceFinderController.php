<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Album;
use App\Jobs\ProcessAlbumPhotoJob;
use App\Jobs\ProcessDirectPhotoUploadJob;
use App\Models\UploadSession;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class FaceFinderController extends Controller
{
    public function faceFinder()
    {
        if (Auth::check()) {
            return redirect()->route('face_finder.upload_photos');
        }
        return view('faceFinder.home');
    }

    public function uploadPhotosPage()
    {
        return view('faceFinder.face-finder');
    }

    public function updateCountryCode(Request $request)
    {
        $validated = $request->validate([
            'country_code' => 'required|string|max:3'
        ]);

        $user = Auth::user();

        // Only update if country_code is not already set
        if (empty($user->country_code)) {
            $user->country_code = strtoupper($validated['country_code']);
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Country code updated successfully'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Country code already set'
        ]);
    }

    public function uploadPhotosForEvent(string $uuid, Request $request)
    {
        $event = Event::where('uuid', $uuid)->firstOrFail(['id', 'user_id', 'uuid', 'name']);
        $userId = auth()->user()->id ?? $event->user_id;

        $validated = $request->validate([
            'zips' => 'nullable|array',
            'zips.*' => 'nullable|file|mimes:zip|max:1024000',
            'photos' => 'nullable|array',
            'photos.*' => 'nullable|file|image|mimes:png,jpg,jpeg,webp',
            'album_id' => 'nullable|integer|exists:albums,id',
        ], [
            'photos.*.image' => 'All files must be valid images.',
            'photos.*.mimes' => 'Photos must be in PNG, JPG, JPEG, or WEBP format.',
        ]);

        $allZipFiles = $request->file('zips', []);
        $allPhotoFiles = $request->file('photos', []);

        // if (!userHasAccessibility()) {
        //     $existingEvent = Event::where('user_id', $userId)->first();
        //     if ($existingEvent) {
        //         return response()->json([
        //             'message' => 'Trial users can create only one event. Please delete your existing event or upgrade your subscription.'
        //         ], 422);
        //     }
        // }

        if(userHasAccessibility()){
            if(isUserStorageFull()){
                return response()->json([
                    'message' => 'Your storage limit has been reached. Please upgrade your subscription to upload more photos. Or delete existing events to free up space.'
                ], 422);
            }
        }

        // Validate all zip files first
        $zipPhotoCount = 0;
        if (!empty($allZipFiles)) {
            $zipValidation = $this->validateZipFiles($allZipFiles, $userId);
            if (!$zipValidation['valid']) {
                return response()->json([
                    'message' => $zipValidation['message']
                ], 422);
            }
            $zipPhotoCount = $zipValidation['totalPhotoCount'] ?? 0;
        }

        // Validate all photo files
        $directPhotoCount = 0;
        if (!empty($allPhotoFiles)) {
            $directPhotoCount = count($allPhotoFiles);
        }

        // Check trial user photo limit - event should have maximum 10 photos total
        if (!userHasAccessibility()) {
            $existingPhotoCount = $event->photos()->count();
            $totalPhotosAfterUpload = $existingPhotoCount + $zipPhotoCount + $directPhotoCount;

            if ($totalPhotosAfterUpload > 10) {
                $newPhotosCount = $zipPhotoCount + $directPhotoCount;
                return response()->json([
                    'message' => "Trial users can have only 10 photos per event. This event currently has {$existingPhotoCount} photos. You are trying to upload {$newPhotosCount} more photos, which would exceed the limit. Upgrade your subscription for more."
                ], 422);
            }
        }

        $albumId = $request->input('album_id');

        // Prepare all zips using validated data
        $zipPreparations = [];
        if (!empty($allZipFiles) && !empty($zipValidation['data'])) {
            foreach ($zipValidation['data'] as $zipData) {
                $preparation = $this->prepareZipForProcessing($uuid, $albumId, $userId, $zipData);
                if (!$preparation) {
                    $this->cleanupZipFiles($zipValidation['data']);
                    return response()->json([
                        'message' => "Failed to process ZIP file: {$zipData['originalName']}. Please check the file and try again."
                    ], 422);
                }
                $zipPreparations[] = $preparation;
            }
        }

        // Zips prepared successfully, now dispatch all jobs
        $zipResults = [];
        foreach ($zipPreparations as $preparation) {
            $result = $this->dispatchZipJobs($preparation);
            if (!$result) {
                $this->cleanupZipFiles($zipValidation['data']);
                return response()->json([
                    'message' => "Failed to dispatch jobs for ZIP file: {$preparation['originalName']}. Please try again."
                ], 422);
            }
            $zipResults[] = $result;
        }

        // Process photos
        $photoResult = null;
        if (!empty($allPhotoFiles)) {
            $photoResult = $this->uploadPhotosToAlbum($uuid, $allPhotoFiles, $albumId, $userId);
            if (!$photoResult) {
                return response()->json([
                    'message' => 'Failed to process photos. Please try again.'
                ], 422);
            }
        }

        // Return success response only if everything succeeded
        $results = array_merge($zipResults, $photoResult ? [$photoResult] : []);

        if (!empty($results)) {
            return response()->json([
                'message' => 'Files are being uploaded. Please wait for a few minutes.',
                'results' => $results,
                'event' => [
                    'user_id' => $userId,
                    'event_id' => $event->id,
                    'album_id' => $albumId
                ]
            ]);
        }

        return response()->json([
            'message' => 'No files to upload.'
        ], 422);
    }

    private function validateZipFiles(array $zipFiles, int $userId): array
    {
        $allowedExtensions = ['png', 'jpg', 'jpeg', 'webp'];
        $totalPhotoCount = 0;
        $zipData = [];

        foreach ($zipFiles as $zipFile) {
            $originalName = $zipFile->getClientOriginalName();

            // Create temporary directory for zip storage
            $localTempDir = storage_path("app/tmp_zip_extract/{$userId}/" . uniqid());
            if (!is_dir($localTempDir)) {
                mkdir($localTempDir, 0775, true);
            }

            // Save zip file to temp location
            $localTempZipPath = $localTempDir . DIRECTORY_SEPARATOR . $originalName;
            file_put_contents($localTempZipPath, file_get_contents($zipFile));

            $zip = new ZipArchive();
            if ($zip->open($localTempZipPath) !== true) {
                @unlink($localTempZipPath);
                @rmdir($localTempDir);

                $this->cleanupZipFiles($zipData);
                return [
                    'valid' => false,
                    'message' => "Failed to open ZIP file: {$originalName}",
                    'data' => []
                ];
            }

            $photos = [];

            // Scan zip file for valid images
            for ($entryIndex = 0; $entryIndex < $zip->numFiles; $entryIndex++) {
                $zipEntryStat = $zip->statIndex($entryIndex);
                $zipEntryName = $zipEntryStat['name'] ?? '';

                // Skip directories
                if (str_ends_with($zipEntryName, '/')) {
                    continue;
                }

                $zipEntryNameLower = strtolower($zipEntryName);

                // Skip system files
                if (
                    str_starts_with($zipEntryNameLower, '__macosx/') ||
                    str_contains($zipEntryNameLower, '/._') ||
                    str_ends_with($zipEntryNameLower, '.ds_store')
                ) {
                    continue;
                }

                // Check file extension
                $fileExtension = pathinfo($zipEntryNameLower, PATHINFO_EXTENSION);
                if (!in_array($fileExtension, $allowedExtensions, true)) {
                    $zip->close();
                    @unlink($localTempZipPath);
                    @rmdir($localTempDir);

                    $this->cleanupZipFiles($zipData);
                    return [
                        'valid' => false,
                        'message' => "ZIP file '{$originalName}' contains invalid file types. Only PNG, JPG, JPEG, and WEBP images are allowed.",
                        'data' => []
                    ];
                }

                $photos[] = $zipEntryName;
            }

            $zip->close();

            // Validate ZIP contains photos
            if (empty($photos)) {
                @unlink($localTempZipPath);
                @rmdir($localTempDir);

                $this->cleanupZipFiles($zipData);
                return [
                    'valid' => false,
                    'message' => "ZIP file '{$originalName}' contains no valid photos. Please ensure your ZIP contains PNG, JPG, JPEG, or WEBP images only.",
                    'data' => []
                ];
            }

            $totalPhotoCount += count($photos);

            // Store zip file path and data for later use, not cleanup yet
            $zipData[] = [
                'file' => $zipFile,
                'originalName' => $originalName,
                'photoCount' => count($photos),
                'photos' => $photos,
                'tempZipPath' => $localTempZipPath,
                'tempDir' => $localTempDir
            ];
        }

        return [
            'valid' => true,
            'message' => 'All zip files validated successfully',
            'data' => $zipData,
            'totalPhotoCount' => $totalPhotoCount
        ];
    }

    private function prepareZipForProcessing($eventUuid, $albumId, $userId, array $zipData): ?array
    {
        $originalName = $zipData['originalName'];
        $photos = $zipData['photos'];
        $localTempZipPath = $zipData['tempZipPath'];
        $localTempDir = $zipData['tempDir'];

        // Check event
        $event = Event::where('uuid', $eventUuid)->first();
        if (!$event) {
            Log::error('Event not found', ['uuid' => $eventUuid]);
            @unlink($localTempZipPath);
            @rmdir($localTempDir);
            return null;
        }

        // Check album
        $album = Album::find($albumId);
        if (!$album) {
            Log::error('Album not found', ['album id' => $albumId]);
            @unlink($localTempZipPath);
            @rmdir($localTempDir);
            return null;
        }

        return [
            'eventId' => $event->id,
            'albumId' => $album->id,
            'userId' => $userId,
            'eventUuid' => $eventUuid,
            'originalName' => $originalName,
            'photos' => $photos,
            'tempZipPath' => $localTempZipPath,
            'tempDir' => $localTempDir
        ];
    }

    private function cleanupZipFiles(array $zipData): void
    {
        foreach ($zipData as $data) {
            if (isset($data['tempZipPath'])) {
                @unlink($data['tempZipPath']);
            }
            if (isset($data['tempDir'])) {
                @rmdir($data['tempDir']);
            }
        }
    }

    private function dispatchZipJobs(array $preparation): ?array
    {
        $eventId = $preparation['eventId'];
        $albumId = $preparation['albumId'];
        $userId = $preparation['userId'];
        $eventUuid = $preparation['eventUuid'];
        $photos = $preparation['photos'];
        $localTempZipPath = $preparation['tempZipPath'];
        $localTempDir = $preparation['tempDir'];

        if (!$albumId) {
            $album = Album::where('event_id', $eventId)->orderBy('created_at')->first();
            if (!$album) {
                // Create a default album if none exists
                $album = Album::create([
                    'event_id' => $eventId,
                    'name' => 'Main Album',
                ]);
            }
            $albumId = $album->id;
        }

        // Dispatch job to process photos
        $batchName = "event_{$albumId}_{$eventUuid}";
        $batchJobs = [];

        // Split into chunks, 100 photos per job
        foreach (array_chunk($photos, 2) as $chunk) {
            $batchJobs[] = new ProcessAlbumPhotoJob($eventId, $albumId, $userId, $eventUuid, $chunk, $localTempZipPath);
        }

        $batch = Bus::batch($batchJobs)
            ->name($batchName)
            ->onQueue('high')
            ->then(function (Batch $batch) {
                UploadSession::where('batch_id', $batch->id)
                    ->update([
                        'status' => 'complete',
                    ]);
            })
            ->catch(function (Batch $batch) {
                UploadSession::where('batch_id', $batch->id)
                    ->update([
                        'status' => 'fail',
                    ]);
            })
            ->finally(function () use ($localTempZipPath, $localTempDir) {
                @unlink($localTempZipPath);
                @rmdir($localTempDir);
            })
            ->dispatch();

        // Add uploader session
        $uploadSession = UploadSession::create([
            'user_id' => $userId,
            'event_id' => $eventId,
            'album_id' => $albumId,
            'batch_id' => $batch->id,
        ]);

        return [
            'upload_session_id' => $uploadSession->id,
            'batch_id' => $batch->id,
            'type' => 'zip'
        ];
    }

    public function uploadPhotosToAlbum(string $eventUuid, $photos, ?int $albumId = null, $userId)
    {
        $event = Event::query()->where('uuid', $eventUuid)->first();

        if (!$event) {
            Log::error('Event not found for photo upload', ['uuid' => $eventUuid]);
            return null;
        }

        if (!$albumId) {
            $album = Album::where('event_id', $event->id)->orderBy('created_at')->first();
            if (!$album) {
                // Create a default album if none exists
                $album = Album::create([
                    'event_id' => $event->id,
                    'name' => 'Main Album',
                ]);
            }
            $albumId = $album->id;
        }

        $batchName = "event_{$albumId}_{$eventUuid}";
        $batchJobs = [];

        // Prepare photo data with base64 encoding for queue serialization
        $photoData = [];
        foreach($photos as $photo){
            $photoData[] = [
                'filename' => $photo->getClientOriginalName(),
                'content' => base64_encode(file_get_contents($photo->getRealPath())),
                'size' => $photo->getSize(),
            ];
        }

        // Batch photos into groups of 100 and dispatch jobs
        foreach (array_chunk($photoData, 2) as $photoBatch) {
            $batchJobs[] = new ProcessDirectPhotoUploadJob($event->id, $albumId, $photoBatch);
        }

        $batch = Bus::batch($batchJobs)
            ->name($batchName)
            ->onQueue('high')
            ->then(function (Batch $batch) {
                UploadSession::where('batch_id', $batch->id)
                    ->update([
                        'status' => 'complete',
                    ]);
            })
            ->catch(function (Batch $batch) {
                UploadSession::where('batch_id', $batch->id)
                    ->update([
                        'status' => 'fail',
                    ]);
            })
            ->dispatch();

        // Add uploader session
        $uploadSession = UploadSession::create([
            'user_id' => $userId,
            'event_id' => $event->id,
            'album_id' => $albumId,
            'batch_id' => $batch->id,
        ]);

        return [
            'upload_session_id' => $uploadSession->id,
            'batch_id' => $batch->id,
            'type' => 'photo'
        ];
    }

    public function checkBatchStatus(Request $request)
    {
        $uploadSessionIds = $request->upload_session_ids;
        $activeUploads = [];

        $batchIds = UploadSession::whereIn('id', $uploadSessionIds)->pluck('batch_id')->toArray();

        foreach($batchIds as $batchId){
            $batch = Bus::findBatch($batchId);

            if ($batch) {
                $activeUploads[] = [
                    'name'     => "Batch : " . $batchId,
                    'progress' => $batch->progress(),
                    'finished' => $batch->finished(),
                ];
            }
        }

        return response()->json($activeUploads);
    }

    public function faq()
    {
        return view('faceFinder.faq');
    }
}
