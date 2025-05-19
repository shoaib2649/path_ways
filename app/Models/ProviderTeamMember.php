<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderTeamMember extends Model
{
    protected $fillable = [
        'provider_id',
        'name',
        'role',
        'email',
        'phone',
        'assigned_at',
        'is_active',
        'notes',
    ];
    
}
