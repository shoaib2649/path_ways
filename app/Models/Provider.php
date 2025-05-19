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
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
