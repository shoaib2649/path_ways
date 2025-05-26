<?php

namespace App\Services;

use App\Models\User;
use App\Models\Patient;
use Illuminate\Support\Facades\DB;
use phpseclib3\Crypt\Hash;

class UserService
{
    public function createUser(array $data): User
    {
        
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'prefix' => $data['prefix'] ?? null,
                'mrn' => $data['mrn'] ?? null,
                'first_name' => $data['first_name'],
                'middle_name' => $data['middle_name'] ?? null,
                'last_name' => $data['last_name'] ?? null,
                'full_name' => $data['full_name'] ?? null,
                'user_role' => $data['user_role'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'email' => $data['email'] ?? null,

                'google_id' => $data['google_id'] ?? null,
                'password' => isset($data['password']) ? bcrypt($data['password']) : null,
                'subscribe_status' => $data['subscribe_status'] ?? null,
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'state' => $data['state'] ?? null,
                'postal_code' => $data['postal_code'] ?? null,
                'country' => $data['country'] ?? null,
                'gender' => $data['gender'] ?? null,
                'age' => $data['age'] ?? null,
                'is_active' => $data['is_active'] ?? null,
                'profile_image' => $data['profile_image'] ?? null,
                'bio' => $data['bio'] ?? null,
                'social_media' => $data['social_media'] ?? null,
            ]);

            return $user;
        });
    }
    public function updateUser(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $user->update([
                'prefix' => $data['prefix'] ?? $user->prefix,
                'mrn' => $data['mrn'] ?? $user->mrn,
                'first_name' => $data['first_name'] ?? $user->first_name,
                'middle_name' => $data['middle_name'] ?? $user->middle_name,
                'last_name' => $data['last_name'] ?? $user->last_name,
                'full_name' => $data['full_name'] ?? $user->full_name,
                'email' => $data['email'] ?? $user->email,
                'google_id' => $data['google_id'] ?? $user->google_id,
                'user_role' => $data['user_role'] ?? $user->user_role,
                'subscribe_status' => $data['subscribe_status'] ?? $user->subscribe_status,
                'phone' => $data['phone'] ?? $user->phone,
                'address' => $data['address'] ?? $user->address,
                'state' => $data['state'] ?? $user->state,
                'postal_code' => $data['postal_code'] ?? $user->postal_code,
                'country' => $data['country'] ?? $user->country,
                'date_of_birth' => $data['date_of_birth'] ?? $user->date_of_birth,
                'gender' => $data['gender'] ?? $user->gender,
                'age' => $data['age'] ?? $user->age,
                'is_active' => $data['is_active'] ?? $user->is_active,
                'profile_image' => $data['profile_image'] ?? $user->profile_image,
                'bio' => $data['bio'] ?? $user->bio,
                'social_media' => $data['social_media'] ?? $user->social_media,
            ]);

            return $user;
        });
    }
}
