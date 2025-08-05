<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrainingAndHiringResource extends JsonResource
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
            'colour' => $this->colour,
            'title' => $this->title,
            'description' => $this->description,

            // User
            'first_name' => $this->user->first_name ?? null,
            'last_name' => $this->user->last_name ?? null,
            'full_name' => isset($this->user->first_name, $this->user->last_name)
                ? "{$this->user->first_name} {$this->user->last_name}"
                : ($this->user->first_name ?? $this->user->last_name ?? null),
            'email' => $this->user->email ?? null,

            // Slots (if availability relationship is set)
            'slots' => $this->availabilities->pluck('slots')->flatten()->map(function ($slot) {
                return [
                    'id' => $slot->id,
                    'start_time' => $slot->start_time,
                    'end_time' => $slot->end_time,
                    'day_of_week' => $slot->day_of_week ?? null,
                ];
            }),
        ];
    }
}
