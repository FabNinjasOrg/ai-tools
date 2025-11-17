<?php

namespace App\Jobs;

use App\Models\Event;
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

    protected $eventUuid;
    protected $photoIds;
    protected $sessionToken;

    /**
     * Create a new job instance.
     */
    public function __construct(string $eventUuid, array $photoIds, string $sessionToken)
    {
        $this->eventUuid = $eventUuid;
        $this->photoIds = $photoIds;
        $this->sessionToken = $sessionToken;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $event = Event::query()->where('uuid', $this->eventUuid)->firstOrFail(['id','uuid','user_id']);
            logger($event->toArray());
            // Get photos
            $photos = Photo::query()
                ->whereIn('id', $this->photoIds)
                ->where('event_id', $event->id)
                ->get(['id', 'filename', 'path']);

            if ($photos->isEmpty()) {
                Log::error('No photos found for ZIP creation', ['event_uuid' => $this->eventUuid, 'photo_ids' => $this->photoIds]);
                return;
            }

            // Create ZIP file
            $zip = new ZipArchive();
            $zipFileName = "matched-photos-{$this->eventUuid}-" . time() . ".zip";
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
            $s3ZipPath = "FaceFinder/ZIPs/{$event->user_id}/{$this->eventUuid}/{$zipFileName}";
            Storage::disk('s3')->put($s3ZipPath, file_get_contents($zipPath), 'private');

            // Generate temporary URL for S3 file
            $s3TemporaryUrl = Storage::disk('s3')->temporaryUrl($s3ZipPath, now()->addHour());

            // Save S3 URL to OTP attempt table
            $otpAttempt = OtpVerificationAttempt::query()
                ->where('session_token', $this->sessionToken)
                ->where('album_uuid', $this->eventUuid)
                ->first();

            if ($otpAttempt) {
                $otpAttempt->update([
                    'zip_s3_url' => $s3TemporaryUrl
                ]);
            }

            // Clean up local ZIP file
            @unlink($zipPath);

            Log::info('ZIP file created successfully', [
                'event_uuid' => $this->eventUuid,
                'zip_path' => $zipPath,
                'photos_added' => $addedCount,
                'total_photos' => $photos->count()
            ]);

        } catch (\Throwable $e) {
            Log::error('Failed to prepare ZIP file', [
                'event_uuid' => $this->eventUuid,
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
            'event_uuid' => $this->eventUuid,
            'photo_ids' => $this->photoIds,
            'error' => $exception->getMessage()
        ]);
    }
}
