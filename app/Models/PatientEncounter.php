<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientEncounter extends Model
{
    protected $fillable = [
        'provider_id',
        'patient_id',
        'facility_id',
        'speciality_id',
        'encounter_type_id',
        'encounter_date',
        'visit_reason',
        'provider',
        'speciality',
        'encounter_type',
        'encounter_status',
    ];
    public function provider()
    {
        return $this->belongsTo(Provider::class, 'provider_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
}
