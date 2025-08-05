<?php

namespace App\Http\Controllers\Api;

use App\Enum\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Biller;
use App\Models\LoginLog;
use App\Models\OperationAndDirector;
use App\Models\Patient;
use App\Models\Provider;
use App\Models\Scheduler;
use App\Models\TrainingAndHiring;
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

            $log = LoginLog::create([
                'user_id' => $user->id,
                'login_time' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            $user->current_login_log_id = $log->id;
            // Check if last login was over 24 hours ago
            if (!$user->last_login_at || $user->last_login_at->diffInHours(now()) >= 24) {
                $user->daily_login_count = 1;
            } else {
                $user->daily_login_count += 1;
            }

            // Update last login timestamp
            $user->last_login_at = now();
            $user->save();


            $full_name = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
            $token = $user->createToken('auth_token')->plainTextToken;
            $role = UserRole::from($user->user_role); // Assuming Enum

            // Role-specific IDs
            $patient_id = optional(Patient::where('user_id', $user->id)->first())->id;
            $provider_id = optional(Provider::where('user_id', $user->id)->first())->id;
            $scheduler_id = optional(Scheduler::where('user_id', $user->id)->first())->id;
            $biller_id = optional(Biller::where('user_id', $user->id)->first())->id;
            $traning_hiring_id = optional(TrainingAndHiring::where('user_id', $user->id)->first())->id;
            $operational_director_id = optional(OperationAndDirector::where('user_id', $user->id)->first())->id;

            $data = [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'role' => $role,
                'full_name' => $full_name,
                'login_count_today' => $user->daily_login_count,
                'patient_id' => $patient_id,
                'provider_id' => $provider_id,
                'scheduler_id' => $scheduler_id,
                'biller_id' => $biller_id,
                'operational_director_id' => $operational_director_id,
                'training_and_hiring_id' => $traning_hiring_id,
            ];

            return $this->sendResponse($data, 'User login successfully.');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Incorrect email and password']);
        }
    }

    public function logout(Request $request)
    {
        $user = auth()->user();
        $logId = $user->current_login_log_id;

        if ($logId) {

            $log = LoginLog::find($logId);
            if ($log && !$log->logout_time) {
                $log->logout_time = now();
                $log->session_duration = now()->diffInSeconds($log->login_time);
                $log->save();
            }

            // Optional: Clear the log ID from user
            $user->current_login_log_id = null;
            $user->save();
        }

        $request->user()->currentAccessToken()->delete();
        return $this->sendResponse('Successfull Logout');
    }
}
