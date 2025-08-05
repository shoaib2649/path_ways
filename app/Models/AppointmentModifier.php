<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentModifier extends Model
{
    //

    protected $fillable = [
        'appointment_id',
        'modifier_id',
        'fee',
        'modifier_1',
        'modifier_2',
        'modifier_3',
        'modifier_4'

    ];
}
