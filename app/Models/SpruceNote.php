<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpruceNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'conversation_id',
        'conversation_item_id',
        'title',
        'subtitle',
        'lastMessageAt',
        'note_text',
        'author_name',
        'attachments',
    ];

    protected $casts = [
        'attachments' => 'array',
        'lastMessageAt' => 'datetime',
    ];
}

