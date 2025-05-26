<?php

namespace App\Http\Controllers\Api;

use App\Enum\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $full_name = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));

            $token =  $user->createToken('auth_token')->plainTextToken;
            
            $role = UserRole::from($user->user_role); // Convert string to Enum

            // Initialize IDs
            $patient_id = null;
            $provider_id = null;

            // Check if user is a patient
            $patient = Patient::where('user_id', $user->id)->first();
            if ($patient) {
                $patient_id = $patient->id;
            }

            // Check if user is a provider
            $provider = Provider::where('user_id', $user->id)->first();
            if ($provider) {
                $provider_id = $provider->id;
            }

            $data = [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'role' => $role,
                'full_name' => $full_name,
                'patient_id' => $patient_id,
                'provider_id' => $provider_id,
            ];

            return $this->sendResponse($data, 'User login successfully.');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        }
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->sendResponse('Successfull Logout');
    }
}
