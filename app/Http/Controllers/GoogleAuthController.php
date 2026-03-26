<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Google\Client as GoogleClient;
use Tymon\JWTAuth\Facades\JWTAuth;

class GoogleAuthController extends Controller
{
    public function googleLogin(Request $request)
    {
        try {
            // Verify Google token
            $client = new GoogleClient(['client_id' => env('GOOGLE_CLIENT_ID')]);
            $payload = $client->verifyIdToken($request->token);

            if (!$payload) {
                return response()->json(['error' => 'Invalid Google token'], 401);
            }

            // Create or update user
            $user = User::updateOrCreate(
                ['email' => $payload['email']],
                [
                    'name' => $payload['name'] ?? 'Unknown',
                    'google_id' => $payload['sub'],
                    'email_verified_at' => now(),
                    'password' => bcrypt(str()->random(16)),
                ]
            );

            // Generate JWT
            $token = JWTAuth::fromUser($user);

            return response()->json([
                'user' => $user,
                'token' => $token
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
