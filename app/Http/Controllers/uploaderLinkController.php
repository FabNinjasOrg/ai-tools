<?php

namespace App\Http\Controllers;

use App\Mail\UploaderLinkShareMail;
use App\Models\Album;
use App\Models\UploaderLink;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class uploaderLinkController extends Controller
{
    public function createUploaderLink(int $id, Request $request)
    {
        $album = Album::query()->where('id', $id)->first(['id','event_id','name']);

        $validated = $request->validate([
            'album_id' => ['required', 'integer', 'in:'.$album->id],
            'event_uuid' => ['required'],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after_or_equal:start_at'],
            'passcode' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
            'timezone' => ['nullable', 'string'],
        ]);

        // Save timezone in session
        if (isset($validated['timezone'])) {
            session(['uploader_link_timezone' => $validated['timezone']]);
        }

        $url = route('face_finder.uploader.show', ['uuid' => $request->event_uuid, 'albumId' => base64_encode($album->id)]);

        // Convert times to UTC using session timezone
        $timezone = $validated['timezone'] ?? session('uploader_link_timezone');
        $startUtc = $timezone
            ? Carbon::parse($validated['start_at'], $timezone)->utc()
            : Carbon::parse($validated['start_at'])->utc();
        $endUtc = $timezone
            ? Carbon::parse($validated['end_at'], $timezone)->utc()
            : Carbon::parse($validated['end_at'])->utc();

        UploaderLink::create([
            'album_id' => $album->id,
            'url' => $url,
            'passcode' => $validated['passcode'],
            'start' => $startUtc,
            'end' => $endUtc,
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('face_finder.albums.show', ['id' => $album->id, 'tab' => 'links'])
            ->with('success', 'Uploader link created successfully.');
    }

    public function updateUploaderLink(int $id, Request $request)
    {
        $album = Album::query()->where('id', $id)->firstOrFail(['id','event_id','name']);
        $link = UploaderLink::query()->where('album_id', $album->id)->latest('id')->firstOrFail();

        $validated = $request->validate([
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after_or_equal:start_at'],
            'passcode' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        // Convert times to UTC using session timezone
        $timezone = session('uploader_link_timezone');
        $startUtc = $timezone
            ? Carbon::parse($validated['start_at'], $timezone)->utc()
            : Carbon::parse($validated['start_at'])->utc();
        $endUtc = $timezone
            ? Carbon::parse($validated['end_at'], $timezone)->utc()
            : Carbon::parse($validated['end_at'])->utc();

        $link->start = $startUtc;
        $link->end = $endUtc;
        $link->passcode = $validated['passcode'];
        $link->status = $validated['status'];
        $link->save();

        return redirect()
            ->route('face_finder.albums.show', ['id' => $album->id, 'tab' => 'links'])
            ->with('success', 'Uploader link updated successfully.');
    }

    public function uploaderPage($uuid, $albumId, Request $request)
    {
        $cookiePasscode = $request->cookie('uploader_passcode') ?? base64_decode($request->cookie('uploader_passcode'));
        $isUserValidated = false;
        $isStorageFull = false;
        $isLinkExpired = false;

        if(isset($cookiePasscode) && !empty($cookiePasscode)){
            $isUserValidated = true;
        }

        $encodedAlbumId = base64_decode($albumId);

        $album = Album::with(['event' => function ($query) use ($uuid) {
            $query->where('uuid',  $uuid);
        }, 'event.user'])->find($encodedAlbumId);

        $uploaderLink = UploaderLink::where('album_id', $album->id)->first();

        if ($isUserValidated && $album && $album->event) {
            if ($uploaderLink) {
                $now = Carbon::now('UTC');
                $startDate = $uploaderLink->start ? Carbon::parse($uploaderLink->start)->utc() : null;
                $endDate = $uploaderLink->end ? Carbon::parse($uploaderLink->end)->utc() : null;

                if (($startDate && $now->lt($startDate)) || ($endDate && $now->gt($endDate))) {
                    $isLinkExpired = true;
                }
            }

            if (!$isLinkExpired && $album->event->user) {
                $isStorageFull = isUserStorageFull($album->event->user);
            }
        }

        // Get timezone from cookie and convert UTC times to user's timezone
        $timezone = $request->cookie('uploader_timezone');
        $startTime = null;
        $endTime = null;

        if ($uploaderLink) {
            if ($uploaderLink->start) {
                $startTime = $timezone
                    ? Carbon::parse($uploaderLink->start)->setTimezone($timezone)->format('M d, Y h:i A')
                    : Carbon::parse($uploaderLink->start)->format('M d, Y h:i A');
            }
            if ($uploaderLink->end) {
                $endTime = $timezone
                    ? Carbon::parse($uploaderLink->end)->setTimezone($timezone)->format('M d, Y h:i A')
                    : Carbon::parse($uploaderLink->end)->format('M d, Y h:i A');
            }
        }

        $data = [
            'albumId' => $album ? $album->id : null,
            'eventUuid' => $album->event ? $album->event->uuid : null,
            'eventName' => $album->event ? $album->event->name : 'Not Available',
            'albumName' => $album ? $album->name : 'Not Available',
            'startTime' => $startTime,
            'endTime' => $endTime,
            'isUserValidated' => $isUserValidated,
            'isStorageFull' => $isStorageFull,
            'isLinkExpired' => $isLinkExpired,
        ];

        return view('faceFinder.uploader', ['data' => $data]);
    }

    public function checkPasscode(Request $request)
    {
        $validate = $request->validate([
            'passcode' => 'required|string|max:255',
            'albumId' => 'required|integer',
            'timezone' => ['nullable', 'string'],
        ]);

        $passcode = $validate['passcode'];
        $albumId = $validate['albumId'];

        $album = Album::find($albumId);
        $getPasscode = UploaderLink::where('album_id', $album->id)->first();

        if($getPasscode && $getPasscode->passcode === $passcode){
            $response = redirect()->route('face_finder.uploader.show', [
                'uuid' => $album->event->uuid,
                'albumId' => base64_encode($album->id)
            ])->cookie("uploader_passcode", base64_encode($getPasscode->passcode), 720);

            // Set timezone cookie if provided
            if (isset($validate['timezone'])) {
                $response->cookie("uploader_timezone", $validate['timezone'], 720);
            }

            return $response;
        } else {
            return redirect()->back()->withErrors(['passcode' => 'Invalid passcode. Please try again.']);
        }

    }

    public function shareUploaderLink(Request $request)
    {
        $validated = $request->validate([
            'emails' => ['required', 'array', 'min:1'],
            'emails.*' => ['required', 'email'],
            'album_id' => ['required', 'integer', 'exists:albums,id'],
        ]);

        $album = Album::findOrFail($validated['album_id']);
        $uploaderLink = UploaderLink::where('album_id', $album->id)->latest('id')->firstOrFail();

        foreach ($validated['emails'] as $email) {
            $mail = new UploaderLinkShareMail($album->name, $uploaderLink->url, $uploaderLink->passcode);
            $mail->onQueue('high');
            Mail::to($email)->queue($mail);
        }

        return redirect()
            ->route('face_finder.albums.show', ['id' => $album->id, 'tab' => 'links'])
            ->with('success', 'Uploader link shared successfully.');
    }
}
