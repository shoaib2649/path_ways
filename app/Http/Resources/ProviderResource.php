<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            // 'user_id' => $this->user_id,
            'specialization' => $this->specialization,
            'license_number' => $this->license_number,
            'license_expiry_date' => $this->license_expiry_date,
            'experience_years' => $this->experience_years,
            'education' => $this->education,
            'certifications' => $this->certifications,
            'clinic_name' => $this->clinic_name,
            'clinic_address' => $this->clinic_address,
            'available_days' => $this->available_days,
            'available_time' => $this->available_time,
            'is_verified' => $this->is_verified,
            'doctor_notes' => $this->doctor_notes,
            'consultation_fee' => $this->consultation_fee,
            'profile_slug' => $this->profile_slug,

            // User
            'first_name' => $this->user->first_name ?? null,
            'last_name' => $this->user->last_name ?? null,
            'full_name' => $this->user->full_name ?? null,
            // 'user' => new UserResource($this->whenLoaded('user')), // assuming you want to include user info
          
        ];
    }
}
