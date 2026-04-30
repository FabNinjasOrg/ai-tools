<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Photo;
use App\Models\Event;
use Illuminate\Bus\Batchable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessDirectPhotoUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    public int $eventId;
    public int $albumId;
    public array $photos;

    public $tries = 3;
    public $timeout = 300;

    /**
     *
     * @param int $eventId
     * @param int $albumId
     * @param array $photos Array of photos with ['filename', 'content' (base64 encoded), 'size']
     */
    public function __construct(int $eventId, int $albumId, array $photos)
    {
        $this->eventId = $eventId;
        $this->albumId = $albumId;
        $this->photos = $photos;
    }

    public function handle(): void
    {
        $event = Event::find($this->eventId);

        if (!$event) {
            Log::error("Event not found for photo upload job", ['event_id' => $this->eventId]);
            return;
        }

        $userId = $event->user_id;
        $uuid = $event->uuid;
        $s3Folder = "FaceFinder/Albums/{$userId}/{$uuid}";

        $toInsert = [];

        foreach ($this->photos as $photoData) {
            try {
                $filename = $photoData['filename'];
                $photoContent = base64_decode($photoData['content']);
                $photoSize = $photoData['size'];

                // Append uniqid to filename
                $uniqueFilename = pathinfo($filename, PATHINFO_FILENAME) . '_' . uniqid() . '.' . pathinfo($filename, PATHINFO_EXTENSION);

                $photoS3Path = "{$s3Folder}/{$uniqueFilename}";

                // Upload photo to S3
                Storage::disk('s3')->put($photoS3Path, $photoContent, 'private');

                // Create temp file for embedding the photo
                $tempPath = storage_path("app/tmp_embedding/{$userId}/{$uuid}/{$uniqueFilename}");
                if (!is_dir(dirname($tempPath))) {
                    mkdir(dirname($tempPath), 0775, true);
                }

                file_put_contents($tempPath, $photoContent);

                // Get face embedding
                $embeddingJson = $this->EmbeddingTheImage($tempPath);

                // Prepare data for bulk insert
                $toInsert[] = [
                    'event_id' => $this->eventId,
                    'album_id' => $this->albumId,
                    'filename' => $uniqueFilename,
                    'path' => $photoS3Path,
                    'size_bytes' => $photoSize,
                    'embedding_json' => $embeddingJson ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Clean up temp file
                @unlink($tempPath);

            } catch (\Throwable $e) {
                Log::error("Failed processing photo in batch: {$photoData['filename']}", [
                    'event_id' => $this->eventId,
                    'error' => $e->getMessage()
                ]);
                // Continue with other photos
                continue;
            }
        }

        // Bulk insert all successfully processed photos
        if (!empty($toInsert)) {
            Photo::insert($toInsert);
            Log::info("Successfully processed photo batch", [
                'event_id' => $this->eventId,
                'count' => count($toInsert)
            ]);
        }
    }

    /**
     * Get face embedding from FastAPI service
     */
    private function EmbeddingTheImage(string $path)
    {
        $baseUrl = env('FASTAPI_BASE_URL', 'http://face-recognition-apis:8005');

        try {
            $response = Http::attach('file', file_get_contents($path), basename($path))
                ->timeout(60)
                ->post($baseUrl . '/image-embedding/');

            if ($response->failed()) {
                Log::warning("Embedding API failed for " . basename($path), [
                    'response' => $response->body()
                ]);
                return null;
            }

            $faces = $response->json('faces');

            return json_encode($faces);

        } catch (\Throwable $e) {
            Log::error("Exception during embedding for " . basename($path), [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}

