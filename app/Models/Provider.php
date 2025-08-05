<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    //
    protected $fillable = [
        'user_id',
        'specialization',
        'license_number',
        'license_expiry_date',
        'experience_years',
        'education',
        'certifications',
        'clinic_name',
        'clinic_address',
        'available_days',
        'available_time',
        'is_verified',
        'doctor_notes',
        'consultation_fee',
        'profile_slug',
        'colour',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function patient()
    {
        return $this->hasMany(Patient::class);
    }
    public function availabilities()
    {
        return $this->hasMany(ProviderAvailability::class);
    }
    public function notes()
    {
        return $this->hasMany(Note::class, 'provider_id', 'id');
    }
    public function modifierColors()
    {
        return $this->hasMany(ProviderModifierColor::class);
    }
}
