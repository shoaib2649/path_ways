<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UpcomingAppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [

            'patient_name' => trim(optional(optional($this->patient)->user)->first_name . ' ' . optional(optional($this->patient)->user)->last_name),
            'provider_name' => trim(optional(optional($this->provider)->user)->first_name . ' ' . optional(optional($this->provider)->user)->last_name),
            'appointment_date' => $this->appointment_date,
            'status' => $this->status,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,

            'cpt_code' => $this->modifiers->map(function ($modifier) {
                return $modifier->cpt_code;
            }),

            'location' => $this->location,
            'is_therapy' => $this->is_therapy,
            'title' => $this->title,
            'is_assessment' => $this->is_assessment,
        ];
    }
}
