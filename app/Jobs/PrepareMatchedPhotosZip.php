<?php

namespace App\Jobs;

use App\Models\Album;
use App\Models\Photo;
use App\Models\OtpVerificationAttempt;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class PrepareMatchedPhotosZip implements ShouldQueue
{
    use Queueable;

    public $timeout = 300; // 5 minutes timeout
    public $tries = 3;

    protected $albumUuid;
    protected $photoIds;
    protected $sessionToken;

    /**
     * Create a new job instance.
     */
    public function __construct(string $albumUuid, array $photoIds, string $sessionToken)
    {
        $this->albumUuid = $albumUuid;
        $this->photoIds = $photoIds;
        $this->sessionToken = $sessionToken;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $album = Album::query()->where('uuid', $this->albumUuid)->firstOrFail(['id','uuid','user_id']);
            logger($album->toArray());
            // Get photos
            $photos = Photo::query()
                ->whereIn('id', $this->photoIds)
                ->where('album_id', $album->id)
                ->get(['id', 'filename', 'path']);

            if ($photos->isEmpty()) {
                Log::error('No photos found for ZIP creation', ['album_uuid' => $this->albumUuid, 'photo_ids' => $this->photoIds]);
                return;
            }

            // Create ZIP file
            $zip = new ZipArchive();
            $zipFileName = "matched-photos-{$this->albumUuid}-" . time() . ".zip";
            $zipPath = storage_path("app/temp/{$zipFileName}");

            // Ensure temp directory exists
            if (!is_dir(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }

            if ($zip->open($zipPath, ZipArchive::CREATE) !== TRUE) {
                Log::error('Failed to create ZIP file', ['zip_path' => $zipPath]);
                return;
            }

            // Add photos to ZIP
            $addedCount = 0;
            foreach ($photos as $photo) {
                try {
                    $fileContents = Storage::disk('s3')->get($photo->path);
                    if ($fileContents) {
                        $zip->addFromString($photo->filename, $fileContents);
                        $addedCount++;
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to add photo to ZIP', [
                        'photo_id' => $photo->id,
                        'filename' => $photo->filename,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            $zip->close();

            // Upload ZIP file to S3
            $s3ZipPath = "FaceFinder/ZIPs/{$album->user_id}/{$this->albumUuid}/{$zipFileName}";
            Storage::disk('s3')->put($s3ZipPath, file_get_contents($zipPath), 'private');

            // Generate temporary URL for S3 file
            $s3TemporaryUrl = Storage::disk('s3')->temporaryUrl($s3ZipPath, now()->addHour());

            // Save S3 URL to OTP attempt table
            $otpAttempt = OtpVerificationAttempt::query()
                ->where('session_token', $this->sessionToken)
                ->where('album_uuid', $this->albumUuid)
                ->first();

            if ($otpAttempt) {
                $otpAttempt->update([
                    'zip_s3_url' => $s3TemporaryUrl
                ]);
            }

            // Clean up local ZIP file
            @unlink($zipPath);

            Log::info('ZIP file created successfully', [
                'album_uuid' => $this->albumUuid,
                'zip_path' => $zipPath,
                'photos_added' => $addedCount,
                'total_photos' => $photos->count()
            ]);

        } catch (\Throwable $e) {
            Log::error('Failed to prepare ZIP file', [
                'album_uuid' => $this->albumUuid,
                'photo_ids' => $this->photoIds,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('PrepareMatchedPhotosZip job failed', [
            'album_uuid' => $this->albumUuid,
            'photo_ids' => $this->photoIds,
            'error' => $exception->getMessage()
        ]);
    }
}
