<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;

class SocialAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                // Create user if not exists
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => bcrypt('google_auth_' . uniqid()), // Random password
                    'role' => 'user' // Default role
                ]);
            } else {
                // Update existing user with Google ID if missing
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'avatar' => $googleUser->getAvatar()
                    ]);
                }
            }

            // Create Token
            $token = $user->createToken('auth_token')->plainTextToken;

            // Redirect to frontend with token
            return redirect('http://localhost:5173/auth/callback?token=' . $token . '&is_admin=' . ($user->role === 'admin' ? 'true' : 'false'));

        } catch (\Exception $e) {
            return redirect('http://localhost:5173/login?error=Google login failed: ' . urlencode($e->getMessage()));
        }
    }
}
