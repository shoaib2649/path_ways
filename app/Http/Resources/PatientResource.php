<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
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
            // 'provider_id' => $this->provider_id,
            // 'mr' => $this->mr,
            // 'suffix' => $this->suffix,
            // 'social_security_number' => $this->social_security_number,
            // 'blood_score' => $this->blood_score,
            // 'lifestyle_score' => $this->lifestyle_score,
            // 'supplement_medication_score' => $this->supplement_medication_score,
            // 'physical_vital_sign_score' => $this->physical_vital_sign_score,
            // 'image' => $this->image,
            // 'module_level' => $this->module_level,
            // 'qualification' => $this->qualification,
            // 'provider_name' => $this->provider_name,
            'referred_by' => $this->referred_by,
            'wait_list' => $this->wait_list,
            'status' => $this->status,
            'location' => $this->location,
            'date_of_birth' => $this->user->date_of_birth,
            'group_appointments' => $this->user->group_appointments,
            'individual_appointments' => $this->user->individual_appointments,
            'location' => $this->user->location,
            'email' => $this->user->email,
            'first_name' => $this->user->first_name ?? null,
            'last_name' => $this->user->last_name ?? null,
            'full_name' => trim(($this->user->first_name ?? '') . ' ' . ($this->user->last_name ?? '')),
            'patient_type' => $this->type,

            'provider_id' => $this->provider->id ?? null,
            'provider_full_name' => trim((optional($this->provider->user)->first_name ?? '') . ' ' . (optional($this->provider->user)->last_name ?? '')),
            // 'user' => new UserResource($this->whenLoaded('user')), // Include user information if loaded
            // 'password' => $this->password,
            // 'middle_name' => $this->middle_name,

            


        ];
    }
}
