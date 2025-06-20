<?php

namespace App\Http\Controllers;

use App\Enum\UserRole;
use App\Models\Patient;
use Illuminate\Http\Request;
use App\Models\User; // or create a SpruceContact model if needed
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SpruceWebhookController extends Controller
{

    public function handle(Request $request)
    {
        $payload = $request->all();

        Log::info('Received Spruce webhook:', $payload);

        $eventType = $payload['type'] ?? null;
        $contact = $payload['data']['object'] ?? null;

        if (!$eventType || !$contact || !isset($contact['id'])) {
            return response()->json(['message' => 'Invalid webhook payload.'], 400);
        }

        $contactId = $contact['id'];

        switch ($eventType) {
            case 'contact.created':
                $existingPatient = Patient::where('external_contact_id', $contactId)->first();
                if ($existingPatient) {
                    return response()->json(['message' => 'Contact already exists.'], 200);
                }

                $user = User::create([
                    'first_name'    => $contact['givenName'] ?? '',
                    'middle_name'   => $contact['middleName'] ?? '',
                    'family_name'   => $contact['familyName'] ?? '',
                    'email'         => $contact['emailAddresses'][0]['value'] ?? null,
                    'phone'         => $contact['phoneNumbers'][0]['value'] ?? null,
                    'date_of_birth' => $contact['dateOfBirth'] ?? null,
                    'gender'        => $contact['gender'] ?? null,
                    'user_role'     => UserRole::Patient,
                ]);

                $patient = Patient::create([
                    'user_id'                  => $user->id,
                    'external_contact_id'      => $contactId,
                    'spruce_link'              => $contact['appURL'] ?? null,
                    'patient_add_from_spruce'  => true,
                ]);

                Log::info('Patient created from Spruce contact', compact('user', 'patient'));
                return response()->json(['message' => 'Patient created.'], 201);

            case 'contact.updated':
                $patient = Patient::where('external_contact_id', $contactId)->first();
                if (!$patient) {
                    return response()->json(['message' => 'Patient not found for update.'], 404);
                }

                $user = $patient->user;
                $user->update([
                    'first_name'  => $contact['givenName'] ?? $user->first_name,
                    'middle_name' => $contact['middleName'] ?? $user->middle_name,
                    'family_name' => $contact['familyName'] ?? $user->family_name,
                    'email'       => $contact['emailAddresses'][0]['value'] ?? $user->email,
                    'phone'       => $contact['phoneNumbers'][0]['value'] ?? $user->phone,
                    'gender'      => $contact['gender'] ?? $user->gender,
                ]);

                Log::info('Patient updated from Spruce contact', compact('user', 'patient'));
                return response()->json(['message' => 'Patient updated.'], 200);

            case 'contact.deleted':
                $patient = Patient::where('external_contact_id', $contactId)->first();
                if ($patient) {
                    $user = $patient->user;
                    $patient->delete();
                    if ($user) $user->delete();

                    Log::info('Patient and user deleted for contact', compact('contactId'));
                    return response()->json(['message' => 'Patient and user deleted.'], 200);
                }

                return response()->json(['message' => 'No patient found to delete.'], 404);

            default:
                return response()->json(['message' => 'Unhandled event type.'], 200);
        }
    }

    public function register()
    {
        $response = Http::withToken(env('SPRUCE_API_KEY'))->post('https://api.sprucehealth.com/v1/webhooks/endpoints', [
            'url' => 'https://api-pathways.rajistan.com/api/webhook/spruce',
            'name' => 'My Laravel Webhook', // ✅ Required
            'eventTypes' => ['contact.created'],
        ]);
 
        return $response->json();
    }
}
