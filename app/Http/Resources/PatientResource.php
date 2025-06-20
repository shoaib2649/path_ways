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
            'external_contact_id' => $this->external_contact_id ?? [],
            'spruce_link' => $this->spruce_link ?? [],
            'conversations_link' => $this->conversations_link ?? [],
            'patient_add_from_spruce' => $this->patient_add_from_spruce ?? [],
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
            'referred_by' => $this->referred_by ?? [],
            'wait_list' => $this->wait_list ?? [],
            'status' => $this->status ?? [],
            'location' => $this->location ?? [],
            // Wrapped in optional()
            'date_of_birth' => optional($this->user)->date_of_birth,
            'group_appointments' => $this->group_appointments,
            'individual_appointments' => $this->individual_appointments,
            'location' => optional($this->user)->location,
            'email' => optional($this->user)->email,
            'given_name' => optional($this->user)->first_name,
            'family_name' => optional($this->user)->family_name,
            'user_role' => optional($this->user)->user_role,
            'phone' => optional($this->user)->phone,
            'gender' => optional($this->user)->gender,
            'genderIdentity' => $this->genderIdentity,
            // 'last_name' => $this->user->last_name ?? null,
            // 'full_name' => trim(($this->user->first_name ?? '') . ' ' . ($this->user->last_name ?? '')),
            'patient_type' => $this->type ?? [],

            'provider_id' => optional($this->provider)->id,
            'provider_full_name' => trim(
                (optional(optional($this->provider)->user)->first_name ?? '') . ' ' .
                    (optional(optional($this->provider)->user)->last_name ?? '')
            ),

            // 'user' => new UserResource($this->whenLoaded('user')), // Include user information if loaded
            // 'password' => $this->password,
            // 'middle_name' => $this->middle_name,

            // CareGiver array
            'caregivers' => $this->caregivers->map(function ($caregiver) {
                return [
                    'id'                   =>$caregiver['id'],
                    'first_name'           => $caregiver['first_name'],
                    'last_name'            => $caregiver['last_name'],
                    'email'                => $caregiver['email'],
                    'phone'                => $caregiver['phone'],
                    'date_of_birth'        => $caregiver['date_of_birth'],
                ];
            }),




        ];
    }
}
