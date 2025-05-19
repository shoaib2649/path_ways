<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'prefix' => $this->prefix,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'password' => $this->password,
            'phone' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'age' => $this->age,
            'is_active' => $this->is_active,
            'profile_image' => $this->profile_image,
            'bio' => $this->bio,
            'social_media' => $this->social_media,
            'subscribe_status' => $this->subscribe_status,
            'user_role' => $this->user_role,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
