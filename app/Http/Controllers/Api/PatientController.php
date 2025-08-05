<?php

namespace App\Http\Controllers\Api;

use App\Enum\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\PatientResource;
use App\Http\Requests\StorePatientRequest;
use App\Models\CareGiver;
use App\Models\Patient;
use App\Models\Provider;
use App\Models\Role;
use App\Models\SpruceNote;
use App\Models\User;
use App\Services\UserService;
use Dotenv\Store\File\Paths;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Traits\SendsSpruceMessages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PatientController extends Controller
{
    use SendsSpruceMessages;
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        $patients = Patient::with('user', 'provider', 'caregivers')
            ->whereNotNull('external_contact_id')
            ->orderBy('id', 'desc')
            ->get();
        foreach ($patients as $patient) {
            $cacheKey = 'spruce_conversations_' . $patient->external_contact_id;
            $conversations = Cache::remember($cacheKey, now()->addMinutes(60), function () use ($patient) {
                return $this->getConversationsLinks($patient->external_contact_id);
            });
            // If found, use them; else fallback to saved spruce_link
            $patient->conversations_link = !empty($conversations) ? $conversations : [$patient->spruce_link];
        }

        Log::info('index page is working', [
            'patient_count' => $patients->count(),
            'timestamp' => now()
        ]);

        return $this->sendResponse(PatientResource::collection($patients), 'Patient Record');
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(StorePatientRequest $request)
    {
        DB::beginTransaction();
        try {
            // Step 1: Create contact in Spruce
            $response = Http::withToken(env('SPRUCE_API_KEY'))->post('https://api.sprucehealth.com/v1/contacts', [
                'middleName' => $request->middleName,
                'gender' => $request->gender,
                'givenName' => $request->givenName,
                'familyName' => $request->familyName,
                'dateOfBirth' => $request->dateOfBirth,
                'emailAddresses' => $request->emailAddresses,
                'phoneNumbers' => $request->phoneNumbers,
            ]);

            if (!$response->successful()) {
                DB::rollBack();
                return $this->sendError('Spruce contact creation failed.', ['spruce_response' => $response->json()]);
            }

            $response_data = $response->json();

            // Step 2: Create User
            $user = User::create([
                'first_name' => $request->givenName,
                'middle_name' => $request->middleName,
                'family_name' => $request->familyName,
                'email' => $request->emailAddresses[0]['value'],
                'phone' => $request->phoneNumbers[0]['value'],
                'date_of_birth' => $request->dateOfBirth,
                'gender' => $request->gender,
                'user_role' => UserRole::Patient,
            ]);

            // $role = Role::where('name', UserRole::Patient)->first();
            // if ($role) {
            //     $user->roles()->attach($role->id);
            // }

            // Step 3: Create Patient
            $patient = Patient::create([
                'user_id' => $user->id,
                'provider_id' => $request->provider_id,
                'partner_family_name' => $request->partnerFamilyName,
                'partner_given_name' => $request->partnerGivenName,
                'external_contact_id' => $response_data['id'] ?? null,
                'spruce_link' => $response_data['appURL'] ?? null,
                'type' => $request->patient_type,
                'genderIdentity' => $request->genderIdentity,
                'referred_by' => $request->referred_by,
                'status' => $request->status,
                'wait_list' => $request->wait_list,
                'group_appointments' => $request->group_billing,
                'individual_appointments' => $request->individual_billing,
                'location' => $request->location,
                'groupId' => $request->groupId,
                'memberId' => $request->memberId,
                'insurance_payer' => $request->insurancePayer,

            ]);

            // Step 4: Create Caregivers
            if ($request->has('caregivers')) {
                foreach ($request->caregivers as $caregiverData) {
                    CareGiver::create([
                        'patient_id' => $patient->id,
                        'first_name' => $caregiverData['firstName'],
                        'last_name' => $caregiverData['lastName'],
                        'email' => $caregiverData['email'],
                        'phone' => $caregiverData['phone'],
                        'date_of_birth' => $caregiverData['dateOfBirth'],
                    ]);
                }
            }

            $sprucePhoneNumber = $request->phoneNumbers[0]['value'] ?? null;
            $messageKey = 'welcome';

            if (empty($sprucePhoneNumber)) {
                return response()->json(['message' => 'Phone number is missing'], 422);
            }

            $this->sendSpruceUpdateSmsMessage($sprucePhoneNumber, $messageKey);
            DB::commit();

            return $this->sendResponse(new PatientResource($patient), 'Patient record created both in local and spruce successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Something went wrong.', ['error' => $e->getMessage()]);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $patient = Patient::where('external_contact_id', $id)->with('user', 'provider', 'caregivers')
            ->whereNotNull('external_contact_id')
            ->orderBy('id', 'desc')->first();

        if (!$patient) {
            return $this->sendError('Patient with this external_contact_id not found.');
        }


        // $response = Http::withToken(env('SPRUCE_API_KEY'))
        //     ->get("https://api.sprucehealth.com/v1/contacts/{$id}");

        // if (!$response->successful()) {
        //     return response()->json(['error' => 'Failed to fetch contact'], $response->status());
        // }

        // $contact = $response->json();

        // $filteredContact = [
        //     'id'          => $contact['id'],
        //     'given_name'  => $contact['givenName'] ?? null,
        //     'middle_name' => $contact['middleName'] ?? null,
        //     'family_name' => $contact['familyName'] ?? null,
        //     'gender'      => $contact['gender'] ?? null,
        //     'email'       => $contact['emailAddresses'][0]['value'] ?? null,
        //     'phone'       => $contact['phoneNumbers'][0]['value'] ?? null,
        // ];

        // return response()->json([
        //     'contact' => $filteredContact,
        // ]);

        // $patient = Patient::with('user')->find($id);
        // if (!empty($patient)) {
        //     return $this->sendResponse(new PatientResource($patient), 'Patient record retrieved successfully');
        // } else {
        //     return $this->sendError('Error occurred while showing the record.');
        // }
        return $this->sendResponse(new PatientResource($patient), 'Patient Record');
    }
    //     public function show(string $id)
    // {
    //     // Step 1: Get local patient with user and guardians
    //     $patient = Patient::with('user', 'guardians')->where('external_contact_id', $id)->first();

    //     if (!$patient) {
    //         return $this->sendError('Patient with this external_contact_id not found.');
    //     }

    //     // Step 2: Get Spruce contact details
    //     $response = Http::withToken(env('SPRUCE_API_KEY'))
    //         ->get("https://api.sprucehealth.com/v1/contacts/{$id}");

    //     if (!$response->successful()) {
    //         return response()->json(['error' => 'Failed to fetch contact'], $response->status());
    //     }

    //     $contact = $response->json();

    //     // Step 3: Filter spruce contact details
    //     $spruceContact = [
    //         'id'          => $contact['id'],
    //         'given_name'  => $contact['givenName'] ?? null,
    //         'middle_name' => $contact['middleName'] ?? null,
    //         'family_name' => $contact['familyName'] ?? null,
    //         'gender'      => $contact['gender'] ?? null,
    //         'email'       => $contact['emailAddresses'][0]['value'] ?? null,
    //         'phone'       => $contact['phoneNumbers'][0]['value'] ?? null,
    //     ];

    //     // Step 4: Format local data (optional: use PatientResource if consistent elsewhere)
    //     $localData = [
    //         'patient_id'   => $patient->id,
    //         'type'         => $patient->type,
    //         'referred_by'  => $patient->referred_by,
    //         'status'       => $patient->status,
    //         'location'     => $patient->location,
    //         'user_role'    => $patient->user->user_role ?? null,
    //         'guardians'    => $patient->guardians->map(function ($guardian) {
    //             return [
    //                 'id'           => $guardian->id,
    //                 'name'         => $guardian->name,
    //                 'phone'        => $guardian->phone,
    //                 'relationship' => $guardian->relationship,
    //             ];
    //         }),
    //     ];

    //     // Step 5: Merge and return
    //     return response()->json([
    //         'contact' => $spruceContact,
    //         'local'   => $localData,
    //     ]);
    // }


    /**
     * Update the specified resource in storage.
     */

    // public function update(StorePatientRequest $request, string $id)
    // {
    //     $contactId = $id; // Use the incoming parameter as the contact ID
    //     // Prepare payload from validated request data
    //     $payload = [
    //         'category' => 'patient',
    //         'companyName' => 'walgreens',
    //         'middleName' => 'test1',
    //         'gender' => 'male', // ✅ must match enum value
    //         'givenName' => 'alian',
    //         'familyName' => 'rose',
    //         'dateOfBirth' => '1990-05-15',

    //         'emailAddresses' => [
    //             ['value' => 'tow@example.com']
    //         ],
    //         'phoneNumbers' => [
    //             ['value' => '+1-212-456-7890']
    //         ],
    //     ];

    //     try {
    //         $response = Http::withToken(env('SPRUCE_API_KEY'))->patch("https://api.sprucehealth.com/v1/contacts/{$contactId}", $payload);
    //         if ($response->successful()) {
    //             return response()->json($response->json(), 200);
    //         } else {
    //             return response()->json([
    //                 'error' => 'Failed to update contact',
    //                 'details' => $response->json()
    //             ], $response->status());
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => 'Exception: ' . $e->getMessage()], 500);
    //     }
    // }
    public function update(StorePatientRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            // Step 1: Update Spruce contact
            $payload = [
                'middleName'      => $request->middleName,
                'gender'          => $request->gender,
                'givenName'       => $request->givenName,
                'familyName'      => $request->familyName,
                'dateOfBirth'     => $request->dateOfBirth,
                'emailAddresses'  => $request->emailAddresses,
                'phoneNumbers'    => $request->phoneNumbers,
            ];

            $response = Http::withToken(env('SPRUCE_API_KEY'))
                ->patch("https://api.sprucehealth.com/v1/contacts/{$id}", $payload);

            if (!$response->successful()) {
                return response()->json([
                    'error' => 'Failed to update contact in Spruce',
                    'details' => $response->json()
                ], $response->status());
            }

            $response_data = $response->json();

            // Step 2: Update local patient

            $patient  = Patient::where('external_contact_id', $id)->first();

            $user = User::find($patient->user_id);
            if (!$patient) {
                return response()->json(['error' => 'Patient not found locally'], 404);
            }
            if (!$user) {
                return response()->json(['error' => 'User not found locally'], 404);
            }
            $patient->update([
                'provider_id'           => $request->provider_id,
                'type'                  => $request->patient_type,
                'partner_family_name'   => $request->partnerFamilyName,
                'partner_given_name'     => $request->partnerGivenName,
                'genderIdentity'        => $request->genderIdentity,
                'referred_by'           => $request->referred_by,
                'status'                => $request->status,
                'wait_list'             => $request->wait_list,
                'group_appointments'    => $request->group_billing,
                'individual_appointments' => $request->individual_billing,
                'location'                 => $request->location,
                'patient_add_from_spruce'  => false,
                'groupId' => $request->groupId,
                'memberId' => $request->memberId,
                'insurance_payer' => $request->insurancePayer,
            ]);


            $user->update([
                'first_name'                        => $request->givenName,
                'middle_name'                       => $request->middleName,
                'family_name'                       => $request->familyName,
                'phone'                             => $request->phoneNumbers[0]['value'] ?? $user->phone,
                'date_of_birth'                     => $request->dateOfBirth,
                'gender'                            => $request->gender,
                'email'                             => $request->emailAddresses[0]['value'],
                'patient_add_from_spruce'           => $request->patient_add_from_spruce ?? false,
            ]);

            if ($request->has('caregivers') && is_array($request->caregivers)) {
                foreach ($request->caregivers as $caregiverData) {
                    if (isset($caregiverData['id'])) {
                        // Try to find existing caregiver for update
                        $caregiver = CareGiver::where('id', $caregiverData['id'])
                            ->first();

                        if ($caregiver) {
                            // Update existing caregiver
                            $caregiver->update([
                                'first_name'    => $caregiverData['firstName'],
                                'last_name'     => $caregiverData['lastName'],
                                'email'         => $caregiverData['email'],
                                'phone'         => $caregiverData['phone'] ?? null,
                                'date_of_birth' => $caregiverData['dateOfBirth'],
                            ]);
                            continue; // Skip to next caregiver
                        }
                    } else {
                        // Create new caregiver if no ID or caregiver not found
                        CareGiver::create([
                            'patient_id'     => $patient->id,
                            'first_name'     => $caregiverData['firstName'],
                            'last_name'      => $caregiverData['lastName'],
                            'email'          => $caregiverData['email'],
                            'phone'          => $caregiverData['phone'] ?? null,
                            'date_of_birth'  => $caregiverData['dateOfBirth'],
                        ]);
                    }
                }
            }


            $sprucePhoneNumber = $request->phoneNumbers[0]['value'] ?? null;
            $messageKey = 'patient_updated';

            if (empty($sprucePhoneNumber)) {
                return response()->json(['message' => 'Phone number is missing'], 422);
            }

            $this->sendSpruceUpdateSmsMessage($sprucePhoneNumber, $messageKey);
            DB::commit();

            return $this->sendResponse(new PatientResource($patient), 'Patient record updated both in local and Spruce successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->sendError('Something went wrong.', ['error' => $e->getMessage()]);
        }
    }

    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            // Step 1: Find the local patient by external_contact_id
            $patient = Patient::where('external_contact_id', $id)->first();
            if (!$patient) {
                return $this->sendError('Patient with this external_contact_id not found.');
            }

            // Step 2: Delete contact from Spruce
            $response = Http::withToken(env('SPRUCE_API_KEY'))
                ->delete("https://api.sprucehealth.com/v1/contacts/{$id}");

            if (!$response->successful()) {
                return response()->json([
                    'error' => 'Failed to delete contact from Spruce',
                    'details' => $response->json(),
                ], $response->status());
            }

            // Delete local patient and associated user
            $user = $patient->user;
            $phone_number = $user->phone;
            $patient->delete();

            if ($user) {
                $user->delete();
            }
            // Delete local patient and associated caregiver
            $patient->caregivers->each->delete();

            $sprucePhoneNumber = $phone_number ?? null;
            $messageKey = 'patient_delete';

            if (empty($sprucePhoneNumber)) {
                return response()->json(['message' => 'Phone number is missing'], 422);
            }

            $this->sendSpruceUpdateSmsMessage($sprucePhoneNumber, $messageKey);
            DB::commit();
            return $this->sendResponse([], 'Patient deleted successfully from both Spruce and local database.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Something went wrong.', ['error' => $e->getMessage()]);
        }
    }

    // public function getPatientConversations(string $patientId)
    // {
    //     $patient = Patient::find($patientId);

    //     if (!$patient || !$patient->external_contact_id) {
    //         return response()->json(['error' => 'Patient or external_contact_id not found'], 404);
    //     }

    //     $response = Http::withToken(env('SPRUCE_API_KEY'))
    //         ->get("https://api.sprucehealth.com/v1/contacts/{$patient->external_contact_id}/conversations");

    //     if (!$response->successful()) {
    //         return response()->json([
    //             'error' => 'Failed to fetch conversations',
    //             'details' => $response->json()
    //         ], $response->status());
    //     }

    //     return response()->json([
    //         'conversations' => $response->json()
    //     ], 200);
    //     // Extract specific fields from each conversation

    //     // $conversations = $response->json()['conversations'] ?? [];
    //     // $filtered = collect($conversations)->map(function ($conv) {
    //     //     return
    //     //         [
    //     //             'message_id'       =>   $conv['id'] ?? null,
    //     //             'title'            =>   $conv['title'] ?? null,
    //     //             'subtitle'         =>   $conv['subtitle'] ?? null,
    //     //             'type'             =>   $conv['type'] ?? null,
    //     //         ];
    //     // });
    // }
    // public function getPatientConversations(string $patientId)
    // {
    //     $patient = Patient::find($patientId);

    //     if (!$patient || !$patient->external_contact_id) {
    //         return response()->json(['error' => 'Patient or external_contact_id not found'], 404);
    //     }

    //     // Fetch conversations
    //     $response = Http::withToken(env('SPRUCE_API_KEY'))
    //         ->get("https://api.sprucehealth.com/v1/contacts/{$patient->external_contact_id}/conversations");

    //     if (!$response->successful()) {
    //         return response()->json([
    //             'error' => 'Failed to fetch conversations',
    //             'details' => $response->json()
    //         ], $response->status());
    //     }

    //     $data = $response->json();
    //     $conversations = $data['conversations'] ?? [];

    //     $appUrls = collect($conversations)->pluck('appURL')->filter()->values();

    //     // If no conversation exists, return the contact's appURL as fallback
    //     if ($appUrls->isEmpty()) {
    //         $contactResponse = Http::withToken(env('SPRUCE_API_KEY'))
    //             ->get("https://api.sprucehealth.com/v1/contacts/{$patient->external_contact_id}");

    //         if ($contactResponse->successful()) {
    //             $contactAppUrl = $contactResponse->json()['appURL'] ?? null;
    //             if ($contactAppUrl) {
    //                 return response()->json([
    //                     'conversations_link' => [$contactAppUrl]
    //                 ], 200);
    //             }
    //         }
    //     }

    //     return response()->json([
    //         'conversations_link' => $appUrls
    //     ], 200);
    // }

    private function getConversationsLinks($externalContactId)
    {
        $response = Http::withToken(env('SPRUCE_API_KEY'))
            ->get("https://api.sprucehealth.com/v1/contacts/{$externalContactId}/conversations");

        if ($response->successful()) {
            $data = $response->json();
            return collect($data['conversations'] ?? [])->pluck('appURL')->filter()->values()->toArray();
        }

        return [];
    }


    public function getConversationMessages(string $conversationId)
    {
        $response = Http::withToken(env('SPRUCE_API_KEY'))
            ->get("https://api.sprucehealth.com/v1/conversations/{$conversationId}/items");

        if (!$response->successful()) {
            return response()->json([
                'error' => 'Failed to fetch messages',
                'details' => $response->json()
            ], $response->status());
        }

        $data = $response->json();
        $items = $data['conversationItems'] ?? [];
        $formatted = collect($items)->map(function ($item) {
            return [
                'author' => $item['author']['displayName'] ?? 'Unknown',
                'text'   => $item['text'] ?? '',
            ];
        });

        return response()->json([
            'messages' => $formatted
        ]);
    }

    public function care_giver_delete($id)
    {
        DB::beginTransaction();

        try {
            $caregiver = CareGiver::find($id);

            if (!$caregiver) {
                return $this->sendError('Caregiver not found.');
            }

            $caregiver->delete();

            DB::commit();

            return $this->sendResponse([], 'Caregiver deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Something went wrong while deleting caregiver.', [
                'error' => $e->getMessage()
            ]);
        }
    }

    public function deleteAllPatients()
    {
        DB::beginTransaction();

        try {
            // Get all patients with their user and caregivers
            $patients = Patient::with(['user', 'caregivers'])->get();

            foreach ($patients as $patient) {
                // Delete all caregivers for this patient
                foreach ($patient->caregivers as $caregiver) {
                    $caregiver->delete();
                }

                // Delete the related user (if exists)
                if ($patient->user) {
                    $patient->user->delete();
                }

                // Delete the patient
                $patient->delete();
            }

            DB::commit();

            return response()->json([
                'message' => 'All patients, their users, and caregivers deleted successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'error' => 'Failed to delete patients, users, and caregivers.',
                'details' => $e->getMessage()
            ], 500);
        }
    }


    public function patient_search(Request $request)
    {
        $query = Patient::with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('first_name')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->first_name . '%');
            });
        }

        $patients = $query->get();

        return $this->sendResponse(PatientResource::collection($patients), 'Search results retrieved successfully.');
    }
}
