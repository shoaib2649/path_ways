<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NoteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'appointment_id' => $this->appointment_id,
            'patient_id' => $this->patient_id,
            'provider_id' => $this->provider_id,
            'encounter' => $this->encounter,
            'provider_name' => trim(($this->provider->user->first_name ?? '') . ' ' . ($this->provider->user->last_name ?? '')),
            'patient_name'  => trim(($this->patient->user->first_name ?? '') . ' ' . ($this->patient->user->last_name ?? '')),
            'trainee_name'  => trim(($this->supervision->user->first_name ?? '') . ' ' . ($this->supervision->user->last_name ?? '')),

            'patient_email' => $this->patient->user->email,
            'status'       => $this->status,
            'submitted_by'       => $this->submitted_by,


            // 'submitted_by' => $this->submitted_by,
            // 'sign' => $this->sign,
            // 'status' => $this->status,
            'created_at' => $this->created_at,

            // 'services' => $this->appointment->modifiers->map(function ($modifier) {
            //     return [
            //         'id' => $modifier->id,
            //         'description' => $modifier->description,
            //         'cpt_code' => $modifier->cpt_code,
            //         'fee' => $modifier->pivot->fee,
            //         'modifier_1' => $modifier->pivot->modifier_1 ?? null,
            //         'modifier_2' => $modifier->pivot->modifier_2 ?? null,
            //         'modifier_3' => $modifier->pivot->modifier_3 ?? null,
            //         'modifier_4' => $modifier->pivot->modifier_4 ?? null,
            //     ];
            // }),

            'services' => $this->appointment && $this->appointment->modifiers
                ? $this->appointment->modifiers->map(function ($modifier) {
                    return [
                        'id' => $modifier->id,
                        'description' => $modifier->description,
                        'cpt_code' => $modifier->cpt_code,
                        'fee' => $modifier->pivot->fee,
                        'modifier_1' => $modifier->pivot->modifier_1 ?? null,
                        'modifier_2' => $modifier->pivot->modifier_2 ?? null,
                        'modifier_3' => $modifier->pivot->modifier_3 ?? null,
                        'modifier_4' => $modifier->pivot->modifier_4 ?? null,
                    ];
                })
                : [],

        ];
    }
}
