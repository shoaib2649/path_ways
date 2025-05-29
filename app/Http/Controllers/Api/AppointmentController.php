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
use App\Models\Provider;
use App\Models\ProviderAvailability;
use App\Models\ProviderAvailabilitySlot;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AppointmentController extends Controller
{

    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $providerId = Provider::where('user_id', $user->id)->value('id');

            if ($providerId) {
                $appointments = Appointment::where('provider_id', $providerId)
                    ->with(['patient.user', 'provider'])
                    ->get();
            } else {
                $appointments = Appointment::with(['patient.user', 'provider'])->get();
            }

            return $this->sendResponse(
                AppointmentResource::collection($appointments),
                'Appointments retrieved successfully.'
            );
        } catch (Exception $e) {
            return $this->sendError('Failed to retrieve appointments.', ['error' => $e->getMessage()]);
        }
    }


    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $overlap = Appointment::where('provider_id', $request->provider_id)
                ->whereDate('appointment_date', $request->appointment_date)
                ->where(function ($query) use ($request) {
                    $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                        ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                        ->orWhere(function ($q) use ($request) {
                            $q->where('start_time', '<=', $request->start_time)
                                ->where('end_time', '>=', $request->end_time);
                        });
                })->exists();

            if ($overlap) {
                return $this->sendError(['message' => 'Appointment time overlaps with an existing appointment'], 409);
            }
            // Step 3: Create appointment with resolved patient ID
            $appointment = Appointment::create([
                'patient_id' => $request->patient_id,
                'provider_id' => $request->provider_id,
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
                'color_primary' => $request->color_primary,
                'color_secondary' => $request->color_secondary,
                'actions' => $request->actions,
                'all_day' => $request->all_day ?? false,
                'resizable_before_start' => $request->resizable_before_start ?? false,
                'resizable_after_end' => $request->resizable_after_end ?? false,
                'draggable' => $request->draggable ?? false,
            ]);

            // Step 4: Commit the transaction
            DB::commit();
            return $this->sendResponse(new AppointmentResource($appointment), 'Appointment created successfully!');
        } catch (Exception $e) {
            // Step 5: Roll back if any error occurs
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
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'appointment_date' => $request->appointment_date,
                'title' => $request->title,
                'description' => $request->description,
                'color_primary' => $request->color_primary,
                'color_secondary' => $request->color_secondary,
                'actions' => $request->actions,
                'all_day' => $request->all_day ?? false,
                'resizable_before_start' => $request->resizable_before_start ?? false,
                'resizable_after_end' => $request->resizable_after_end ?? false,
                'draggable' => $request->draggable ?? false,
            ]);

            return $this->sendResponse(new AppointmentResource($appointment), 'Appointment updated successfully!');
        } catch (Exception $e) {
            return $this->sendError('Failed to update appointment.', ['error' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $appointment = Appointment::findOrFail($id);
            $appointment->delete();

            return $this->sendResponse([], 'Appointment deleted successfully!');
        } catch (Exception $e) {
            return $this->sendError('Failed to delete appointment.', ['error' => $e->getMessage()]);
        }
    }


    public function upcoming_appointments()
    {
        try {

            $today = Carbon::now()->toDateString();
            $appointments = Appointment::with(['provider', 'patient'])
                ->whereDate('appointment_date', '>=', $today)
                ->orderBy('appointment_date', 'asc')
                ->get();

            return $this->sendResponse(
                UpcomingAppointmentResource::collection($appointments),
                'Upcoming availability slots retrieved successfully.'
            );
        } catch (Exception $e) {
            return $this->sendError('Failed to retrieve upcoming availability slots.', ['error' => $e->getMessage()]);
        }
    }
}
