<?php

namespace App\Http\Controllers\Api;

use App\Enum\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Http\Resources\AppointmentResource;
use App\Models\Patient;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\UserService;
use App\Http\Requests\StorePatientRequest;
use App\Http\Resources\ProviderAvailabilityResource;
use App\Http\Resources\ProviderAvailabilitySlotResource;
use App\Http\Resources\UpcomingAppointmentResource;
use App\Models\AppointmentModifier;
use App\Models\Provider;
use App\Models\ProviderAvailability;
use App\Models\ProviderAvailabilitySlot;
use App\Models\TrainingAndHiring;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use App\Traits\SendsSpruceMessages;

class AppointmentController extends Controller
{
    use SendsSpruceMessages;
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            // dd($user)
            $providerId = Provider::where('user_id', $user->id)->value('id');
            $trainingId = TrainingAndHiring::where('user_id', $user->id)->value('id');
            if ($providerId) {

                $appointments = Appointment::where('provider_id', $providerId)
                    ->with(['patient.user', 'provider', 'modifiers'])
                    ->orderByRaw('DATE(start_time) ASC')   // Sort by date
                    ->orderBy('start_time', 'asc')         // Then by time of day
                    ->get();
            } elseif ($trainingId) {
                $appointments = Appointment::where('trainee_id', $trainingId)
                    ->with(['patient.user', 'provider', 'modifiers'])
                    ->orderByRaw('DATE(start_time) ASC')   // Sort by date
                    ->orderBy('start_time', 'asc')         // Then by time of day
                    ->get();
            } else {
                $appointments = Appointment::with(['patient.user', 'provider', 'modifiers'])
                    ->orderByRaw('DATE(start_time) ASC')   // Sort by date
                    ->orderBy('start_time', 'asc')         // Then by time of day
                    ->get();
            }
            return $this->sendResponse(
                AppointmentResource::collection($appointments),
                'Appointments retrieved successfully.'
            );
        } catch (Exception $e) {
            return $this->sendError('Failed to retrieve appointments.', ['error' => $e->getMessage()]);
        }
    }

    //    old code
    // public function store(Request $request)
    // {
    //     DB::beginTransaction();
    //     try {
    //         // $overlap = Appointment::where('provider_id', $request->provider_id)
    //         //     ->whereDate('appointment_date', $request->appointment_date)
    //         //     ->where(function ($query) use ($request) {
    //         //         $query->whereBetween('start_time', [$request->start_time, $request->end_time])
    //         //             ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
    //         //             ->orWhere(function ($q) use ($request) {
    //         //                 $q->where('start_time', '<=', $request->start_time)
    //         //                     ->where('end_time', '>=', $request->end_time);
    //         //             });
    //         //     })->exists();
    //         $overlap = Appointment::where('provider_id', $request->provider_id)
    //             ->whereDate('appointment_date', $request->appointment_date)
    //             ->where(function ($query) use ($request) {
    //                 $query->where(function ($q) use ($request) {
    //                     $q->where('start_time', '<', $request->end_time)
    //                         ->where('end_time', '>', $request->start_time);
    //                 });
    //             })->exists();



    //         if ($overlap) {
    //             return $this->sendError(['message' => 'This appointment time has already been booked.'], 409);
    //         }

    //         // Unique color for each provider
    //         $proivder_id = Appointment::where('provider_id', $request->provider_id)->first();
    //         // Step 3: Create appointment with resolved patient ID
    //         $appointment = Appointment::create([
    //             'patient_id' => $request->patient_id,
    //             // 'cpt_code'  => $request->cpt_code,
    //             // 'fees'  => $request->fees,
    //             'provider_id' => $request->provider_id,
    //             'start_time' => $request->start_time,
    //             'end_time' => $request->end_time,
    //             'is_therapy' => $request->is_therapy,
    //             'is_assessment' => $request->is_assessment,
    //             'appointment_date' => $request->appointment_date,
    //             'title' => $request->title,
    //             'type' => $request->type,
    //             'location' => $request->location,
    //             'repeat_type' => $request->repeat_type,
    //             'description' => $request->description,
    //             'color_primary' => $proivder_id->color_primary ?? '#C5ECFD',
    //             'color_secondary' => $request->color_secondary,
    //             'actions' => $request->actions,
    //             'all_day' => $request->all_day ?? false,
    //             'resizable_before_start' => $request->resizable_before_start ?? false,
    //             'resizable_after_end' => $request->resizable_after_end ?? false,
    //             'draggable' => $request->draggable ?? false,
    //             'status' => $request->appointment_status ?? 'pending',

    //         ]);

    //         if ($request->has('modifiers')) {
    //             $modifierSyncData = [];

    //             foreach ($request->modifiers as $modifier) {
    //                 $modifierSyncData[$modifier['id']] = [
    //                     'fee'        => $modifier['fee'] ?? null,
    //                     'modifier_1' => $modifier['modifier_1'] ?? null,
    //                     'modifier_2' => $modifier['modifier_2'] ?? null,
    //                     'modifier_3' => $modifier['modifier_3'] ?? null,
    //                     'modifier_4' => $modifier['modifier_4'] ?? null,
    //                 ];
    //             }

    //             $appointment->modifiers()->sync($modifierSyncData);
    //         }


    //         $sprucePhoneNumber = $appointment->patient->user->phone ?? null;
    //         $messageKey = 'appointment_created';

    //         $placeholders = [
    //             'start_time' => Carbon::parse($appointment->start_time)->format('Y-m-d H:i'),
    //             'end_time'   => Carbon::parse($appointment->end_time)->format('H:i'),
    //         ];


    //         if (empty($sprucePhoneNumber)) {
    //             return response()->json(['message' => 'Phone number is missing'], 422);
    //         }

    //         $this->sendSpruceUpdateSmsMessage($sprucePhoneNumber, $messageKey, $placeholders);


    //         DB::commit();
    //         return $this->sendResponse(new AppointmentResource($appointment), 'Appointment created successfully!');
    //     } catch (Exception $e) {
    //         // Step 5: Roll back if any error occurs
    //         DB::rollBack();
    //         Log::error('Appointment creation failed: ' . $e->getMessage());

    //         return $this->sendError('Failed to create appointment.', ['error' => $e->getMessage()]);
    //     }
    // }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $userType = $request->has('provider_id') ? 'provider' : 'trainee';
            $userId = $userType === 'provider' ? $request->provider_id : $request->trainee_id;

            // Check for overlapping appointments
            $overlap = Appointment::where("{$userType}_id", $userId)
                ->whereDate('appointment_date', $request->appointment_date)
                ->where(function ($query) use ($request) {
                    $query->where(function ($q) use ($request) {
                        $q->where('start_time', '<', $request->end_time)
                            ->where('end_time', '>', $request->start_time);
                    });
                })->exists();

            if ($overlap) {
                return $this->sendError(['message' => 'This appointment time has already been booked.'], 409);
            }

            $colorSource = $userType === 'provider'
                ? Appointment::where('provider_id', $request->provider_id)->first()
                : Appointment::where('trainee_id', $request->trainee_id)->first();

            $appointment = Appointment::create([
                'patient_id' => $request->patient_id,
                'provider_id' => $request->provider_id,
                'trainee_id' => $request->trainee_id,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'is_therapy' => $request->is_therapy,
                'is_assessment' => $request->is_assessment,
                'appointment_date' => $request->appointment_date,
                'title' => $request->title,
                'type' => $request->type,
                'location' => $request->location,
                'repeat_type' => $request->repeat_type,
                'description' => $request->description,
                'color_primary' => $colorSource->color_primary ?? '#C5ECFD',
                'color_secondary' => $request->color_secondary,
                'actions' => $request->actions,
                'all_day' => $request->all_day ?? false,
                'resizable_before_start' => $request->resizable_before_start ?? false,
                'resizable_after_end' => $request->resizable_after_end ?? false,
                'draggable' => $request->draggable ?? false,
                'status' => $request->appointment_status ?? 'pending',
            ]);

            // Sync modifiers if provided
            if ($request->has('modifiers')) {
                $modifierSyncData = [];
                foreach ($request->modifiers as $modifier) {
                    $modifierSyncData[$modifier['id']] = [
                        'fee'        => $modifier['fee'] ?? null,
                        'modifier_1' => $modifier['modifier_1'] ?? null,
                        'modifier_2' => $modifier['modifier_2'] ?? null,
                        'modifier_3' => $modifier['modifier_3'] ?? null,
                        'modifier_4' => $modifier['modifier_4'] ?? null,
                    ];
                }
                $appointment->modifiers()->sync($modifierSyncData);
            }

            // Send SMS via Spruce
            $sprucePhoneNumber = $appointment->patient->user->phone ?? null;
            $messageKey = 'appointment_created';
            $placeholders = [
                'start_time' => Carbon::parse($appointment->start_time)->format('Y-m-d H:i'),
                'end_time'   => Carbon::parse($appointment->end_time)->format('H:i'),
            ];

            if (empty($sprucePhoneNumber)) {
                return response()->json(['message' => 'Phone number is missing'], 422);
            }

            $this->sendSpruceUpdateSmsMessage($sprucePhoneNumber, $messageKey, $placeholders);

            DB::commit();
            return $this->sendResponse(new AppointmentResource($appointment), 'Appointment created successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Appointment creation failed: ' . $e->getMessage());
            return $this->sendError('Failed to create appointment.', ['error' => $e->getMessage()]);
        }
    }



    public function show($id)
    {
        try {
            $appointment = Appointment::findOrFail($id);
            return $this->sendResponse(new AppointmentResource($appointment), 'Appointment retrieved successfully!');
        } catch (Exception $e) {
            return $this->sendError('Appointment not found.', ['error' => $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $appointment = Appointment::findOrFail($id);

            $appointment->update([
                'cpt_code'  => $request->cpt_code,
                'fees'  => $request->fees,
                'start_time' => $request->start_time,
                'status' => $request->appointment_status ?? 'pending',
                'end_time' => $request->end_time,
                'appointment_date' => $request->appointment_date,
                'title' => $request->title,
                'description' => $request->description,
                'color_primary' => $request->color_primary,
                'color_secondary' => $request->color_secondary,
                'actions' => $request->actions,
                'location' => $request->location,
                'all_day' => $request->all_day ?? false,
                'resizable_before_start' => $request->resizable_before_start ?? false,
                'resizable_after_end' => $request->resizable_after_end ?? false,
                'draggable' => $request->draggable ?? false,
            ]);
            Appointment::where('provider_id', $appointment->provider_id)
                ->update([
                    'color_primary' => $request->color_primary,
                ]);


            if ($request->has('modifiers')) {
                $modifierSyncData = [];

                foreach ($request->modifiers as $modifier) {
                    $modifierSyncData[$modifier['id']] = [
                        'fee'        => $modifier['fee'] ?? null,
                        'modifier_1' => $modifier['modifier_1'] ?? null,
                        'modifier_2' => $modifier['modifier_2'] ?? null,
                        'modifier_3' => $modifier['modifier_3'] ?? null,
                        'modifier_4' => $modifier['modifier_4'] ?? null,
                    ];
                }

                $appointment->modifiers()->sync($modifierSyncData);
            }
            $sprucePhoneNumber = $appointment->patient->user->phone ?? null;
            $messageKey = 'appointment_updated';

            $placeholders = [
                'start_time' => Carbon::parse($appointment->start_time)->format('Y-m-d H:i'),
                'end_time'   => Carbon::parse($appointment->end_time)->format('H:i'),
            ];

            if (empty($sprucePhoneNumber)) {
                return response()->json(['message' => 'Phone number is missing'], 422);
            }

            $this->sendSpruceUpdateSmsMessage($sprucePhoneNumber, $messageKey, $placeholders);

            return $this->sendResponse(new AppointmentResource($appointment), 'Appointment updated successfully!');
        } catch (Exception $e) {
            return $this->sendError('Failed to update appointment.', ['error' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $appointment = Appointment::findOrFail($id);
            // $appointment->delete();
            if ($appointment) {
                $appointment->update(['status' => 'cancel']);
            }
            $sprucePhoneNumber = $appointment->patient->user->phone ?? null;
            $messageKey = 'appointment_deleted';

            $placeholders = [
                'start_time' => Carbon::parse($appointment->start_time)->format('Y-m-d H:i'),
                'end_time'   => Carbon::parse($appointment->end_time)->format('H:i'),
            ];

            if (empty($sprucePhoneNumber)) {
                return response()->json(['message' => 'Phone number is missing'], 422);
            }

            $this->sendSpruceUpdateSmsMessage($sprucePhoneNumber, $messageKey, $placeholders);

            return $this->sendResponse([], 'Appointment cancel successfully!');
        } catch (Exception $e) {
            return $this->sendError('Failed to delete appointment.', ['error' => $e->getMessage()]);
        }
    }

    public function modifer_delete($id)
    {
        try {

            $appointment = AppointmentModifier::where('modifier_id', $id)->first();
            $appointment->delete();
            return $this->sendResponse([], 'AppointmentModifier  successfully deleted!');
        } catch (Exception $e) {
            return $this->sendError('Failed to delete appointment.', ['error' => $e->getMessage()]);
        }
    }
    public function upcoming_appointments()
    {
        try {
            $user = Auth::user();
            $providerId = Provider::where('user_id', $user->id)->value('id');
            $today = Carbon::now()->toDateString();

            // Base query
            $appointmentsQuery = Appointment::with(['provider', 'patient', 'modifiers'])
                ->whereDate('appointment_date', '>=', $today)
                ->orderBy('appointment_date', 'asc');

            // Filter by provider if applicable
            if ($providerId) {
                $appointmentsQuery->where('provider_id', $providerId);
            }

            // Get results
            $appointments = $appointmentsQuery->get();

            return $this->sendResponse(
                UpcomingAppointmentResource::collection($appointments),
                'Upcoming availability slots retrieved successfully.'
            );
        } catch (Exception $e) {
            return $this->sendError('Failed to retrieve upcoming availability slots.', ['error' => $e->getMessage()]);
        }
    }
    // old code 
    // public function patient_appointment_details($id)
    // {
    //     // dd($id);


    //     // return response()->json([
    //     //     'success' => true,
    //     //     'data' => $appointments
    //     // ]);
    //     try {
    //         $appointments = Appointment::with('patient.user')->where('patient_id', $id)->get();
    //         return $this->sendResponse(
    //             AppointmentResource::collection($appointments),
    //             'Appointments retrieved successfully.'
    //         );
    //     } catch (Exception $e) {
    //         return $this->sendError('Appointment not found.', ['error' => $e->getMessage()]);
    //     }
    // }

    public function patient_appointment_details($id)
    {
        try {
            // Get appointments with patient and user relationship
            $appointments = Appointment::with('patient.user')
                ->where('patient_id', $id)
                ->get();

            // Case 1: Appointments found
            if ($appointments->isNotEmpty()) {
                return $this->sendResponse(
                    AppointmentResource::collection($appointments),
                    'Appointments retrieved successfully.'
                );
            }

            // Case 2: No appointments – get patient detail with user
            $patient = Patient::with('user')->find($id);

            if (!$patient) {
                return $this->sendError('Patient not found.', [], 404);
            }

            return $this->sendResponse(
                new \App\Http\Resources\PatientResource($patient),
                'Patient found but no appointments exist.'
            );
        } catch (\Exception $e) {
            return $this->sendError('Failed to fetch patient details.', ['error' => $e->getMessage()], 500);
        }
    }
    public function patient_appointmen_summary($id)
    {
        try {
            // Fetch appointments of the patient
            $appointments = Appointment::where('patient_id', $id)->get();

            if ($appointments->isEmpty()) {
                return $this->sendResponse([
                    'summary' => [
                        'total' => 0,
                        'statuses' => []
                    ]
                ], 'No appointments found for this patient.');
            }

            // Generate summary
            $summary = [
                'total' => $appointments->count(),
                'statuses' => $appointments->groupBy('status')->map(fn($group) => $group->count())
            ];

            return $this->sendResponse(['summary' => $summary], 'Appointment summary retrieved successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Failed to fetch appointment summary.', ['error' => $e->getMessage()], 500);
        }
    }
}
