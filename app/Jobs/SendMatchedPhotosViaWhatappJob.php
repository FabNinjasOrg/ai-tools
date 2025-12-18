<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Photo;
use Illuminate\Bus\Batchable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class SendMatchedPhotosViaWhatappJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    public string $phoneNumber;
    public array $photoIds;

    public $tries = 3;
    public $timeout = 300;

    /**
     * Create a new job instance.
     */
    public function __construct(string $phoneNumber, array $photoIds)
    {
        $this->phoneNumber = $phoneNumber;
        $this->photoIds = $photoIds;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $photos = Photo::whereIn('id', $this->photoIds)->get();

            foreach ($photos as $photo) {
                $photoUrl = Storage::disk('s3')->temporaryUrl($photo->path, now()->addDay());

                $response = Http::withToken(env('WHATSAPP_TOKEN'))
                    ->post('https://graph.facebook.com/v22.0/' . env('WHATSAPP_PHONE_NUMBER_ID') . '/messages',
                        [
                            'messaging_product' => 'whatsapp',
                            'to' => $this->phoneNumber,
                            'type' => 'document',
                            'document' => [
                                'link' => $photoUrl,
                                'filename' => basename($photo->path),
                            ],
                        ]
                    );

                if ($response->failed()) {
                    Log::error(
                        "Failed to send WhatsApp document to {$this->phoneNumber}",
                        [
                            'photo_id' => $photo->id,
                            'response' => $response->body(),
                        ]
                    );
                }

                // API throtetting delay of 0.5 seconds
                usleep(5000);
            }
        } catch (\Exception $e) {
            Log::error("Error in SendMatchedPhotosViaWhatappJob: " . $e->getMessage());
        }
    }
}
