<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Photo;
use App\Models\OtpVerificationAttempt;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionPlanPrice;
use App\Jobs\PrepareMatchedPhotosZip;
use App\Jobs\ProcessAlbumPhotoJob;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use ZipArchive;
use Illuminate\Support\Facades\Http;

class FaceFinderController extends Controller
{
    public function faceFinder()
    {
        if (Auth::check()) {
            return redirect()->route('face_finder.upload_album');
        }
        return view('faceFinder.home');
    }

    public function pricing()
    {
        if (Auth::check()) {
            return redirect()->route('face_finder.manage_subscription');
        }

        $currency = 'USD'; // Always USD for guests
        $plans = $this->getPricingPlans($currency);

        return view('faceFinder.pricing', compact('plans', 'currency'));
    }

    public function uploadAlbumPage()
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

    public function show(string $uuid)
    {
        $album = Album::query()->where('uuid', $uuid)->first();
        $attemptTotal = 0;
        $attemptUniquePhones = 0;
        $noMatchCount = 0;

        if ($album) {
            $attemptTotal = $album->otpAttempts()->count();

            $attemptUniquePhones = $album->otpAttempts()
                ->whereNotNull('phone_number')
                ->distinct('phone_number')
                ->count('phone_number');

            $noMatchCount =  $album->otpAttempts()->where('matched_found_photos', 0)->count();
        }

        return view('faceFinder.album', [
            'uuid' => $uuid,
            'attemptTotal' => $attemptTotal,
            'attemptUniquePhones' => $attemptUniquePhones,
            'noMatchCount' => $noMatchCount
        ]);
    }

    public function index(Request $request)
    {
        $albums = Album::query()
            ->where('user_id', auth()->id())
            ->where('upload_status', 'completed')
            ->withCount('photos')
            ->orderByDesc('created_at')
            ->get(['id', 'uuid', 'name', 'zip_size_bytes']);

        // If AJAX request, return JSON
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'albums' => $albums->map(function ($a) {
                    return [
                        'id' => $a->id,
                        'uuid' => $a->uuid,
                        'name' => $a->name,
                        'size' => $a->zip_size_bytes,
                        'count' => $a->photos_count,
                    ];
                })
            ]);
        }

        // Otherwise, return the view
        return view('faceFinder.albums-list');
    }

    public function zipfileUploadStatus(string $uuid)
    {
        $album = Album::query()->where('uuid', $uuid)->first();

        if (!$album) {
            return response()->json(['message' => 'Album not found'], 404);
        }

        return response()->json(['upload_status' => $album->upload_status]);
    }

    public function storeZip(Request $request)
    {
        $request->validate([
            'zip' => 'required|file|mimes:zip|max:1024000', // ~1GB
            'name' => 'nullable|string|max:255',
        ]);

        $zipFile = $request->file('zip');
        $originalName = $zipFile->getClientOriginalName();
        $baseName = $request->input('name') ?: preg_replace('/\.zip$/i', '', $originalName);

        $userId = auth()->id();

        // Prevent a new upload if one is already in progress for this user
        $hasInProgress = Album::query()
            ->where('user_id', $userId)
            ->where('upload_status', 'inprogress')
            ->exists();
        if ($hasInProgress) {
            return response()->json([
                'message' => 'An album upload is already in progress. Please wait until it completes and upload again.'
            ], 422);
        }

        // Check if user is on trial and enforce limits
        $isUserOnTrial = isUserOnTrial();

        if ($isUserOnTrial) {
            // Check single album limit
            $existingAlbum = Album::where('user_id', $userId)->first();
            if ($existingAlbum) {
                return response()->json([
                    'message' => 'Trial users can create only one album. Please delete your existing album or upgrade your subscription.'
                ], 422);
            }
        }

        $uuid = (string) Str::uuid();

        // Extract ZIP temporarily locally
        $localTempDir = storage_path("app/tmp_zip_extract/{$uuid}");
        if (!is_dir($localTempDir)) {
            mkdir($localTempDir, 0775, true);
        }

        $localTempZipPath = $localTempDir . DIRECTORY_SEPARATOR . $originalName;
        file_put_contents($localTempZipPath, file_get_contents($zipFile));

        $zip = new ZipArchive();
        if ($zip->open($localTempZipPath) !== true) {
            return response()->json(['message' => 'Failed to open ZIP.'], 422);
        }

        $allowedExtensions = ['png', 'jpg', 'jpeg', 'webp'];

        // collect photos from the zip file
        $photos = [];

        for ($entryIndex = 0; $entryIndex < $zip->numFiles; $entryIndex++) {
            $zipEntryStat = $zip->statIndex($entryIndex);
            $zipEntryName = $zipEntryStat['name'] ?? '';

            // Skip directories and unwanted files
            if (str_ends_with($zipEntryName, '/')) continue;

            $zipEntryNameLower = strtolower($zipEntryName);
            if (
                str_starts_with($zipEntryNameLower, '__macosx/') ||
                str_contains($zipEntryNameLower, '/._') ||
                str_ends_with($zipEntryNameLower, '.ds_store')
            ) continue;

            $fileExtension = pathinfo($zipEntryNameLower, PATHINFO_EXTENSION);
            if (!in_array($fileExtension, $allowedExtensions, true)) continue;

            $photos[] = $zipEntryName;
        }

        $zip->close();

        // Validate ZIP contains photos
        if (empty($photos)) {
            return response()->json([
                'message' => 'ZIP file contains no valid photos. Please ensure your ZIP contains PNG, JPG, JPEG, or WEBP images only.'
            ], 422);
        }

        // Check trial user photo limit BEFORE uploading
        if ($isUserOnTrial && count($photos) > 10) {
            return response()->json([
                'message' => 'Trial users can upload up to 10 images only. Upgrade your subscription for more.'
            ], 422);
        }

        // Create album record with inprogress status
        $album = Album::create([
            'user_id' => $userId,
            'uuid' => $uuid,
            'name' => $baseName,
            'zip_filename' => $originalName,
            'zip_path' => ' ',
            'zip_size_bytes' => $zipFile->getSize() ?: 0,
            'photos_count' => count($photos),
            'upload_status' => 'inprogress',
        ]);

        // Dispatch job to process photos
        $batchName = "album_{$userId}_{$uuid}";
        $batchJobs = [];

        // Split into chunks, 100 photos per job
        foreach (array_chunk($photos, 100) as $chunk) {
            $batchJobs[] = new ProcessAlbumPhotoJob($album->id, $uuid, $chunk, $localTempZipPath);
        }

        $albumId = $album->id;
        $batch = Bus::batch($batchJobs)
            ->name($batchName)
            ->onQueue('high')
            ->then(function (Batch $batch) use ($albumId) {
                try {
                    $album = Album::find($albumId);
                    if ($album) {
                        $album->upload_status = 'completed';
                        $album->save();
                    }
                } catch (\Throwable $e) {
                    Log::error('Failed to mark album completed', ['album_id' => $albumId, 'error' => $e->getMessage()]);
                }
            })
            ->catch(function (Batch $batch, \Throwable $e) use ($albumId) {
                try {
                    $album = Album::find($albumId);
                    if ($album) {
                        $album->upload_status = 'fail';
                        $album->save();
                    }
                } catch (\Throwable $ex) {
                    Log::error('Failed to mark album failed', ['album_id' => $albumId, 'error' => $ex->getMessage()]);
                }
            })
            ->finally(function () use ($localTempZipPath, $localTempDir) {
                @unlink($localTempZipPath);
                @rmdir($localTempDir);
            })
            ->dispatch();

        return response()->json([
            'album' => [
                'id' => $album->id,
                'uuid' => $album->uuid,
                'name' => $album->name,
                'size' => $album->zip_size_bytes,
                'count' => $album->photos_count,
            ],
            'batch_id' => $batch->id,
        ]);
    }

    public function photos(string $uuid, Request $request)
    {
        $album = Album::query()->where('uuid', $uuid)->firstOrFail(['id','uuid','name','public_url']);

        $perPage = $request->query('per_page', 24);

        $photos = Photo::query()
            ->where('album_id', $album->id)
            ->orderBy('id')
            ->paginate($perPage, ['id','filename','path','size_bytes']);

        return response()->json([
            'album' => [
                'uuid' => $album->uuid,
                'name' => $album->name,
                'public_url' => $album->public_url,
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
        $album = Album::query()->where('uuid', $uuid)->firstOrFail();
        if ($album->public_url) {
            return response()->json(['public_url' => $album->public_url]);
        }

        // Generate URL /album/{name}/{uuid}
        $slugName = Str::slug($album->name);
        $publicUrl = route('face_finder.public.show', ['name' => $slugName, 'uuid' => $album->uuid]);
        $album->public_url = $publicUrl;
        $album->save();

        return response()->json(['public_url' => $publicUrl]);
    }

    public function deleteAlbum(string $uuid, Request $request)
    {
        $album = Album::query()->where('uuid', $uuid)->firstOrFail();

        try {
            $s3 = Storage::disk('s3');
            $s3Folder = "FaceFinder/Albums/{$album->uuid}";

            // Delete all files in the album folder (ZIP + photos)
            if ($s3->exists($s3Folder)) {
                $s3->deleteDirectory($s3Folder);
            }

            // Delete OTP attempts for this album
            // OtpVerificationAttempt::query()->where('album_id', $album->id)->delete();

            // Finally delete album
            $album->delete();

            // Redirect back to upload albums page
            return redirect()->route('face_finder.upload_album')->with('status', 'album_deleted');
        } catch (\Throwable $e) {
            Log::error('Failed to delete album', ['uuid' => $uuid, 'error' => $e->getMessage()]);
            return back()->withErrors(['delete' => 'Failed to delete album']);
        }
    }

    public function EmbeddingTheImage(string $path) {
        $baseUrl = env('FASTAPI_BASE_URL', 'http://fastapi:8005');
        $response = Http::attach('file', file_get_contents($path), basename($path))
            ->timeout(60)
            ->post($baseUrl.'/image-embedding/');

        if ($response->failed()) {
            logger("Embedding API failed for ". basename($path), ['response' => $response->body()]);
            return;
        }

        // API returns faces: array of embeddings (or empty array)
        $faces = $response->json('faces');

        // Convert to JSON for DB storage (store per-photo faces)
        $embeddingJson = json_encode($faces);

        return $embeddingJson;
    }

    public function publicAlbumPage(string $name, string $uuid)
    {
        $album = Album::query()->where('uuid', $uuid)->first(['name', 'user_id']);
        $albumName = $album->name ?? '';

        // Check if album owner is on trial
        $isOwnerOnTrial = false;
        if ($album && $album->user_id) {
            $owner = User::find($album->user_id);
            if ($owner) {
                $isOwnerOnTrial = $owner->subscriptions()
                    ->where('type', 'trial')
                    ->where('status', 'active')
                    ->exists();
            }
        }

        return view('faceFinder.public-album', [
            'uuid' => $uuid,
            'albumName' => $albumName,
            'isOwnerOnTrial' => $isOwnerOnTrial
        ]);
    }

    public function logOtpAttempt(string $uuid, Request $request)
    {
        $album = Album::query()->where('uuid', $uuid)->firstOrFail(['id','uuid']);

        $data = $request->validate([
            'phone_number' => 'nullable|string|max:32',
        ]);

        try {
            $phoneNumber = $data['phone_number'] ?? null;
            $sessionToken = Str::random(40);

            // Check if there's already an entry for this phone number and album
            $existingAttempt = OtpVerificationAttempt::query()
                ->where('album_id', $album->id)
                ->where('phone_number', $phoneNumber)
                ->first();

            if ($existingAttempt) {
                $existingAttempt->increment('attempts');

                $existingAttempt->session_token = $sessionToken;
                $existingAttempt->save();

            } else {
                // Create new attempt
                OtpVerificationAttempt::create([
                    'album_id' => $album->id,
                    'album_uuid' => $album->uuid,
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

        $albumUuid = $request->input('album_uuid');

        // Get album info
        $album = Album::query()->where('uuid', $albumUuid)->firstOrFail();

        try {
            // Get all photos with embeddings from the album
            $albumPhotos = Photo::query()
                ->where('album_id', $album->id)
                ->whereNotNull('embedding_json')
                ->get(['id', 'filename', 'path', 'embedding_json']);

            if ($albumPhotos->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No photos with face data found in this album',
                    'matched_photos' => []
                ]);
            }

            // Prepare embeddings for FastAPI
            $embeddings = $albumPhotos->map(function ($photo) {
                return json_decode($photo->embedding_json, true);
            })->filter()->values()->toArray();

            if (empty($embeddings)) {
                // Update state with no matches
                $this->saveMatchedPhotosData($album, collect());

                return response()->json([
                    'success' => false,
                    'message' => 'No valid face embeddings found in album photos',
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
                $this->saveMatchedPhotosData($album, collect());

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
                        $matchPhoto = $albumPhotos->get($result['index']);
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
            $this->saveMatchedPhotosData($album, $matchedPhotos);

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

    private function saveMatchedPhotosData($album, $matchedPhotos)
    {
        try {
            $sessionToken = request()->cookie('otp_session_token');

            if (!$sessionToken) {
                return; // No session token, skip saving
            }

            // Find the existing attempt for this session and album
            $existingAttempt = OtpVerificationAttempt::query()
                ->where('album_id', $album->id)
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
        $album = Album::query()->where('uuid', $uuid)->firstOrFail(['id','uuid']);

        $sessionToken = $request->cookie('otp_session_token');

        if (!$sessionToken) {
            return response()->json(['verified' => false]);
        }

        $existingAttempt = OtpVerificationAttempt::query()
            ->where('album_id', $album->id)
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
        $album = Album::query()->where('uuid', $uuid)->firstOrFail(['id','uuid']);

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
            ->where('album_id', $album->id)
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
        $album = Album::query()->where('uuid', $uuid)->firstOrFail(['id','uuid']);

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
            ->where('album_id', $album->id)
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

    public function manageSubscription()
    {
        $user = Auth::user();
        $currency = 'USD';

        if ($user && $user->country_code === 'IN') {
            $currency = 'INR';
        }

        $plans = $this->getPricingPlans($currency);

        return view('faceFinder.buy-subscription', compact('plans', 'currency'));
    }
    
    private function getPricingPlans($currency = 'USD')
    {
        $plans = SubscriptionPlan::active()
            ->with(['prices' => function ($query) {
                $query->active()->orderBy('plan_interval');
            }])
            ->orderBy('id')
            ->get();

        return $plans->map(function ($plan) use ($currency) {
            $priceField = $currency === 'INR' ? 'inr_price' : 'usd_price';

            $monthlyPrice = $plan->prices->firstWhere('plan_interval', 'monthly');
            $yearlyPrice = $plan->prices->firstWhere('plan_interval', 'yearly');

            return [
                'id' => $plan->id,
                'name' => $plan->name,
                'storage' => $plan->storage,
                'monthly' => $monthlyPrice ? $monthlyPrice->$priceField : 0,
                'yearly' => $yearlyPrice ? $yearlyPrice->$priceField : 0,
            ];
        })->toArray();
    }

}
