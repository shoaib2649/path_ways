<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Controller;
use Exception;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {

        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::updateOrCreate(
                ['email' => $googleUser->email],
                [
                    'name' => $googleUser->name,
                    'google_id' => $googleUser->id,
                    'password' => bcrypt('default_password'),
                ]
            );

            // Log the user in
            // Auth::login($user);

            // Generate Sanctum token
            $token = $user->createToken('auth_token')->plainTextToken;

            // Prepare response data
            $data = [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'token' => $token,
            ];

            return $this->sendResponse([

                'data' => $data,
                'message' => 'Successfully authenticated with Google',
            ], 200);
        } catch (Exception $e) {
            return $this->sendError(
                'Failed to authenticate with Google',
                ['error' => $e->getMessage()],
                401
            );
        }
    }
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return $this->sendResponse('Successfull Logout');
    }
}
