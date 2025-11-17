<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Event;
use App\Models\Photo;
use App\Models\UploaderLink;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AlbumController extends Controller
{
    public function allAlbums()
    {
        return view('faceFinder.albums');
    }

    public function loadAllAlbums(Request $request)
    {
        $user = Auth::user();

        // Get all events for the user
        $events = Event::query()
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->get(['id','name','uuid']);

        $eventIds = $events->pluck('id');

        // Build albums query
        $albumsQuery = Album::query()
            ->whereIn('event_id', $eventIds)
            ->with('event:id,name,uuid');

        // Apply event filter if provided
        if ($request->has('event_id') && $request->event_id) {
            $albumsQuery->where('event_id', $request->event_id);
        }

        $albums = $albumsQuery->orderByDesc('id')->get(['id','event_id','name','created_at']);

        return response()->json([
            'events' => $events->map(function ($event) {
                return [
                    'id' => $event->id,
                    'name' => $event->name,
                    'uuid' => $event->uuid,
                ];
            }),
            'albums' => $albums->map(function ($album) {
                return [
                    'id' => $album->id,
                    'name' => $album->name,
                    'event_name' => $album->event ? $album->event->name : 'Unknown Event',
                    'event_uuid' => $album->event ? $album->event->uuid : null,
                    'created_at' => $album->created_at,
                ];
            })
        ]);
    }

    public function storeAlbum(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'album_name' => 'required|string|max:255',
        ]);

        $user = Auth::user();

        $event = Event::query()
            ->where('id', $validated['event_id'])
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Create the album
        $album = Album::create([
            'event_id' => $event->id,
            'name' => $validated['album_name'],
        ]);

        return redirect()
            ->route('face_finder.events.show', ['uuid' => $event->uuid])
            ->with('success', 'Album created successfully.');
    }

    public function albumShow(int $id)
    {
        $album = Album::query()->where('id', $id)->firstOrFail(['id','event_id','name','created_at']);
        $event = Event::query()->where('id', $album->event_id)->firstOrFail(['id','uuid','name']);
        $uploaderLink = UploaderLink::query()->where('album_id', $album->id)->latest('id')->first();

        // Convert UTC times to session timezone for display
        $timezone = session('uploader_link_timezone');
        $startAtLocal = null;
        $endAtLocal = null;

        if ($uploaderLink && $timezone) {
            $startAtLocal = $uploaderLink->start
                ? Carbon::parse($uploaderLink->start)->setTimezone($timezone)->format('Y-m-d\TH:i')
                : null;
            $endAtLocal = $uploaderLink->end
                ? Carbon::parse($uploaderLink->end)->setTimezone($timezone)->format('Y-m-d\TH:i')
                : null;
        }

        return view('faceFinder.album', [
            'albumId' => $album->id,
            'albumName' => $album->name,
            'eventUuid' => $event->uuid,
            'eventName' => $event->name,
            'uploaderLink' => $uploaderLink,
            'startAtLocal' => $startAtLocal,
            'endAtLocal' => $endAtLocal,
        ]);
    }

    public function albumPhotos(int $id, Request $request)
    {
        $album = Album::query()->where('id', $id)->firstOrFail(['id','event_id','name', 'uploader_url', 'created_at']);
        $event = Event::query()->where('id', $album->event_id)->firstOrFail(['id','uuid','name','public_url']);

        $perPage = $request->query('per_page', 24);

        $photos = Photo::query()
            ->where('event_id', $event->id)
            ->where('album_id', $album->id)
            ->orderBy('id')
            ->paginate($perPage, ['id','filename','path','size_bytes']);

        return response()->json([
            'album' => [
                'id' => $album->id,
                'name' => $album->name,
                'event_uuid' => $event->uuid,
                'event_name' => $event->name,
                'public_url' => $event->public_url,
                'uploader_url' => $album->uploader_url,
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

    public function deleteAlbum(int $id, Request $request)
    {
        $userId = auth()->id();

        $album = Album::query()
            ->where('id', $id)
            ->firstOrFail();

        $s3 = Storage::disk('s3');

        try {
            Photo::query()
                ->where('album_id', $album->id)
                ->chunkById(100, function ($photos) use ($s3) {
                    foreach ($photos as $photo) {
                        if ($photo->path && $s3->exists($photo->path)) {
                            $s3->delete($photo->path);
                        }
                    }
                });

            DB::transaction(function () use ($album) {
                Photo::query()->where('album_id', $album->id)->delete();
                UploaderLink::query()->where('album_id', $album->id)->delete();
                $album->delete();
            });

            return redirect()
                ->route('face_finder.albums.index')
                ->with('success', 'Album deleted successfully.');
        } catch (\Throwable $e) {
            Log::error('Failed to delete album', [
                'album_id' => $album->id,
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['delete' => 'Failed to delete album. Please try again.']);
        }
    }
}
