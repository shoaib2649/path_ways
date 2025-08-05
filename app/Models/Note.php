<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Note extends Model
{
    //
    protected $fillable = [
        'appointment_id',
        'supervision_id',
        'provider_id',
        'patient_id',
        'encounter',
        'cpt_code',
        'cpt_description',
        'fees',
        'submitted_by',
        'sign',
        'status',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function supervision()
    {
        return $this->belongsTo(TrainingAndHiring::class, 'supervision_id', 'id');
    }
    public function provider()
    {
        return $this->belongsTo(Provider::class, 'provider_id', 'id');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
}
