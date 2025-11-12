<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Album;
use App\Models\Photo;
use App\Models\UploaderLink;
use App\Models\OtpVerificationAttempt;
use App\Models\SubscriptionPlan;
use App\Jobs\ProcessAlbumPhotoJob;
use App\Jobs\ProcessDirectPhotoUploadJob;
use App\Services\CalculateUserStorageService;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use ZipArchive;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class FaceFinderController extends Controller
{
    private $calculateUserStorageService;

    public function __construct(CalculateUserStorageService $calculateUserStorageService)
    {
        $this->calculateUserStorageService = $calculateUserStorageService;
    }

    public function faceFinder()
    {
        if (Auth::check()) {
            return redirect()->route('face_finder.upload_photos');
        }
        return view('faceFinder.home');
    }

    public function pricing()
    {
        if (Auth::check()) {
            return redirect()->route('face_finder.buy_subscription');
        }

        $currency = 'USD'; // Always USD for guests
        $plans = $this->getPlanData($currency);

        return view('faceFinder.pricing', compact('plans', 'currency'));
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

    public function createUploaderLink(int $id, Request $request)
    {
        $album = Album::query()->where('id', $id)->first(['id','event_id','name']);

        $validated = $request->validate([
            'album_id' => ['required', 'integer', 'in:'.$album->id],
            'event_uuid' => ['required'],
            'start_at' => ['nullable', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
            'passcode' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $url = route('face_finder.uploader.show', ['uuid' => $request->event_uuid, 'albumId' => base64_encode($album->id)]);

        UploaderLink::create([
            'album_id' => $album->id,
            'url' => $url,
            'passcode' => $validated['passcode'] ?? null,
            'start' => $validated['start_at'] ?? null,
            'end' => $validated['end_at'] ?? null,
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('face_finder.albums.show', ['id' => $album->id, 'tab' => 'links'])
            ->with('success', 'Uploader link created successfully.');
    }

    public function updateUploaderLink(int $id, Request $request)
    {
        $album = Album::query()->where('id', $id)->firstOrFail(['id','event_id','name']);
        $link = UploaderLink::query()->where('album_id', $album->id)->latest('id')->firstOrFail();

        $validated = $request->validate([
            'start_at' => ['nullable', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
            'passcode' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $link->start = $validated['start_at'] ?? null;
        $link->end = $validated['end_at'] ?? null;
        $link->passcode = $validated['passcode'] ?? null;
        $link->status = $validated['status'];
        $link->save();

        return redirect()
            ->route('face_finder.albums.show', ['id' => $album->id, 'tab' => 'links'])
            ->with('success', 'Uploader link updated successfully.');
    }

    public function show(string $uuid)
    {
        $event = Event::query()->where('uuid', $uuid)->first();
        $attemptTotal = 0;
        $attemptUniquePhones = 0;
        $noMatchCount = 0;

        if ($event) {
            $attemptTotal = $event->otpAttempts()->count();

            $attemptUniquePhones = $event->otpAttempts()
                ->whereNotNull('phone_number')
                ->distinct('phone_number')
                ->count('phone_number');

            $noMatchCount =  $event->otpAttempts()->where('matched_found_photos', 0)->count();
        }

        return view('faceFinder.event', [
            'uuid' => $uuid,
            'attemptTotal' => $attemptTotal,
            'attemptUniquePhones' => $attemptUniquePhones,
            'noMatchCount' => $noMatchCount
        ]);
    }

    public function index(Request $request)
    {
        return view('faceFinder.events-list');
    }

    public function loadAlbums(Request $request)
    {
        $events = Event::query()
            ->where('user_id', auth()->id())
            ->where('upload_status', 'completed')
            ->withCount('photos')
            ->orderByDesc('created_at')
            ->get(['id', 'uuid', 'name', 'created_at']);

        $albumCounts = Album::query()
            ->whereIn('event_id', $events->pluck('id'))
            ->selectRaw('event_id, COUNT(*) as cnt')
            ->groupBy('event_id')
            ->pluck('cnt', 'event_id');

        return response()->json([
            'albums' => $events->map(function ($a) use ($albumCounts) {
                return [
                    'id' => $a->id,
                    'uuid' => $a->uuid,
                    'name' => $a->name,
                    'count' => $a->photos_count,
                    'albums_count' => (int) ($albumCounts[$a->id] ?? 0),
                    'created_at' => $a->created_at,
                ];
            })
        ]);
    }

    public function zipfileUploadStatus(string $uuid)
    {
        $event = Event::query()->where('uuid', $uuid)->first();

        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        return response()->json(['upload_status' => $event->upload_status]);
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

        if (!userHasAccessibility()) {
            $existingEvent = Event::where('user_id', $userId)->first();
            if ($existingEvent) {
                return response()->json([
                    'message' => 'Trial users can create only one event. Please delete your existing event or upgrade your subscription.'
                ], 422);
            }
        }

        if(isUserStorageFull()){
            return response()->json([
                'message' => 'Your storage limit has been reached. Please upgrade your subscription to upload more photos. Or delete existing events to free up space.'
            ], 422);
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

            // Check trial user photo limit (combined total of zip photos + direct photos)
            if (!userHasAccessibility() && ($zipPhotoCount + $directPhotoCount) > 10) {
                return response()->json([
                    'message' => "Trial users can upload up to 10 images only. You are trying to upload " . ($zipPhotoCount + $directPhotoCount) . " images total ({$zipPhotoCount} from ZIP files, {$directPhotoCount} direct photos). Upgrade your subscription for more."
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
            $photoResult = $this->uploadPhotosToAlbum($uuid, $allPhotoFiles, $albumId);
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
                    'uuid' => $event->uuid,
                    'name' => $event->name
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
        $batchName = "event_{$userId}_{$eventUuid}";
        $batchJobs = [];

        // Split into chunks, 100 photos per job
        foreach (array_chunk($photos, 100) as $chunk) {
            $batchJobs[] = new ProcessAlbumPhotoJob($eventId, $albumId, $userId, $eventUuid, $chunk, $localTempZipPath);
        }

        $batch = Bus::batch($batchJobs)
            ->name($batchName)
            ->onQueue('high')
            ->then(function (Batch $batch) use ($eventId) {
                try {
                    $event = Event::find($eventId);
                    if ($event) {
                        $event->upload_status = 'completed';
                        $event->save();
                    }
                } catch (\Throwable $e) {
                    Log::error('Failed to mark event completed', ['event_id' => $eventId, 'error' => $e->getMessage()]);
                }
            })
            ->catch(function (Batch $batch, \Throwable $e) use ($eventId) {
                try {
                    $event = Event::find($eventId);
                    if ($event) {
                        $event->upload_status = 'fail';
                        $event->save();
                    }
                } catch (\Throwable $ex) {
                    Log::error('Failed to mark event failed', ['event_id' => $eventId, 'error' => $ex->getMessage()]);
                }
            })
            ->finally(function () use ($localTempZipPath, $localTempDir) {
                @unlink($localTempZipPath);
                @rmdir($localTempDir);
            })
            ->dispatch();

        return [
            'batch_id' => $batch->id,
            'type' => 'zip'
        ];
    }


    public function uploadPhotosToAlbum(string $eventUuid, $photos, ?int $albumId = null)
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

        // Prepare photo data with base64 encoding for queue serialization
        $photoData = [];
        foreach($photos as $photo){
            $photoData[] = [
                'filename' => $photo->getClientOriginalName(),
                'content' => base64_encode(file_get_contents($photo->getRealPath())),
                'size' => $photo->getSize(),
            ];
        }

        // Batch photos into groups of 10 and dispatch jobs
        foreach (array_chunk($photoData, 10) as $photoBatch) {
            ProcessDirectPhotoUploadJob::dispatch($event->id, $albumId, $photoBatch)->onQueue('high');
        }

        return [
            'message' => 'Photos are being uploaded, Please wait for a few minutes. Once uploaded, photos will be visible here.',
            'type' => 'photos',
            'count' => count($photos)
        ];
    }

    public function photos(string $uuid, Request $request)
    {
        $event = Event::query()->where('uuid', $uuid)->firstOrFail(['id','uuid','name','public_url','uploader_url']);

        $perPage = $request->query('per_page', 24);

        $photos = Photo::query()
            ->where('event_id', $event->id)
            ->orderBy('id')
            ->paginate($perPage, ['id','filename','path','size_bytes']);

        return response()->json([
            'album' => [
                'uuid' => $event->uuid,
                'name' => $event->name,
                'public_url' => $event->public_url,
                'uploader_url' => $event->uploader_url,
            ],
            'photos' => $photos->map(function($p){
                return [
                    'id' => $p->id,
                    'src' => Storage::disk('s3')->temporaryUrl($p->path, now()->addDay()),
                    'size' => $p->size_bytes,
                ];
            }),
            'pagination' => [
                'current_page' => $photos->currentPage(),
                'last_page' => $photos->lastPage(),
                'per_page' => $photos->perPage(),
                'total' => $photos->total(),
                'has_more_pages' => $photos->hasMorePages(),
            ],
        ]);
    }

    public function albums(string $uuid, Request $request)
    {
        $event = Event::query()->where('uuid', $uuid)->firstOrFail(['id','uuid','name']);
        $albums = Album::query()
            ->where('event_id', $event->id)
            ->orderByDesc('id')
            ->get(['id','name','created_at']);

        return response()->json([
            'event_name' => $event->name,
            'albums' => $albums->map(function ($a) {
                return [
                    'id' => $a->id,
                    'name' => $a->name,
                    'created_at' => $a->created_at,
                ];
            })
        ]);
    }

    public function albumShow(int $id)
    {
        $album = Album::query()->where('id', $id)->firstOrFail(['id','event_id','name','created_at']);
        $event = Event::query()->where('id', $album->event_id)->firstOrFail(['id','uuid','name']);
        $uploaderLink = UploaderLink::query()->where('album_id', $album->id)->latest('id')->first();

        return view('faceFinder.album', [
            'albumId' => $album->id,
            'albumName' => $album->name,
            'eventUuid' => $event->uuid,
            'eventName' => $event->name,
            'uploaderLink' => $uploaderLink,
        ]);
    }

    public function albumPhotos(int $id, Request $request)
    {
        $album = Album::query()->where('id', $id)->firstOrFail(['id','event_id','name', 'uploader_url', 'created_at']);
        $event = Event::query()->where('id', $album->event_id)->firstOrFail(['id','uuid','name','public_url']);

        $perPage = $request->query('per_page', 24);

        $photos = Photo::query()
            ->where('event_id', $event->id)
            ->orderBy('id')
            ->paginate($perPage, ['id','filename','path','size_bytes']);

        return response()->json([
            'album' => [
                'id' => $album->id,
                'name' => $album->name,
                'event_uuid' => $event->uuid,
                'event_name' => $event->name,
                'public_url' => $event->public_url,
                'uploader_url' => $album->uploader_url,
            ],
            'photos' => $photos->map(function($p){
                return [
                    'id' => $p->id,
                    'src' => Storage::disk('s3')->temporaryUrl($p->path, now()->addDay()),
                    'size' => $p->size_bytes,
                ];
            }),
            'pagination' => [
                'current_page' => $photos->currentPage(),
                'last_page' => $photos->lastPage(),
                'per_page' => $photos->perPage(),
                'total' => $photos->total(),
                'has_more_pages' => $photos->hasMorePages(),
            ],
        ]);
    }

    public function generatePublic(string $uuid)
    {
        $event = Event::query()->where('uuid', $uuid)->firstOrFail();
        if ($event->public_url) {
            return response()->json(['public_url' => $event->public_url]);
        }

        // Generate URL /event/{name}/{uuid}
        $slugName = Str::slug($event->name);
        $publicUrl = route('face_finder.public.show', ['name' => $slugName, 'uuid' => $event->uuid]);
        $event->public_url = $publicUrl;
        $event->save();

        return response()->json(['public_url' => $publicUrl]);
    }

    public function deleteAlbum(string $uuid, Request $request)
    {
        $event = Event::query()->where('uuid', $uuid)->firstOrFail();

        try {
            $s3 = Storage::disk('s3');
            $s3Folder = "FaceFinder/Albums/{$event->user_id}/{$event->uuid}";

            // Delete all files in the event folder (ZIP + photos)
            if ($s3->exists($s3Folder)) {
                $s3->deleteDirectory($s3Folder);
            }

            // Delete OTP attempts for this event
            // OtpVerificationAttempt::query()->where('event_id', $event->id)->delete();

            // Finally delete event
            $event->delete();

            // Redirect back to upload events page
            return redirect()->route('face_finder.upload_photos')->with('status', 'event_deleted');
        } catch (\Throwable $e) {
            Log::error('Failed to delete event', ['uuid' => $uuid, 'error' => $e->getMessage()]);
            return back()->withErrors(['delete' => 'Failed to delete event']);
        }
    }

    public function publicAlbumPage(string $name, string $uuid)
    {
        $event = Event::query()->where('uuid', $uuid)->first(['name', 'user_id']);
        $eventName = $event->name ?? '';

        return view('faceFinder.public-event', [
            'uuid' => $uuid,
            'albumName' => $eventName
        ]);
    }

    public function logOtpAttempt(string $uuid, Request $request)
    {
        $event = Event::query()->where('uuid', $uuid)->firstOrFail(['id','uuid']);

        $data = $request->validate([
            'phone_number' => 'nullable|string|max:32',
        ]);

        try {
            $phoneNumber = $data['phone_number'] ?? null;
            $sessionToken = Str::random(40);

            // Check if there's already an entry for this phone number and event
            $existingAttempt = OtpVerificationAttempt::query()
                ->where('event_id', $event->id)
                ->where('phone_number', $phoneNumber)
                ->first();

            if ($existingAttempt) {
                $existingAttempt->increment('attempts');

                $existingAttempt->session_token = $sessionToken;
                $existingAttempt->save();

            } else {
                // Create new attempt
                OtpVerificationAttempt::create([
                    'event_id' => $event->id,
                    'album_uuid' => $event->uuid,
                    'phone_number' => $phoneNumber,
                    'ip_address' => $request->ip(),
                    'user_agent' => substr($request->userAgent() ?? '', 0, 512),
                    'attempts' => 1,
                    'matched_found_photos' => 0,
                    'session_token' => $sessionToken
                ]);
            }

            return response()->json(['ok' => true])
                ->cookie(
                    'otp_session_token',
                    $sessionToken,
                    180,   // minutes
                    '/',  // path
                    null, // domain
                    false, // secure
                    true   // httpOnly
                );
        } catch (\Throwable $e) {
            Log::error('Failed to log OTP attempt', ['error' => $e->getMessage()]);
            return response()->json(['ok' => false], 500);
        }
    }

    public function findPhotos(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg',
            'album_uuid' => 'required|string|exists:albums,uuid',
        ]);

        $photo = $request->file('photo');

        $eventUuid = $request->input('album_uuid');

        // Get event info
        $event = Event::query()->where('uuid', $eventUuid)->firstOrFail();

        try {
            // Get all photos with embeddings from the event
            $eventPhotos = Photo::query()
                ->where('event_id', $event->id)
                ->whereNotNull('embedding_json')
                ->get(['id', 'filename', 'path', 'embedding_json']);

            if ($eventPhotos->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No photos with face data found in this event',
                    'matched_photos' => []
                ]);
            }

            // Prepare embeddings for FastAPI
            $embeddings = $eventPhotos->map(function ($photo) {
                return json_decode($photo->embedding_json, true);
            })->filter()->values()->toArray();

            if (empty($embeddings)) {
                // Update state with no matches
                $this->saveMatchedPhotosData($event, collect());

                return response()->json([
                    'success' => false,
                    'message' => 'No valid face embeddings found in event photos',
                    'matched_photos' => []
                ]);
            }

            // Call FastAPI compare-face endpoint
            $baseUrl = env('FASTAPI_BASE_URL', 'http://fastapi:8005');
            $response = Http::attach('file', file_get_contents($photo->getPathname()), $photo->getClientOriginalName())
                ->timeout(60)
                ->post($baseUrl.'/compare-face/', [
                    'db_embeddings' => json_encode($embeddings)
                ]);

            if ($response->failed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Face comparison service unavailable',
                    'matched_photos' => []
                ]);
            }

            $comparisonResult = $response->json();

            if (!$comparisonResult['match']) {
                // Update state with no matches
                $this->saveMatchedPhotosData($event, collect());

                return response()->json([
                    'success' => true,
                    'message' => 'No matching faces found',
                    'matched_photos' => []
                ]);
            }

            // Get matched photos based on comparison results
            $matchedPhotos = collect();

            // Add matches if available
            if (isset($comparisonResult['all_results']) && is_array($comparisonResult['all_results'])) {
                foreach ($comparisonResult['all_results'] as $result) {
                    if (isset($result['index'])) {
                        $matchPhoto = $eventPhotos->get($result['index']);
                        if ($matchPhoto) {
                            $matchedPhotos->push([
                                'id' => $matchPhoto->id,
                                // 'filename' => $matchPhoto->filename,
                                // 'src' => Storage::disk('s3')->temporaryUrl($matchPhoto->path, now()->addDay()),
                                'similarity' => isset($result['similarity']) ? (float) $result['similarity'] : 0.0,
                            ]);
                        }
                    }
                }
            }

            // Save matched photo count and IDs if user is verified
            $this->saveMatchedPhotosData($event, $matchedPhotos);

            return response()->json([
                'success' => true,
                'message' => 'Face comparison completed',
                'matched_photos' => $matchedPhotos->values()->toArray(),
                'total_matches' => $matchedPhotos->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Face comparison error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error processing face comparison',
                'matched_photos' => []
            ]);
        }
    }

    private function saveMatchedPhotosData($event, $matchedPhotos)
    {
        try {
            $sessionToken = request()->cookie('otp_session_token');

            if (!$sessionToken) {
                return; // No session token, skip saving
            }

            // Find the existing attempt for this session and event
            $existingAttempt = OtpVerificationAttempt::query()
                ->where('event_id', $event->id)
                ->where('session_token', $sessionToken)
                ->first();

            if ($existingAttempt) {
                // Create array with photo IDs and their matching percentages
                $matchedPhotosData = $matchedPhotos->map(function($photo) {
                    return [
                        'id' => $photo['id'],
                        'percentage' => isset($photo['similarity']) ? round($photo['similarity'] * 100, 2) : 0
                    ];
                })->toArray();

                // Update the matched_found_photos count and photo data with percentages
                $existingAttempt->update([
                    'matched_found_photos' => $matchedPhotos->count(),
                    'matched_photo_id_json' => json_encode($matchedPhotosData),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Failed to save matched photos data', ['error' => $e->getMessage()]);
        }
    }

    public function checkOtpSession(string $uuid, Request $request)
    {
        $event = Event::query()->where('uuid', $uuid)->firstOrFail(['id','uuid']);

        $sessionToken = $request->cookie('otp_session_token');

        if (!$sessionToken) {
            return response()->json(['verified' => false]);
        }

        $existingAttempt = OtpVerificationAttempt::query()
            ->where('event_id', $event->id)
            ->where('session_token', $sessionToken)
            ->first();

        if ($existingAttempt) {
            return response()->json([
                'verified' => true
            ]);
        } else {
            return response()->json(['verified' => false]);
        }
    }

    public function loadMatchedPhotos(string $uuid, Request $request)
    {
        $event = Event::query()->where('uuid', $uuid)->firstOrFail(['id','uuid']);

        $sessionToken = $request->cookie('otp_session_token');

        if (!$sessionToken) {
            return response()->json([
                'success' => false,
                'message' => 'Session not verified',
                'matched_photos' => [],
                'has_more' => false
            ], 401);
        }

        $existingAttempt = OtpVerificationAttempt::query()
            ->where('event_id', $event->id)
            ->where('session_token', $sessionToken)
            ->first();

        if (!$existingAttempt) {
            return response()->json([
                'success' => false,
                'message' => 'Session not found',
                'matched_photos' => [],
                'has_more' => false
            ], 404);
        }

        $matchedPhotosData = $existingAttempt->matched_photo_id_json ? json_decode($existingAttempt->matched_photo_id_json, true) : [];

        // Pagination parameters
        $page = max(1, (int) $request->query('page', 1));
        $perPage = 24;
        $offset = ($page - 1) * $perPage;

        // Extract photo IDs and create percentage lookup
        $allPhotoIds = array_column($matchedPhotosData, 'id');
        $percentageLookup = array_column($matchedPhotosData, 'percentage', 'id');
        $totalPhotos = count($allPhotoIds);

        // Paginate photo IDs first (slice before database query)
        $paginatedPhotoIds = array_slice($allPhotoIds, $offset, $perPage);

        // Fetch only the photos for current page
        $matchedPhotos = [];
        if (!empty($paginatedPhotoIds)) {
            $photos = Photo::query()
                ->whereIn('id', $paginatedPhotoIds)
                ->get(['id', 'filename', 'path']);

            // Generate S3 URLs only for current page photos
            $matchedPhotos = $photos->map(function($photo) use ($percentageLookup) {
                return [
                    'id' => $photo->id,
                    'filename' => $photo->filename,
                    'src' => Storage::disk('s3')->temporaryUrl($photo->path, now()->addDay()),
                    'similarity' => ($percentageLookup[$photo->id] ?? 0) / 100,
                ];
            })->toArray();
        }

        // Check if there are more photos
        $hasMore = ($offset + $perPage) < $totalPhotos;

        return response()->json([
            'success' => true,
            'matched_photos' => $matchedPhotos,
            'has_more' => $hasMore
        ]);
    }

    public function downloadMatchedPhotosZip(string $uuid, Request $request)
    {
        $event = Event::query()->where('uuid', $uuid)->firstOrFail(['id','uuid']);

        $data = $request->validate([
            'photo_ids' => 'required|array',
            'photo_ids.*' => 'integer|exists:photos,id'
        ]);

        $sessionToken = $request->cookie('otp_session_token');

        if (!$sessionToken) {
            return response()->json(['error' => 'Session not verified'], 401);
        }

        // Verify session token
        $existingAttempt = OtpVerificationAttempt::query()
            ->where('event_id', $event->id)
            ->where('session_token', $sessionToken)
            ->first();

        if (!$existingAttempt) {
            return response()->json(['error' => 'Session not verified'], 401);
        }

        try {
            // Dispatch job to prepare ZIP file
            PrepareMatchedPhotosZip::dispatch($uuid, $data['photo_ids'], $sessionToken);

            return response()->json([
                'message' => 'ZIP file preparation started',
                'status' => 'processing'
            ]);

        } catch (\Throwable $e) {
            Log::error('Failed to dispatch ZIP preparation job', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to start ZIP preparation'], 500);
        }
    }

    public function buySubscription()
    {
        $user = Auth::user();
        $currency = 'USD';

        if ($user && $user->country_code === 'IN') {
            $currency = 'INR';
        }

        $plans = $this->getPlanData($currency);

        $currentPlanId = null;
        $paymentPending = userSubscribedButPaymentPending();

        if (userSubscriptionActivated()) {
            $currentSubscription = $user->subscriptions()
                ->where('type', 'subscription')
                ->where('status', 'active')
                ->latest()
                ->first();

            if ($currentSubscription && $currentSubscription->plan_id) {
                $currentPlanId = $currentSubscription->plan_id;
            }
        }

        return view('faceFinder.buy-subscription', compact('plans', 'currency', 'currentPlanId', 'paymentPending'));
    }

    public function manageSubscription()
    {
        if (!userHasAccessibility()) {
            return redirect()->route('face_finder.buy_subscription');
        }

        $user = Auth::user();
        $currency = 'USD';

        if ($user && $user->country_code === 'IN') {
            $currency = 'INR';
        }

        $currentSubscription = $user->subscriptions()
            ->where('type', 'subscription')
            ->latest()
            ->first();

        $planData = null;
        $subscriptionDetails = null;

        if ($currentSubscription && $currentSubscription->plan_id) {
            // Get plan data for the specific plan
            $planData = $this->getPlanData($currency, $currentSubscription->plan_id);

            // Build subscription details
            $subscriptionDetails = [
                'subscription_id' => $currentSubscription->subscription_id,
                'status' => $currentSubscription->status,
                'start_date' => $currentSubscription->start_date,
                'end_date' => $currentSubscription->end_date,
                'payment_gateway' => $currentSubscription->payment_gateway,
            ];
        }
        $currencySymbol = $currency === 'INR' ? '₹' : '$';

        return view('faceFinder.manage-subscription', [
            'currency' => $currency,
            'currencySymbol' => $currencySymbol,
            'planData' => $planData,
            'subscriptionDetails' => $subscriptionDetails
        ]);
    }

    private function getPlanData($currency = 'USD', $planId = null)
    {
        $subscriptionPlan = SubscriptionPlan::query()
            ->with(['prices' => function ($query) {
                $query->active();
            }]);

        // If plan ID is provided, get specific plan
        if ($planId) {
            $subscriptionPlan->where('id', $planId);
            $plan = $subscriptionPlan->first();

            if (!$plan) {
                return null;
            }

            return $this->formatPlanData($plan, $currency);
        }

        // Get all active plans
        $plans = $subscriptionPlan->active()->orderBy('id')->get();

        return $plans->map(function ($plan) use ($currency) {
            return $this->formatPlanData($plan, $currency);
        })->toArray();
    }

    private function formatPlanData($plan, $currency)
    {
        $priceField = $currency === 'INR' ? 'inr_price' : 'usd_price';
        $planIdField = $currency === 'INR' ? 'razorpay_plan_id' : 'stripe_plan_id';

        $price = $plan->prices->first();

        return [
            'id' => $plan->id,
            'name' => $plan->name,
            'plan_id' => $plan->$planIdField,
            'storage' => $plan->storage,
            'price' => $price ? $price->$priceField : 0,
            'amount' => $price ? $price->$priceField : null,
            'billing_type' => str_contains(strtolower($plan->name), 'monthly') ? 'monthly' : 'yearly',
        ];
    }

    public function bulkDeletePhotos(string $uuid, Request $request)
    {
        $event = Event::query()->where('uuid', $uuid)->where('user_id', auth()->id())->firstOrFail();

        $validated = $request->validate([
            'photo_ids' => 'required|string',
        ]);

        try {
            $photoIds = json_decode($validated['photo_ids'], true);

            if (!is_array($photoIds) || empty($photoIds)) {
                return redirect()->back()->withErrors('No photos selected for deletion.');
            }

            $photos = Photo::query()
                ->where('event_id', $event->id)
                ->whereIn('id', $photoIds)
                ->get();

            if ($photos->isEmpty()) {
                return redirect()->back()->withErrors('No valid photos found to delete.');
            }

            $s3 = Storage::disk('s3');
            $deletedCount = 0;

            foreach ($photos as $photo) {
                try {
                    // Delete from S3
                    if ($s3->exists($photo->path)) {
                        $s3->delete($photo->path);
                    }

                    // Delete from database
                    $photo->delete();
                    $deletedCount++;
                } catch (\Throwable $e) {
                    Log::error('Failed to delete photo', [
                        'photo_id' => $photo->id,
                        'error' => $e->getMessage()
                    ]);
                    continue;
                }
            }

            return redirect()->back()->with('success', "Successfully deleted {$deletedCount} photo(s).");
        } catch (\Throwable $e) {
            Log::error('Bulk delete photos error', ['error' => $e->getMessage()]);
            return redirect()->back()->withErrors('Failed to delete photos. Please try again.');
        }
    }

    // public function storeZip(Request $request)
    // {
    //     // Get user's first event or create a new one
    //     $event = Event::where('user_id', auth()->id())->first();

    //     if (!$event) {
    //         // Create a new event with default name
    //         $uuid = (string) Str::uuid();
    //         $event = Event::create([
    //             'user_id' => auth()->id(),
    //             'uuid' => $uuid,
    //             'name' => 'My Event',
    //             'photos_count' => 0,
    //             'upload_status' => 'completed',
    //         ]);
    //     }

    //     // Redirect to the common upload function
    //     return $this->uploadPhotosForEvent($event->uuid, $request);
    // }

    public function eventsCreate(Request $request)
    {
        return view('faceFinder.events-create');
    }

    public function storeEvent(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'album_name' => 'required|string|max:255',
        ]);

        $userId = auth()->id();
        $uuid = (string) Str::uuid();

        // Check if user is on trial and enforce limits
        if (!userHasAccessibility()) {
            $existingEvent = Event::where('user_id', $userId)->first();
            if ($existingEvent) {
                return redirect()->back()->withErrors([
                    'name' => 'Trial users can create only one event. Please delete your existing event or upgrade your subscription.'
                ]);
            }
        }

        $event = Event::create([
            'user_id' => $userId,
            'uuid' => $uuid,
            'name' => $validated['name'],
            'photos_count' => 0,
            'upload_status' => 'completed',
        ]);

        // Create an Album
        Album::create([
            'event_id' => $event->id,
            'name' => $validated['album_name'],
        ]);

        return redirect()->route('face_finder.events.show', ['uuid' => $event->uuid]);
    }

    public function uploaderPage($uuid, $albumId, Request $request)
    {
        $cookiePasscode = $request->cookie('uploader_passcode') ?? base64_decode($request->cookie('uploader_passcode'));
        $isUserValidated = false;
        $isStorageFull = false;
        $isLinkExpired = false;

        if(isset($cookiePasscode) && !empty($cookiePasscode)){
            $isUserValidated = true;
        }

        $encodedAlbumId = base64_decode($albumId);

        $album = Album::with(['event' => function ($query) use ($uuid) {
            $query->where('uuid',  $uuid);
        }, 'event.user'])->find($encodedAlbumId);

        $uploaderLink = UploaderLink::where('album_id', $album->id)->first();

        if ($isUserValidated && $album && $album->event) {
            if ($uploaderLink) {
                $now = Carbon::now();
                $startDate = $uploaderLink->start ? Carbon::parse($uploaderLink->start) : null;
                $endDate = $uploaderLink->end ? Carbon::parse($uploaderLink->end) : null;

                if (($startDate && $now->lt($startDate)) || ($endDate && $now->gt($endDate))) {
                    $isLinkExpired = true;
                }
            }

            if (!$isLinkExpired && $album->event->user) {
                $isStorageFull = isUserStorageFull($album->event->user);
            }
        }

        $data = [
            'albumId' => $album ? $album->id : null,
            'eventUuid' => $album->event ? $album->event->uuid : null,
            'eventName' => $album->event ? $album->event->name : 'Not Available',
            'albumName' => $album ? $album->name : 'Not Available',
            'startTime' => $uploaderLink && $uploaderLink->start ? Carbon::parse($uploaderLink->start)->format('M d, Y h:i A') : null,
            'endTime' => $uploaderLink && $uploaderLink->end ? Carbon::parse($uploaderLink->end)->format('M d, Y h:i A') : null,
            'isUserValidated' => $isUserValidated,
            'isStorageFull' => $isStorageFull,
            'isLinkExpired' => $isLinkExpired,
        ];

        return view('faceFinder.uploader', ['data' => $data]);
    }

    public function checkPasscode(Request $request)
    {
        $validate = $request->validate([
            'passcode' => 'required|string|max:255',
            'albumId' => 'required|integer',
        ]);

        $passcode = $validate['passcode'];
        $albumId = $validate['albumId'];

        $album = Album::find($albumId);
        $getPasscode = UploaderLink::where('album_id', $album->id)->first();

        if($getPasscode && $getPasscode->passcode === $passcode){
            return redirect()->route('face_finder.uploader.show', [
                'uuid' => $album->event->uuid,
                'albumId' => base64_encode($album->id)
            ])->cookie("uploader_passcode", base64_encode($getPasscode->passcode), 720);
        } else {
            return redirect()->back()->withErrors(['passcode' => 'Invalid passcode. Please try again.']);
        }

    }
}
