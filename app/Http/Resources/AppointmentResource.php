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
            'provider_id' => $this->provider_id,
            'appointment_date' => $this->appointment_date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'is_therapy' => $this->is_therapy,
            'is_assessment' => $this->is_assessment,
            'title' => $this->title,
            'type' => $this->type,
            'description' => $this->description,
            // 'color_primary' => $this->color_primary,
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
