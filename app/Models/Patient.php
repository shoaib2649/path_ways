<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = ['mr','mrn','user_id', 'provider_id', 'suffix','type', 'social_security_number', 'blood_score', 'lifestyle_score', 'supplement_medication_score', 'physical_vital_sign_score','wait_list', 'image', 'module_level', 'qualification', 'provider_name', 'status','location','group_appointments','individual_appointments','referred_by'];

        

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
}
