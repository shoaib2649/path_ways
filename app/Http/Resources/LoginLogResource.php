<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoginLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'login_time' => $this->login_time?->toDateTimeString(),
            'logout_time' => $this->logout_time?->toDateTimeString(),
            'session_duration' => $this->session_duration,
            'session_duration_formatted' => $this->session_duration_formatted,
            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
        ];
    }
}
