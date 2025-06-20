<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrainingAndHiringResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'title' => $this->title,
            // User
            'first_name' => $this->user->first_name ?? null,
            'last_name' => $this->user->last_name ?? null,
            'full_name' => trim(($this->user->first_name ?? '') . ' ' . ($this->user->last_name ?? '')),
            'email' => $this->user->email ?? null,
            // 'user' => new UserResource($this->whenLoaded('user')), // assuming you want to include user info

        ];
    }
}
