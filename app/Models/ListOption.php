<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListOption extends Model
{
    protected $fillable = [
        'list_type',
        'slug',
        'title',
        'sequence',
        'is_default',
        'option_value',
        'mapping',
        'notes',
        'codes',
        'toggle_setting_1',
        'toggle_setting_2',
        'activity',
        'subtype',
        'edit_options',
    ];
}
