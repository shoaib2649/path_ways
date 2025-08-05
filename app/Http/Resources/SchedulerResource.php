<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchedulerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->user->first_name ?? null,
            'last_name' => $this->user->last_name ?? null,
            'full_name' => trim(($this->user->first_name ?? '') . ' ' . ($this->user->last_name ?? '')),
            'email' => $this->user->email,
            'colour' => $this->colour,
            // 'specialization' => $this->specialization,
            // 'notes' => $this->notes,
            // 'created_at' => $this->created_at,
        ];
    }
}
