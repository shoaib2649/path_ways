<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpruceNoteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'patient_id' => $this->patient_id,
            'conversation_id' => $this->conversation_id,
            'conversation_item_id' => $this->conversation_item_id,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'lastMessageAt' => $this->lastMessageAt,
            'note_text' => $this->note_text,
            'author_name' => $this->author_name,
            'attachments' => json_decode($this->attachments),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
