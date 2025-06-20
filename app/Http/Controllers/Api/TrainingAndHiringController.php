<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTrainingAndHiringRequest;
use App\Http\Resources\TrainingAndHiringResource;
use App\Models\TrainingAndHiring;
use App\Models\User;
use App\Enum\UserRole;
use App\Services\UserService;
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
    public function index()
    {
        $items = TrainingAndHiring::with('user')->get();
        return $this->sendResponse(TrainingAndHiringResource::collection($items), 'Training and Hiring records retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTrainingAndHiringRequest $request)
    {
        DB::beginTransaction();
        try {
            // Create the User
            $data = $request->all();
            $data['user_role'] = UserRole::TH; // Adjust role as needed
            $user = $this->userService->createUser($data);

            // Create the TrainingAndHiring record
            $item = TrainingAndHiring::create([
                'user_id' => $user->id,
                'description' => $request->description ?? '',
                'title' => $request->title ?? '',

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
            $user=$this->userService->updateUser($user, $userData);
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
