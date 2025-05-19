<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'name',
        'duration',
        'price',
        'description',
        'category',
        'requires_approval',
        'created_at',
        'updated_at',
    ];

    public function providerAvailabilities()
    {
        return $this->belongsToMany(ProviderAvailability::class, 'provider_availability_service', 'service_id', 'provider_availability_id');
    }
}
