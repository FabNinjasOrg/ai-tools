<?php

namespace App\Http\Controllers;

use App\Models\UserConsent;
use App\Models\OtpVerificationAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserConsentController extends Controller
{
    public static function syncConsentFromCookie($userId)
    {
        try {
            if (!isset($_COOKIE['ff_app_consent'])) {
                return;
            }

            $consentValue = $_COOKIE['ff_app_consent'];

            if (!$consentValue || !in_array($consentValue, ['accept', 'reject'])) {
                return;
            }

            $existingConsent = UserConsent::where('user_id', $userId)->first();

            if ($existingConsent) {
                if ($existingConsent->status !== $consentValue) {
                    $existingConsent->status = $consentValue;
                    $existingConsent->save();
                }
            } else {
                UserConsent::create([
                    'user_id' => $userId,
                    'guest_user_id' => null,
                    'status' => $consentValue,
                ]);
            }
        } catch (\Throwable $e) {
            //
        }
    }

    public function saveUserConsent(Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|in:accept,reject'
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
        }

        $existingConsent = UserConsent::where('user_id', $user->id)->first();

        try {
            if ($existingConsent) {
                if ($existingConsent->status !== $validated['status']) {
                    $existingConsent->status = $validated['status'];
                    $existingConsent->save();
                }
            } else {
                UserConsent::create([
                    'user_id' => $user->id,
                    'guest_user_id' => null,
                    'status' => $validated['status'],
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Consent saved successfully']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Failed to save consent'], 500);
        }
    }

    public function saveGuestConsent(Request $request)
    {
        if (!request()->is('face-finder/public/*')) {
            return response()->json(['success' => false, 'message' => 'This endpoint is only accessible from public pages'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:accept,reject',
        ]);

        if (!$request->cookie('otp_session_token')) {
            return response()->json(['success' => false, 'message' => 'Session token is required'], 422);
        }

        $sessionToken = $request->cookie('otp_session_token');
        $otpAttempt = OtpVerificationAttempt::where('session_token', $sessionToken)->first();

        if (!$otpAttempt) {
            return response()->json(['success' => false, 'message' => 'Invalid session token'], 404);
        }

        $guestUserId = $otpAttempt->id;
        $existingConsent = UserConsent::where('guest_user_id', $guestUserId)->first();

        try {
            if ($existingConsent) {
                if ($existingConsent->status !== $validated['status']) {
                    $existingConsent->status = $validated['status'];
                    $existingConsent->save();
                }
            } else {
                UserConsent::create([
                    'user_id' => null,
                    'guest_user_id' => $guestUserId,
                    'status' => $validated['status'],
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Consent saved successfully']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Failed to save consent'], 500);
        }
    }
}

