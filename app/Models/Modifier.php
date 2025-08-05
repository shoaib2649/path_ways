<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modifier extends Model
{
    protected $fillable = ['cpt_code', 'description', 'fees', 'colour'];


    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'cpt_code', 'cpt_code');
    }
    public function modifiers()
    {
        return $this->belongsToMany(Modifier::class, 'appointment_modifier')->withPivot('fee')->withTimestamps();
    }
    public function providerColors()
    {
        return $this->hasMany(ProviderModifierColor::class);
    }
}
