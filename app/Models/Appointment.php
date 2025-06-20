<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'patient_id',
        'cpt_code',
        'status',
        'provider_id',
        'appointment_date',
        'start_time',
        'end_time',
        'repeat_type',
        'location',
        'type',
        'title',
        'description',
        'color_primary',
        'color_secondary',
        'actions',
        'all_day',
        'resizable_before_start',
        'resizable_after_end',
        'draggable',
        'is_therapy',
        'is_assessment',

    ];
    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }


    public function modifier()
    {
        return $this->belongsTo(Modifier::class, 'cpt_code', 'cpt_code');
    }
}
