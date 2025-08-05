<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderAvailabilityResource extends JsonResource
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
            'provider_id' => $this->provider_id,
            'training_id' => $this->training_id,
            'provider_name' => $this->provider?->user
                ? $this->provider->user->first_name . ' ' . $this->provider->user->last_name
                : null,
            'trainee_name' => $this->training?->user
                ? $this->training->user->first_name . ' ' . $this->training->user->last_name
                : null,

            'title' => $this->title,
            'type' => $this->type,
            'location' => $this->location,
            'recurrence' => $this->recurrence,
            'slots' => ProviderAvailabilitySlotResource::collection($this->whenLoaded('slots')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
