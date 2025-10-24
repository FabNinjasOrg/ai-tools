<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Photo;
use App\Models\OtpVerificationAttempt;
use App\Models\User;
use App\Jobs\PrepareMatchedPhotosZip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use ZipArchive;
use Illuminate\Support\Facades\Http;

class FaceFinderController extends Controller
{
    public function faceFinder()
    {
        return view('faceFinder.home');
    }

    public function uploadAlbumPage()
    {
        return view('faceFinder.face-finder');
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
            ->withCount('photos')
            ->orderByDesc('created_at')
            ->get(['id', 'uuid', 'name', 'zip_size_bytes']);

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

    public function storeZip(Request $request)
    {
        $request->validate([
            'zip' => 'required|file|mimes:zip|max:1024000', // ~1GB
            'name' => 'nullable|string|max:255',
        ]);

        $zipFile = $request->file('zip');
        $originalName = $zipFile->getClientOriginalName();
        $baseName = $request->input('name') ?: preg_replace('/\.zip$/i', '', $originalName);

        $uuid = (string) Str::uuid();
        $userId = auth()->id();

        $s3Folder = "FaceFinder/Albums/{$uuid}";

        // Store ZIP privately on S3
        // Storage::disk('s3')->putFileAs($s3Folder, $zipFile, $originalName, 'public');
        // $zipPath = "{$s3Folder}/{$originalName}";

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

        $numExtractedPhotos = 0;
        $allowedExtensions = ['png', 'jpg', 'jpeg', 'webp'];
        $toInsert = [];

        // Extract each valid photo and upload to S3
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

            $fileContents = $zip->getFromIndex($entryIndex);
            if ($fileContents === false) continue;

            $filename = basename($zipEntryName);
            $photoS3Path = "{$s3Folder}/{$filename}";

            // Upload to S3 under FaceFinder/Albums/{uuid}/{filename}
            Storage::disk('s3')->put($photoS3Path, $fileContents, 'private');

            // Save temporarily locally for embedding
            $localTempPhoto = $localTempDir . DIRECTORY_SEPARATOR . $filename;
            file_put_contents($localTempPhoto, $fileContents);

            // Call FastAPI for embedding
            $imageEmbeddingJson = $this->EmbeddingTheImage($localTempPhoto);

            // Add to bulk insert
            $toInsert[] = [
                'album_id' => null, // Will update after creating album
                'filename' => $filename,
                'path' => $photoS3Path,
                'size_bytes' => strlen($fileContents),
                'embedding_json' => $imageEmbeddingJson ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $numExtractedPhotos++;

            // Clean up local temp photo
            @unlink($localTempPhoto);
        }

        $zip->close();
        @unlink($localTempZipPath);
        @rmdir($localTempDir);

        // Create album record
        $album = Album::create([
            'user_id' => $userId,
            'uuid' => $uuid,
            'name' => $baseName,
            'zip_filename' => $originalName,
            'zip_path' => ' ',
            'zip_size_bytes' => $zipFile->getSize() ?: 0,
            'photos_count' => $numExtractedPhotos,
        ]);

        // Update album_id in photo data and insert
        foreach ($toInsert as &$photoData) {
            $photoData['album_id'] = $album->id;
        }
        if (!empty($toInsert)) {
            Photo::insert($toInsert);
        }

        // Update album photo count
        $album->photos_count = $numExtractedPhotos;
        $album->save();

        return response()->json([
            'album' => [
                'id' => $album->id,
                'uuid' => $album->uuid,
                'name' => $album->name,
                'size' => $album->zip_size_bytes,
                'count' => $album->photos_count,
            ],
        ]);
    }

    public function photos(string $uuid, Request $request)
    {
        $album = Album::query()->where('uuid', $uuid)->firstOrFail(['id','uuid','name','public_url']);

        $page = max(1, (int) $request->query('page', 1));
        $perPage = min(60, $request->query('per_page', 24));
        $offset = ($page - 1) * $perPage;

        $photos = Photo::query()
            ->where('album_id', $album->id)
            ->orderBy('id')
            ->offset($offset)
            ->limit($perPage)
            ->get(['id','filename','path','size_bytes']);

        $hasMore = Photo::query()->where('album_id', $album->id)->count() > ($offset + $photos->count());

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
            'page' => $page,
            'per_page' => $perPage,
            'has_more' => $hasMore,
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
        $response = Http::attach('file', file_get_contents($path), basename($path))
            ->timeout(60)
            ->post('http://host.docker.internal:8005/image-embedding/');

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
        $albumName = Album::query()->where('uuid', $uuid)->value('name') ?? '';

        return view('faceFinder.public-album', ['uuid' => $uuid, 'albumName' => $albumName]);
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
                return response()->json([
                    'success' => false,
                    'message' => 'No valid face embeddings found in album photos',
                    'matched_photos' => []
                ]);
            }

            // Call FastAPI compare-face endpoint
            $response = Http::attach('file', file_get_contents($photo->getPathname()), $photo->getClientOriginalName())
                ->timeout(60)
                ->post('http://host.docker.internal:8005/compare-face/', [
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
                                'filename' => $matchPhoto->filename,
                                'src' => Storage::disk('s3')->temporaryUrl($matchPhoto->path, now()->addDay()),
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
            $matchedPhotosData = $existingAttempt->matched_photo_id_json ? json_decode($existingAttempt->matched_photo_id_json, true) : [];

            // Get full photo details for matched photos
            $matchedPhotos = [];
            if (!empty($matchedPhotosData)) {
                $photoIds = array_column($matchedPhotosData, 'id');
                $percentageLookup = array_column($matchedPhotosData, 'percentage', 'id');

                if (!empty($photoIds)) {
                    $photos = Photo::query()
                        ->whereIn('id', $photoIds)
                        ->get(['id', 'filename', 'path']);

                    $matchedPhotos = $photos->map(function($photo) use ($percentageLookup) {
                        return [
                            'id' => $photo->id,
                            'filename' => $photo->filename,
                            'src' => Storage::disk('s3')->temporaryUrl($photo->path, now()->addDay()),
                            'similarity' => ($percentageLookup[$photo->id] ?? 0) / 100,
                        ];
                    })->toArray();
                }
            }

            return response()->json([
                'verified' => true,
                'matched_photos' => $matchedPhotos
            ]);
        } else {
            return response()->json(['verified' => false]);
        }
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

}
