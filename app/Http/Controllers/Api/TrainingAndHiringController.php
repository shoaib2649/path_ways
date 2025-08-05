<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTrainingAndHiringRequest;
use App\Http\Resources\TrainingAndHiringResource;
use App\Models\TrainingAndHiring;
use App\Models\User;
use App\Enum\UserRole;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TrainingAndHiringController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     */
    // old code
    // public function index()

    // {
    //     $items = TrainingAndHiring::with('user', 'availabilities.slots')->get();
    //     return $this->sendResponse(TrainingAndHiringResource::collection($items), 'Training and Hiring records retrieved successfully');
    // }
    public function index()
    {

        $user = Auth::user();

        // dd($user->user_role);
        // If the user has the 'training_and_hiring' role (adjust as per your enum or constant)
        if ($user->user_role == 'training_and_hiring') {
            $training_hiring = TrainingAndHiring::with(['user', 'availabilities.slots'])->where('user_id', $user->id)->first();

            if (!$training_hiring) {
                return $this->sendError('Training and Hiring profile not found', 404);
            }

            return $this->sendResponse(new TrainingAndHiringResource($training_hiring), 'Your Training and Hiring Profile');
        }

        // If not, return all training and hiring profiles
        $training_hiring = TrainingAndHiring::with(['user', 'availabilities.slots'])->get();
        return $this->sendResponse(
            TrainingAndHiringResource::collection($training_hiring),
            'All Training and Hiring records retrieved successfully'
        );
    }


    /**
     * Store a newly created resource in storage.
     */
    // old code 
    // public function store(StoreTrainingAndHiringRequest $request)
    // {
    //     DB::beginTransaction();
    //     try {
    //         // Create the User
    //         $data = $request->all();
    //         $data['user_role'] = UserRole::TH; // Adjust role as needed
    //         $user = $this->userService->createUser($data);

    //         // Create the TrainingAndHiring record
    //         $item = TrainingAndHiring::create([
    //             'user_id' => $user->id,
    //             'description' => $request->description ?? '',
    //             'title' => $request->title ?? '',

    //         ]);

    //         DB::commit();

    //         return $this->sendResponse(new TrainingAndHiringResource($item), 'Training and Hiring record created successfully!');
    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         return $this->sendError('Something went wrong.', ['error' => $e->getMessage()]);
    //     }
    // }
    // updated code 

    public function store(StoreTrainingAndHiringRequest $request)
    {
        DB::beginTransaction();

        try {
            // Step 1: Create the user first
            $data = $request->all();
            $data['user_role'] = UserRole::TH; // Training & Hiring role
            $user = $this->userService->createUser($data);

            // Step 2: Now create the TrainingAndHiring record with the user_id
            $item = TrainingAndHiring::create([
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
                'title' => $request->title ?? '',
                'description' => $request->description ?? '',
            ]);

            DB::commit();

            return $this->sendResponse(new TrainingAndHiringResource($item), 'Training and Hiring record created successfully!');
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
        $item = TrainingAndHiring::with('user')->find($id);
        if (!empty($item)) {
            return $this->sendResponse(new TrainingAndHiringResource($item), 'Training and Hiring record retrieved successfully');
        } else {
            return $this->sendError('Error occurred while showing the record.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreTrainingAndHiringRequest $request, string $id)
    {
        DB::beginTransaction();

        try {
            $item = TrainingAndHiring::findOrFail($id);
            $user = $item->user;

            // Update the User
            $userData = $request->only([
                'prefix',
                'first_name',
                'middle_name',
                'last_name',
                'full_name',
                'email',
                'google_id',
                'user_role',
                'subscribe_status',
                'phone',
                'address',
                'state',
                'postal_code',
                'country',
                'date_of_birth',
                'gender',
                'age',
                'is_active',
                'profile_image',
                'bio',
                'social_media'
            ]);
            $userData['user_role'] = $userData['user_role'] ?? UserRole::TH; // Adjust role as needed
            $user = $this->userService->updateUser($user, $userData);
            // Update the TrainingAndHiring record
            $item->update([
                // 'admin_id' => $request->admin_id ?? '',
                'description' => $request->description ?? '',
                'title' => $request->title ?? '',
            ]);

            DB::commit();

            return $this->sendResponse(new TrainingAndHiringResource($item), 'Training and Hiring record updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->sendError('Error occurred while updating the record.', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = TrainingAndHiring::find($id);

        if (!$item) {
            return $this->sendError('Training and Hiring record not found.');
        }

        $user = $item->user;
        $item->delete();

        if ($user) {
            $user->delete();
        }

        return $this->sendResponse([], 'Training and Hiring record deleted successfully!');
    }
}
