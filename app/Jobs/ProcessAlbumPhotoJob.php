<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use ZipArchive;
use Illuminate\Support\Facades\Log;
use App\Models\Photo;
use Illuminate\Bus\Batchable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessAlbumPhotoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    public int $eventId;
    public int $albumId;
    public int $userId;
    public string $uuid;
    public array $photoEntries;
    public string $zipPath;

    public $tries = 3;
    public $timeout = 600;

    /**
     * Create a new job instance.
     */
    public function __construct($eventId, $albumId, $userId, $uuid, array $photoEntries, string $zipPath)
    {
        $this->eventId = $eventId;
        $this->albumId = $albumId;
        $this->userId = $userId;
        $this->uuid = $uuid;
        $this->photoEntries = $photoEntries;
        $this->zipPath = $zipPath;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $s3Folder = "FaceFinder/Albums/{$this->userId}/{$this->uuid}";
        $zip = new ZipArchive();

        if ($zip->open($this->zipPath) !== true) {
            Log::error("Failed to open ZIP for event {$this->eventId}");
            return;
        }

        $toInsert = [];

        foreach ($this->photoEntries as $photoName) {
            try {
                $content = $zip->getFromName($photoName);
                if ($content === false) {
                    Log::warning("Skipping unreadable photo: {$photoName}");
                    continue;
                }

                $filename = basename($photoName);

                // Append uniqid to filename
                $uniqueFilename = pathinfo($filename, PATHINFO_FILENAME) . '_' . uniqid() . '.' . pathinfo($filename, PATHINFO_EXTENSION);

                $photoS3Path = "{$s3Folder}/{$uniqueFilename}";

                Storage::disk('s3')->put($photoS3Path, $content, 'private');

                // Create temp file for embedding the photo
                $tempPath = storage_path("app/tmp_embedding/{$this->userId}/{$this->uuid}/{$uniqueFilename}");
                if (!is_dir(dirname($tempPath))) mkdir(dirname($tempPath), 0775, true);
                file_put_contents($tempPath, $content);

                $embeddingJson = $this->EmbeddingTheImage($tempPath);

                $toInsert[] = [
                    'event_id' => $this->eventId,
                    'album_id' => $this->albumId,
                    'filename' => $uniqueFilename,
                    'path' => $photoS3Path,
                    'size_bytes' => strlen($content),
                    'embedding_json' => $embeddingJson ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                @unlink($tempPath);
            } catch (\Throwable $e) {
                Log::error("Failed processing photo: {$photoName}", [
                    'event_id' => $this->eventId,
                    'error' => $e->getMessage()
                ]);
                // Continue gracefully don’t fail the job
                continue;
            }
        }

        if (!empty($toInsert)) {
            Photo::insert($toInsert);
        }

        $zip->close();
    }

    private function EmbeddingTheImage(string $path) {
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
}
