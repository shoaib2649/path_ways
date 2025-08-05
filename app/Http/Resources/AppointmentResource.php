<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
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
            'patient_id' => $this->patient_id,
            'first_name' => $this->patient->user->first_name,
            'last_name' => $this->patient->user->family_name,
            'email' => $this->patient->user->email,
            'phone' => $this->patient->user->phone,
            'spruce_link' => $this->patient->spruce_link,
            'patient_full_name' => optional($this->patient->user, fn($user) => $user->first_name . ' ' . $user->family_name),
            'provider_id' => $this->provider_id,
            'trainee_id' => $this->trainee_id,
            'provider_name' => trim(($this->provider->user->first_name ?? '') . ' ' . ($this->provider->user->last_name ?? '')),
            'trainee_name' => trim(($this->trainee->user->first_name ?? '') . ' ' . ($this->trainee->user->last_name ?? '')),
            'appointment_date' => $this->appointment_date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'is_therapy' => $this->is_therapy,
            'is_assessment' => $this->is_assessment,
            'title' => $this->title,
            // 'type' => $this->type,
            'location' => $this->location ?? null,
            'description' => $this->description,
            'color_primary' => $this->color_primary,
            'appointment_status' => $this->status ?? null,
            'insurancePayer' => $this->patient->insurance_payer,
            'groupId' => $this->patient->groupId,
            'memberId' => $this->patient->memberId,
            'invoice_number' => optional($this->note)->invoice ? $this->note->invoice->invoice_number : null,

            // ✅ Display all modifiers attached to this appointment
            'services' => $this->modifiers->map(function ($modifier) {
                return [
                    'id' => $modifier->id,
                    'description' => $modifier->description,
                    'cpt_code'   => $modifier->cpt_code,
                    'fee'        => $modifier->pivot->fee,
                    'modifier_1' => $modifier->pivot->modifier_1 ?? null,
                    'modifier_2' => $modifier->pivot->modifier_2 ?? null,
                    'modifier_3' => $modifier->pivot->modifier_3 ?? null,
                    'modifier_4' => $modifier->pivot->modifier_4 ?? null,

                ];
            }),

        ];
    }
}
