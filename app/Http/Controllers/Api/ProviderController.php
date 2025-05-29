<?php

namespace App\Http\Controllers\Api;

use App\Enum\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProviderResource;
use App\Models\Provider;
use App\Models\User;
use App\Http\Requests\StoreProviderRequest;
use Illuminate\Support\Facades\DB;
use App\Services\UserService;

class ProviderController extends Controller
{


    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $providers = Provider::with('user')->get();
        return $this->sendResponse(ProviderResource::collection($providers), 'Provider Data');
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(StoreProviderRequest $request)
    {

        DB::beginTransaction();
        try {
            // Create the user first
            $data = $request->all();

            $data['user_role'] = UserRole::Provider;
            $user = $this->userService->createUser($data);

            // Now create the provider record with the user_id
            $provider = Provider::create([
                'user_id' => $user->id,
                'specialization' => $request->specialization,
                'license_number' => $request->license_number,
                'license_expiry_date' => $request->license_expiry_date,
                'experience_years' => $request->experience_years,
                'education' => $request->education,
                'certifications' => $request->certifications,
                'clinic_name' => $request->clinic_name,
                'clinic_address' => $request->clinic_address,
                'available_days' => $request->available_days,
                'available_time' => $request->available_time,
                'is_verified' => $request->is_verified ?? false,
                'doctor_notes' => $request->doctor_notes,
                'consultation_fee' => $request->consultation_fee,
                'profile_slug' => $request->profile_slug,
            ]);
            DB::commit();
            return $this->sendResponse(new ProviderResource($provider), 'Provider record created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Unauthorized.', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $provider = Provider::with('user')->find($id);
        if ($provider) {
            return $this->sendResponse(new ProviderResource($provider), 'Provider data retrieved successfully');
        } else {
            return $this->sendError('Provider not found.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreProviderRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            $provider = Provider::findOrFail($id);
            $user = $provider->user;
            // Update the User
            $userData = $request->only(['prefix', 'mrn', 'first_name', 'middle_name', 'last_name', 'full_name', 'email', 'google_id', 'user_role', 'subscribe_status', 'phone', 'address', 'state', 'postal_code', 'country', 'date_of_birth', 'gender', 'age', 'is_active', 'profile_image', 'bio', 'social_media']);
            $userData['user_role'] = $userData['user_role'] ?? UserRole::Provider;
            $this->userService->updateUser($user, $userData);
            // Update the Provider
            $provider->update([
                'specialization' => $request->specialization ?? $provider->specialization,
                'license_number' => $request->license_number ?? $provider->license_number,
                'license_expiry_date' => $request->license_expiry_date ?? $provider->license_expiry_date,
                'experience_years' => $request->experience_years ?? $provider->experience_years,
                'education' => $request->education ?? $provider->education,
                'certifications' => $request->certifications ?? $provider->certifications,
                'clinic_name' => $request->clinic_name ?? $provider->clinic_name,
                'clinic_address' => $request->clinic_address ?? $provider->clinic_address,
                'available_days' => $request->available_days ?? $provider->available_days,
                'available_time' => $request->available_time ?? $provider->available_time,
                'is_verified' => $request->is_verified ?? $provider->is_verified ?? false,
                'doctor_notes' => $request->doctor_notes ?? $provider->doctor_notes,
                'consultation_fee' => $request->consultation_fee ?? $provider->consultation_fee,
                'profile_slug' => $request->profile_slug ?? $provider->profile_slug,
            ]);
            DB::commit();
            return $this->sendResponse(new ProviderResource($provider), 'Provider record updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Error occurred while updating the provider record.', ['error' => $e->getMessage()]);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $provider = Provider::find($id);

        if (!$provider) {
            return $this->sendError('Provider not found.');
        }

        $user = $provider->user; 

        $provider->delete();

        if ($user) {
            $user->delete();
        }

        return $this->sendResponse([], 'Provider deleted successfully!');
    }
}
