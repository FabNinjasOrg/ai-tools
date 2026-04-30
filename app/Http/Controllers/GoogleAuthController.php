<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\UserSubscription;
use App\Http\Controllers\UserConsentController;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $googleId = $googleUser->getId();
            $email = $googleUser->getEmail();
            $name = $googleUser->getName();

            $user = User::where('google_id', $googleId)->first();

            if (!$user) {
                $user = User::where('email', $email)->first();

                if($user){
                    $user->update([
                        'google_id' => $googleId,
                        'email_verified_at' => $user->email_verified_at ?? Carbon::now(),
                    ]);
                }else{
                    $user = User::create([
                        'name' => $name,
                        'email' => $email,
                        'password' => bcrypt(str()->random(8)),
                        'google_id' => $googleId,
                        'email_verified_at' => Carbon::now(),
                    ]);

                    // Assign trial subscription to the new user
                    $trialSubscription = UserSubscription::create([
                        'user_id' => $user->id,
                        'type' => 'trial',
                        'start_date' => now(),
                        'status' => 'active',
                    ]);
                }
            }

            // Login the user
            Auth::login($user);

            // Sync consent from cookie to database
            UserConsentController::syncConsentFromCookie($user->id);

            return redirect()->route('face_finder.upload_photos');
        } catch (\Exception $e) {
            return redirect()->route('face_finder')->with('error', 'Failed to login with Google: ' . $e->getMessage());
        }
    }
}
