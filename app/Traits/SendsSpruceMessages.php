<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

trait SendsSpruceMessages
{
    public function sendSpruceUpdateSmsMessage(string $phoneNumber, ?string $templateKey = null, array $placeholders = []): void

    {
        if (!$phoneNumber || !$templateKey) {
            Log::warning('SMS not sent. Missing phone number or message key.');
            return;
        }

        $message = getMessageTemplate($templateKey, $placeholders);
        // dd($message);

        $smsPayload = [
            'destination' => [
                'smsOrEmailEndpoint' => $phoneNumber
            ],
            'message' => [
                'body' => [
                    [
                        'type' => 'text',
                        'value' => $message,
                    ]
                ],
                'internal' => true
            ]
        ];

        $spruceEndpointId = env('SPRUCE_SMS_ENDPOINT_ID');
        $url = "https://api.sprucehealth.com/v1/internalendpoints/{$spruceEndpointId}/conversations";

        $response = Http::withToken(env('SPRUCE_API_KEY'))
            ->acceptJson()
            ->post($url, $smsPayload);

        if ($response->successful()) {
            Log::info('Spruce SMS sent successfully to ' . $phoneNumber);
            Log::info('Spruce message sent ' . $message);
        } else {
            Log::error('Spruce SMS failed', [
                'phone' => $phoneNumber,
                'response' => $response->json(),
            ]);
        }
    }
}
