<?php

namespace App\Http\Controllers;

use App\Jobs\PrepareMatchedPhotosZip;
use App\Models\Event;
use App\Models\OtpVerificationAttempt;
use App\Models\Photo;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use ZipArchive;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class publicLinkController extends Controller
{
    public function publicEventPage(string $name, string $uuid)
    {
        $event = Event::query()->where('uuid', $uuid)->first(['name', 'user_id']);
        $eventName = $event->name ?? '';

        return view('faceFinder.public-event', [
            'uuid' => $uuid,
            'eventName' => $eventName
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
                    'event_uuid' => $event->uuid,
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
            'event_uuid' => 'required|string|exists:events,uuid',
        ]);

        $photo = $request->file('photo');

        $eventUuid = $request->input('event_uuid');

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
            $embeddings = [];
            $photoIndexMap = [];

            foreach ($eventPhotos as $key => $eventPhoto) {
                $faces = json_decode($eventPhoto->embedding_json, true);

                if (is_array($faces) && !empty($faces)) {
                    $photoIndexMap[] = $eventPhoto; // same index as embeddings
                    $embeddings[] = $faces;
                }
            }

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
                ->attach('embeddings', json_encode($embeddings), 'embeddings.json')
                ->timeout(120)
                ->post($baseUrl.'/compare-face/');

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
                        if (isset($photoIndexMap[$result['index']])) {
                            $matchPhoto = $photoIndexMap[$result['index']];

                            $matchedPhotos->push([
                                'id' => $matchPhoto->id,
                                'similarity' => (float) $result['similarity'],
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
                    'src' => Storage::disk('s3')->temporaryUrl(
                        $photo->path,
                        now()->addDay(),
                        [
                            'ResponseContentDisposition' => 'attachment; filename="' . basename($photo->filename) . '"',
                        ]
                    ),
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
}
