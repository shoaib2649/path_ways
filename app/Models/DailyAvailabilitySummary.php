<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyAvailabilitySummary extends Model
{
    //
    protected $fillable = [
        'provider_id',
        'trainer_id',
        'therapy',
        'assessment',
        'therapy_patients',
        'assessment_patients',
    ];
}
