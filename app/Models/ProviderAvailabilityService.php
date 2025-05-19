<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderAvailabilityService extends Model
{
    protected $fillable = [
        'provider_availability_id',
        'service_id',
    ];
}
