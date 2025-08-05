<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'patient_id',
        'cpt_code',
        'fees',
        'status',
        'provider_id',
        'trainee_id',
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
        'modifier_1',
        'modifier_2',
        'modifier_3',
        'modifier_4',

    ];
    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function trainee()
    {
        return $this->belongsTo(TrainingAndHiring::class, 'trainee_id', 'id');
    }
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
    public function note()
    {
        return $this->hasOne(Note::class);
    }
    public function modifier()
    {
        return $this->hasMany(Modifier::class);
    }


    public function modifiers()
    {
        return $this->belongsToMany(Modifier::class, 'appointment_modifiers')
            ->withPivot(['fee', 'modifier_1', 'modifier_2', 'modifier_3', 'modifier_4'])
            ->withTimestamps();
    }
}
