<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderModifierColor extends Model
{
    protected $fillable = ['provider_id', 'modifier_id', 'color'];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
    public function schduler()
    {
        return $this->belongsTo(Scheduler::class);
    }
    public function training_hiring()
    {
        return $this->belongsTo(TrainingAndHiring::class);
    }

    public function modifier()
    {
        return $this->belongsTo(Modifier::class);
    }
}
