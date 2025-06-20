<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingAndHiring extends Model
{
    //
    protected $fillable = [

        'admin_id',
        'user_id',
        'title',
        'description',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
