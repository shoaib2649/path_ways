<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientEncounterResource extends JsonResource
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
            'provider_id' => $this->provider_id,
            'patient_id' => $this->patient_id,
            'facility_id' => $this->facility_id,
            'speciality_id' => $this->speciality_id,
            'encounter_type_id' => $this->encounter_type_id,
            'encounter_date' => $this->encounter_date,
            'visit_reason' => $this->visit_reason,
            'provider' => $this->provider,
            'speciality' => $this->speciality,
            'encounter_type' => $this->encounter_type,
            'encounter_status' => $this->encounter_status,
            'patient' => new PatientResource($this->whenLoaded('patient')),
            'provider_details' => new ProviderResource($this->whenLoaded('provider')),
        ];
    }
}
