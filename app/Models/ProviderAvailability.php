<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderAvailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id',
        'training_id',
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

    public function slots()
    {
        return $this->hasMany(ProviderAvailabilitySlot::class, 'provider_availability_id');
    }
    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
    public function training()
    {
        return $this->belongsTo(TrainingAndHiring::class, 'training_id', 'id');
    }
}
