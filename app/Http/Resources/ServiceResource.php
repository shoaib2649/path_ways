<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // 'id' => $this->id,
            'name' => $this->name,
            // 'duration' => $this->duration,
            // 'price' => $this->price,
            // 'description' => $this->description,
            // 'category' => $this->category,
            // 'requires_approval' => $this->requires_approval,
            // 'created_at' => $this->created_at,
        ];
    }
}
