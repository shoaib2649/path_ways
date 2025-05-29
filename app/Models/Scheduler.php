<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Scheduler extends Model
{
    protected $fillable = [

        'specialization',
        'notes',
        'user_id',
        'admin_id'

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
