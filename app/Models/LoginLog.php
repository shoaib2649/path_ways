<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\CarbonInterval;

class LoginLog extends Model
{
    //
    protected $fillable = [
        'user_id',
        'login_time',
        'logout_time',
        'session_duration',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'login_time' => 'datetime',
        'logout_time' => 'datetime',
    ];
    public function getSessionDurationFormattedAttribute()
    {
        if (!$this->session_duration) {
            return null;
        }

        return CarbonInterval::seconds($this->session_duration)->cascade()->forHumans();
    }
}
