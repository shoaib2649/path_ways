<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    //
    protected $fillable = [
        'appointment_id',
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
}
