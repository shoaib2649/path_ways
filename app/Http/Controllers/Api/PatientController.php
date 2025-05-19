<?php

namespace App\Http\Controllers\Api;

use App\Enum\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\PatientResource;
use App\Http\Requests\StorePatientRequest;
use App\Models\Patient;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\DB;

class PatientController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        $patients = Patient::with('user', 'provider')->get();
        return $this->sendResponse(PatientResource::collection($patients), 'Patient Record');
    }

    /**
     * Store a newly created resource in storage.
     */



    public function store(StorePatientRequest $request)
    {
        DB::beginTransaction();
        try {
            // Create the User
            $data = $request->all();
            $data['user_role'] = UserRole::Patient;
            $user = $this->userService->createUser($data);
            // Create the Patient
            $patient = Patient::create([
                'user_id' => $user->id,
                'provider_id' => $request->provider_id,
                // 'mr' => $request->mr,
                // 'suffix' => $request->suffix,
                // 'social_security_number' => $request->social_security_number,
                // 'blood_score' => $request->blood_score,
                // 'lifestyle_score' => $request->lifestyle_score,
                // 'supplement_medication_score' => $request->supplement_medication_score,
                // 'physical_vital_sign_score' => $request->physical_vital_sign_score,
                // 'image' => $request->image,
                // 'module_level' => $request->module_level,
                // 'qualification' => $request->qualification,
                'type' => $request->clientType,
                'referred_by' => $request->referred_by,
                'status' => $request->status,
                'wait_list' => $request->wait_list,
                'group_appointments' => $request->group_billing,
                'individual_appointments' => $request->individual_billing,
                'location' => $request->location,
            ]);

            DB::commit();

            return $this->sendResponse(new PatientResource($patient), 'Patient record created successfully!');
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
        $patient = Patient::with('user')->find($id);
        if (!empty($patient)) {
            return $this->sendResponse(new PatientResource($patient), 'Patient record retrieved successfully');
        } else {
            return $this->sendError('Error occurred while showing the record.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StorePatientRequest $request, string $id)
    {
        DB::beginTransaction();

        try {
            $patient = Patient::findOrFail($id);
            $user = $patient->user;
            // Update the User
            $userData = $request->only(['prefix', 'mrn', 'first_name', 'middle_name', 'last_name', 'full_name', 'email', 'google_id', 'user_role', 'subscribe_status', 'phone', 'address', 'state', 'postal_code', 'country', 'date_of_birth', 'gender', 'age', 'is_active', 'profile_image', 'bio', 'social_media']);
            $userData['user_role'] = $userData['user_role'] ?? UserRole::Patient;
            $this->userService->updateUser($user, $userData);

            // Update the Patient
            $patient->update([
                'provider_id' => $request->provider_id ?? $patient->provider_id,
                'mr' => $request->mr ?? $patient->mr,
                'suffix' => $request->suffix ?? $patient->suffix,
                'social_security_number' => $request->social_security_number ?? $patient->social_security_number,
                'blood_score' => $request->blood_score ?? $patient->blood_score,
                'lifestyle_score' => $request->lifestyle_score ?? $patient->lifestyle_score,
                'supplement_medication_score' => $request->supplement_medication_score ?? $patient->supplement_medication_score,
                'physical_vital_sign_score' => $request->physical_vital_sign_score ?? $patient->physical_vital_sign_score,
                'image' => $request->image ?? $patient->image,
                'module_level' => $request->module_level ?? $patient->module_level,
                'qualification' => $request->qualification ?? $patient->qualification,
                'provider_name' => $request->provider_name ?? $patient->provider_name,
                'status' => $request->status ?? $patient->status,
                'wait_list' => $request->wait_list ?? $patient->wait_list ?? false, // Default to false if null
                'group_appointments' => $request->group_appointments ?? $patient->group_appointments,
                'individual_appointments' => $request->individual_appointments ?? $patient->individual_appointments,
                'location' => $request->location ?? $patient->location,
            ]);

            DB::commit();

            return $this->sendResponse(new PatientResource($patient), 'Patient record updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->sendError('Error occurred while updating the patient record.', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $patient = Patient::find($id);
        if (!$patient) {
            return $this->sendError('Patient not found.');
        }
        $patient->delete();
        return $this->sendResponse([], 'Patient deleted successfully!');
    }
}
