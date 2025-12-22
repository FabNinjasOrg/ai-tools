<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Event;
use App\Models\OtpVerificationAttempt;
use App\Models\Photo;
use App\Models\UploaderLink;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function index(Request $request)
    {
        return view('faceFinder.events-list');
    }

    public function loadEvents(Request $request)
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
            'eventId' => $event ? $event->id : null,
            'attemptTotal' => $attemptTotal,
            'attemptUniquePhones' => $attemptUniquePhones,
            'noMatchCount' => $noMatchCount
        ]);
    }

    public function photos(string $uuid, Request $request)
    {
        $event = Event::query()->where('uuid', $uuid)->firstOrFail(['id','uuid','name','public_url']);

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

    public function bulkDeletePhotos(string $uuid, Request $request)
    {
        $event = Event::query()->where('uuid', $uuid)->where('user_id', auth()->id())->firstOrFail();

        $validated = $request->validate([
            'photo_ids' => 'required|string',
        ]);

        try {
            $photoIds = json_decode($validated['photo_ids'], true);

            if (!is_array($photoIds) || empty($photoIds)) {
                return response()->json(['message' => 'No photos selected for deletion.'], 422);
            }

            $photos = Photo::query()
                ->where('event_id', $event->id)
                ->whereIn('id', $photoIds)
                ->get();

            if ($photos->isEmpty()) {
                return response()->json(['message' => 'No valid photos found to delete.'], 422);
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

            return response()->json(['message' => "Successfully deleted {$deletedCount} photo(s)."]);
        } catch (\Throwable $e) {
            Log::error('Bulk delete photos error', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to delete photos. Please try again.'], 500);
        }
    }

    public function generatePublic(string $uuid)
    {
        $event = Event::query()->where('uuid', $uuid)->firstOrFail();
        // if ($event->public_url) {
        //     return response()->json(['public_url' => $event->public_url]);
        // }

        // Generate URL /event/{name}/{uuid}
        $slugName = Str::slug($event->name);
        $publicUrl = route('face_finder.public.show', ['name' => $slugName, 'uuid' => $event->uuid]);
        $event->public_url = $publicUrl;
        $event->save();

        return response()->json(['public_url' => $publicUrl]);
    }

    public function deleteEvent(string $uuid, Request $request)
    {
        $event = Event::query()
            ->where('uuid', $uuid)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        try {
            $s3 = Storage::disk('s3');
            $s3Folder = "FaceFinder/Albums/{$event->user_id}/{$event->uuid}";

            // Delete all files in the event folder (ZIP + photos)
            if ($s3->exists($s3Folder)) {
                $s3->deleteDirectory($s3Folder);
            }

            DB::transaction(function () use ($event) {
                $albumIds = Album::query()
                    ->where('event_id', $event->id)
                    ->pluck('id');

                if ($albumIds->isNotEmpty()) {
                    UploaderLink::query()->whereIn('album_id', $albumIds)->delete();
                }

                Photo::query()->where('event_id', $event->id)->delete();
                Album::query()->where('event_id', $event->id)->delete();
                OtpVerificationAttempt::query()->where('event_id', $event->id)->delete();

                // Finally delete event (soft delete)
                $event->delete();
            });

            // Redirect back to upload events page
            return redirect()->route('face_finder.upload_photos')->with('status', 'event_deleted');
        } catch (\Throwable $e) {
            Log::error('Failed to delete event', ['uuid' => $uuid, 'error' => $e->getMessage()]);
            return back()->withErrors(['delete' => 'Failed to delete event']);
        }
    }
}
