<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'address',
        'contact_information',
        'facility_type',
        'facility_capacity',
        'status',
    ];
}
