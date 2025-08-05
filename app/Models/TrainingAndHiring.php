<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingAndHiring extends Model
{
    //
    protected $fillable = [

        'admin_id',
        'user_id',
        'title',
        'description',

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
        return $this->belongsTo(User::class);
    }
    public function notes()
    {
        return $this->hasMany(Note::class, 'supervision_id', 'id');
    }
    // public function availability()
    // {
    //     return $this->hasMany(ProviderAvailability::class, 'training_id', 'id');
    // }
    public function availabilities()
    {
        return $this->hasMany(ProviderAvailability::class, 'training_id', 'id');
    }
    public function modifierColors()
    {
        return $this->hasMany(ProviderModifierColor::class);
    }
}
