<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InsuranceProvider extends Model
{
    //

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'website_url',
        'logo',
        'status',
        'description',
    ];
}
