<?php

namespace App\Http\Controllers\Api;

use App\Enum\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProviderResource;
use App\Models\Provider;
use App\Models\User;
use App\Http\Requests\StoreProviderRequest;
use App\Models\Scheduler;
use App\Models\TrainingAndHiring;
use Illuminate\Support\Facades\DB;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $user = Auth::user();
        // Check if user has "provider" role
        if ($user->user_role == 'provider') {
            $provider = Provider::with('user', 'availabilities.slots')->where('user_id', $user->id)->first();
            if (!$provider) {
                return $this->sendError('Provider not found', 404);
            }
            return $this->sendResponse(new ProviderResource($provider), 'Your Provider Profile');
        }

        // If not provider, return all providers
        $providers = Provider::with('user')->get();
        return $this->sendResponse(ProviderResource::collection($providers), 'All Providers');
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

    // public function change_provider_status(Request $request, $id)
    // {
    //     DB::beginTransaction();

    //     try {
    //         $provider = Provider::find($id);

    //         if (!$provider) {
    //             return $this->sendError('Provider not found.', [], 404);
    //         }

    //         $provider->update([
    //             'colour' => $request->input('colour')
    //         ]);

    //         DB::commit();

    //         return $this->sendResponse([
    //             'id' => $provider->id,
    //             'colour' => $provider->colour,
    //         ], 'Provider colour updated successfully.');
    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         return $this->sendError('Failed to update provider colour.', ['error' => $e->getMessage()]);
    //     }
    // }
    public function change_provider_status(Request $request)
    {
        DB::beginTransaction();

        try {
            $user = Auth::user();

            if (!$user) {
                return $this->sendError('Unauthorized: No user logged in.', [], 401);
            }

            $colour = $request->input('colour');

            if (!$colour) {
                return $this->sendError('Color is required.', [], 422);
            }

            $updatedModel = null;

            switch ($user->user_role) {
                case 'provider':
                    $updatedModel = Provider::where('user_id', $user->id)->first();
                    break;
                case 'scheduler':
                    $updatedModel = Scheduler::where('user_id', $user->id)->first();
                    break;
                case 'training_and_hiring':
                    $updatedModel = TrainingAndHiring::where('user_id', $user->id)->first();
                    break;

                default:
                    return $this->sendError('Your role is not allowed to update color.', [], 403);
            }

            if (!$updatedModel) {
                return $this->sendError('User model not found.', [], 404);
            }

            $updatedModel->update([
                'colour' => $colour,
            ]);

            DB::commit();

            return $this->sendResponse([
                'id' => $updatedModel->id,
                'colour' => $updatedModel->colour,
            ], 'Colour updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->sendError('Failed to update colour.', ['error' => $e->getMessage()]);
        }
    }
}
