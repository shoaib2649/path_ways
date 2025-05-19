<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderAvailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id',
        'title',
        'day_of_week',
        'slots',
        'type',
        'location',
        'recurrence',

    ];
    protected $casts = [
        // 'slots' => 'array',
        'day_of_week' => 'array',
    ];


    public function services()
    {
        return $this->belongsToMany(Service::class, 'provider_availability_service', 'provider_availability_id', 'service_id');
    }

    public function slots()
    {
        return $this->hasMany(ProviderAvailabilitySlot::class, 'provider_availability_id');
    }
}
