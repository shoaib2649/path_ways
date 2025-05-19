<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EncounterSectionResource extends JsonResource
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
            'encounter_id' => $this->encounter_id,
            'chief_complaint' => $this->chief_complaint,
            'history' => $this->history,
            'medical_history' => $this->medical_history,
            'surgical_history' => $this->surgical_history,
            'family_history' => $this->family_history,
            'social_history' => $this->social_history,
            'allergies' => $this->allergies,
            'medications' => $this->medications,
            'review_of_systems' => $this->review_of_systems,
            'physical_exam' => $this->physical_exam,
            'vital_sign' => $this->vital_sign,
            'assessments' => $this->assessments,
            'procedure' => $this->procedure,
            'follow_up' => $this->follow_up,
            'json_dump' => $this->json_dump,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
