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
            'encounter' => $this->encounter,
            'cpt_code' => $this->cpt_code,
            'cpt_description' => $this->cpt_description,
            'fees' => $this->fees,
            'submitted_by' => $this->submitted_by,
            'sign' => $this->sign,
            'status' => $this->status,
            'created_at' => $this->created_at,
        ];
    }
}
