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
            'last_name' => $this->patient->user->last_name,
            'email' => $this->patient->user->email,
            'patient_full_name' => optional($this->patient->user)->first_name . ' ' . optional($this->patient->user)->last_name,
            'cpt_code' => $this->cpt_code,
            'modifier_description' => $this->description,
            'provider_id' => $this->provider_id,
            'provider_name' => trim(($this->provider->user->first_name ?? '') . ' ' . ($this->provider->user->last_name ?? '')),
            'appointment_date' => $this->appointment_date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'is_therapy' => $this->is_therapy,
            'is_assessment' => $this->is_assessment,
            'title' => $this->title,
            'type' => $this->type,
            'description' => $this->description,
            'color_primary' => $this->color_primary,
            'description_modifier' => $this->modifier->description ?? null,
            'appointment_status' => $this->status ?? null,
            // 'color_secondary' => $this->color_secondary,
            // 'actions' => $this->actions,
            // 'all_day' => $this->all_day,
            // 'resizable_before_start' => $this->resizable_before_start,
            // 'resizable_after_end' => $this->resizable_after_end,
            // 'draggable' => $this->draggable,
            // 'created_at' => $this->created_at,
            // 'updated_at' => $this->updated_at,
            // 'provider' => new ProviderResource($this->whenLoaded('provider')),
            // 'patient' => new ProviderResource($this->whenLoaded('patient')),
        ];
    }
}
