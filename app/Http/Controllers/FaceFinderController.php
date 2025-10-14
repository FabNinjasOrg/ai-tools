<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use ZipArchive;
use Illuminate\Support\Facades\Http;

class FaceFinderController extends Controller
{
    public function show(string $uuid)
    {
        return view('faceFinder.album', ['uuid' => $uuid]);
    }

    public function index(Request $request)
    {
        $albums = Album::query()
            ->orderByDesc('created_at')
            ->limit(50)
            ->get(['id', 'uuid', 'name', 'zip_size_bytes as size', 'photos_count as count']);

        return response()->json(['albums' => $albums]);
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
        $userId = optional($request->user())->id;
        if (!$userId) {
            $userId = User::query()->value('id');
        }
        if (!$userId) {
            $user = User::create([
                'name' => 'Default User',
                'email' => 'default@example.com',
                'password' => bcrypt('password'),
            ]);
            $userId = $user->id;
        }

        // Store zip privately
        $zipPath = "albums/{$uuid}/{$originalName}";
        Storage::disk('local')->putFileAs("albums/{$uuid}", $zipFile, $originalName);

        // Extract to public disk
        $publicDir = Storage::disk('public')->path("albums/{$uuid}");
        if (! is_dir($publicDir)) {
            mkdir($publicDir, 0775, true);
        }

        $tmpZipPath = Storage::disk('local')->path($zipPath);
        $zip = new ZipArchive();
        if ($zip->open($tmpZipPath) !== true) {
            return response()->json(['message' => 'Failed to open ZIP.'], 422);
        }

        $numExtractedPhotos = 0;
        $allowedExtensions = ['png', 'jpg', 'jpeg', 'webp'];
        for ($entryIndex = 0; $entryIndex < $zip->numFiles; $entryIndex++) {
            $zipEntryStat = $zip->statIndex($entryIndex);
            $zipEntryName = $zipEntryStat['name'] ?? '';

            // Skip directories
            if (str_ends_with($zipEntryName, '/')) continue;

            $zipEntryNameLower = strtolower($zipEntryName);

            // Skip macOS metadata and hidden files
            if (
                str_starts_with($zipEntryNameLower, '__macosx/') ||
                str_contains($zipEntryNameLower, '/._') ||
                str_ends_with($zipEntryNameLower, '.ds_store')
            ) continue;

            $fileExtension = pathinfo($zipEntryNameLower, PATHINFO_EXTENSION);
            if (!in_array($fileExtension, $allowedExtensions, true)) continue;

            $fileContents = $zip->getFromIndex($entryIndex);
            if ($fileContents === false) continue;

            $fileName = basename($zipEntryName);
            $destinationPath = rtrim($publicDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $fileName;
            file_put_contents($destinationPath, $fileContents);
            $numExtractedPhotos++;
        }
        $zip->close();

        // Create album record
        $album = Album::create([
            'user_id' => $userId,
            'uuid' => $uuid,
            'name' => $baseName,
            'zip_filename' => $originalName,
            'zip_path' => $zipPath,
            'zip_size_bytes' => $zipFile->getSize() ?: 0,
            'photos_count' => 0,
        ]);

        // Insert photos
        $publicDisk = Storage::disk('public');
        $files = glob($publicDisk->path("albums/{$uuid}/*"));
        $toInsert = [];
        foreach ($files as $path) {
            if (!is_file($path)) continue;
            $filename = basename($path);

           // Call FastAPI for image embedding
           $imageEmbeddingJson = $this->EmbeddingTheImage($path);

            $toInsert[] = [
                'album_id' => $album->id,
                'filename' => $filename,
                'path' => "albums/{$uuid}/{$filename}",
                'size_bytes' => filesize($path) ?: 0,
                'embedding_json'   => $imageEmbeddingJson ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        if (!empty($toInsert)) {
            Photo::insert($toInsert);
        }

        $album->photos_count = count($toInsert);
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
        $perPage = min(60, max(12, (int) $request->query('per_page', 24)));
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
                    'src' => asset('storage/'.$p->path),
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
        $albumName = Album::query()->where('uuid', $uuid)->value('name') ?? 'Album';

        return view('faceFinder.public-album', ['uuid' => $uuid, 'albumName' => $albumName]);
    }

    public function findPhotos(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg',
            'album_uuid' => 'required|string|exists:albums,uuid',
        ]);

        $photo = $request->file('photo');

        // $photoPath = storage_path('app/test.png');
        // $photo = new \Illuminate\Http\UploadedFile(
        //     $photoPath,
        //     'test.png',
        //     mime_content_type($photoPath) ?: 'image/jpeg',
        //     null,
        //     true
        // );

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
                                'src' => asset('storage/' . $matchPhoto->path),
                                'similarity' => isset($result['similarity']) ? (float) $result['similarity'] : 0.0,
                            ]);
                        }
                    }
                }
            }

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
}
