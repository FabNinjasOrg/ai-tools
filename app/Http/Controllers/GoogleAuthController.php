<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\UserSubscription;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
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
                    ]);
                }else{
                    $user = User::create([
                        'name' => $name,
                        'email' => $email,
                        'password' => bcrypt(str()->random(8)),
                        'google_id' => $googleId,
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

            return redirect()->route('face_finder.upload_photos');
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Failed to login with Google: ' . $e->getMessage());
        }
    }
}
