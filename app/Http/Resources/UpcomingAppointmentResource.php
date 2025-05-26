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
            
            'patient_name' =>$this->patient->user->first_name,    
            'appointment_date' =>$this->appointment_date,    
            'start_time' =>$this->start_time,    
            'end_time' =>$this->end_time,    
            'is_therapy' =>$this->is_therapy,    
            'is_assessment' =>$this->is_assessment,    
        ];
    }
}
