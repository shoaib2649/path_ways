<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EncounterSection extends Model
{
    protected $fillable = [
        'provider_id',
        'patient_id',
        'facility_id',
        'encounter_id',
        'chief_complaint',
        'history',
        'medical_history',
        'surgical_history',
        'family_history',
        'social_history',
        'allergies',
        'medications',
        'review_of_systems',
        'physical_exam',
        'vital_sign',
        'assessments',
        'procedure',
        'follow_up',
        'json_dump',
        'status',
    ];
}
