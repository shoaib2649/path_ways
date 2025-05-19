<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderException extends Model
{
    protected $fillable = ['provider_id', 'date', 'start_time', 'end_time','day_off'];
    //
}
