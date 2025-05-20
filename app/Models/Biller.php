<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Biller extends Model
{
    //
    protected $fillable = [
        'user_id',
        'admin_id',
        'department',
        'billing_code',
        'is_active',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
