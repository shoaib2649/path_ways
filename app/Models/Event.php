<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    //
    protected $fillable = [
        'provider_id',
        'title',
        'description',
        'start_time',
        'end_time',
        'location',
        'status'
    ];
    public function provider()
    {
        return $this->belongsTo(Provider::class, 'provider_id');
    }
}
