<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modifier extends Model
{
    protected $fillable = ['cpt_code', 'description', 'fees'];


    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'cpt_code', 'cpt_code');
    }
}
