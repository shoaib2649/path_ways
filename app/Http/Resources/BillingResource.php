<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BillingResource extends JsonResource
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
            'appointment_id' => $this->appointment_id,
            'patient_id' => $this->patient_id,
            'provider_id' => $this->provider_id,
            'meeting_type' => $this->meeting_type,
            'time' => $this->time,
            'amount' => $this->amount,
            'rate' => $this->rate,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            // 'appointment' => $this->whenLoaded('appointment'),
            'appointment' => new AppointmentResource($this->whenLoaded('appointment')),

        ];
    }
}
