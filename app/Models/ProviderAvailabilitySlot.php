<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderAvailabilitySlot extends Model
{
    //
    protected $fillable = ['provider_availability_id', 'day_of_week', 'start_time', 'end_time', 'date'];

    public function availability()
    {
        return $this->belongsTo(ProviderAvailability::class);
    }
    
}
