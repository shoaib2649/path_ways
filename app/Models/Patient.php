<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = ['mr', 'patient_add_from_spruce', 'genderIdentity', 'mrn', 'user_id', 'spruce_link', 'provider_id', 'external_contact_id', 'suffix', 'type', 'social_security_number', 'blood_score', 'lifestyle_score', 'supplement_medication_score', 'physical_vital_sign_score', 'wait_list', 'image', 'module_level', 'qualification', 'provider_name', 'status', 'location', 'group_appointments', 'individual_appointments', 'referred_by'];
    // In Patient.php model
    protected $casts = [
        'patient_add_from_spruce' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
    public function appointment()
    {
        return $this->hasMany(Appointment::class);
    }

    public function caregivers()
    {
        return $this->hasMany(CareGiver::class);
    }
}
