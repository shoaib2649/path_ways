<?php

namespace App\Http\Controllers\Api;

use App\Enum\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Biller;
use App\Models\Patient;
use App\Models\Provider;
use App\Models\Scheduler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        // dd($request->all());
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
            $scheduler_id = null;
            $biller_id = null;

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
            $scheduler = Scheduler::where('user_id', $user->id)->first();
            if ($scheduler) {
                $scheduler_id =  $scheduler->id;
            }
            $biller = Biller::where('user_id', $user->id)->first();
            if ($biller) {
                $biller_id =  $biller->id;
            }


            $data = [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'role' => $role,
                'full_name' => $full_name,
                'patient_id' => $patient_id,
                'provider_id' => $provider_id,
                'scheduler_id' => $scheduler_id,
                'biller_id' => $biller_id,
                'operational_director_id' => $user->id,
                'training_and_hiring' => $user->id,
            ];

            return $this->sendResponse($data, 'User login successfully.');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Incorrect email and password']);
        }
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->sendResponse('Successfull Logout');
    }
}
