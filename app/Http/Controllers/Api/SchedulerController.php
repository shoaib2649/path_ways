<?php

namespace App\Http\Controllers\Api;

use App\Enum\UserRole;
use App\Models\Scheduler;
use App\Http\Requests\StoreSchedulerRequest;
use App\Http\Resources\SchedulerResource;
use App\Services\UserService;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SchedulerController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        $schedulers = Scheduler::with('user')->get();
        return $this->sendResponse(SchedulerResource::collection($schedulers), 'Scheduler records retrieved.');
    }

    public function store(StoreSchedulerRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->all();
            $data['user_role'] = UserRole::Scheduler;
            $user = $this->userService->createUser($data);
            $scheduler = Scheduler::create([
                'user_id' => $user->id,
                'notes' => $request->notes,
                'specialization' => $request->specialization,
            ]);

            DB::commit();
            return $this->sendResponse(new SchedulerResource($scheduler), 'Scheduler record created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Something went wrong.', ['error' => $e->getMessage()]);
        }
    }

    public function show(string $id)
    {
        $scheduler = Scheduler::with('user')->find($id);
        if ($scheduler) {
            return $this->sendResponse(new SchedulerResource($scheduler), 'Scheduler record retrieved successfully.');
        }
        return $this->sendError('Scheduler not found.');
    }

    public function update(StoreSchedulerRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            $scheduler = Scheduler::findOrFail($id);
            $user = $scheduler->user;

            $userData = $request->only(['prefix', 'mrn', 'first_name', 'middle_name', 'last_name', 'full_name', 'email', 'google_id', 'user_role', 'subscribe_status', 'phone', 'address', 'state', 'postal_code', 'country', 'date_of_birth', 'gender', 'age', 'is_active', 'profile_image', 'bio', 'social_media']);

            $userData['user_role'] = $userData['user_role'] ?? UserRole::Scheduler;
            $this->userService->updateUser($user, $userData);

            $scheduler->update([
                'notes' => $request->notes ?? $scheduler->notes,
                'specialization' => $request->specialization ?? $scheduler->specialization,
            ]);

            DB::commit();
            return $this->sendResponse(new SchedulerResource($scheduler), 'Scheduler record updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Error occurred while updating the scheduler record.', ['error' => $e->getMessage()]);
        }
    }

    public function destroy(string $id)
    {
        $scheduler = Scheduler::find($id);

        if (!$scheduler) {
            return $this->sendError('Scheduler not found.');
        }

        $user = $scheduler->user;
        $scheduler->delete();

        if ($user) {
            $user->delete(); 
        }

        return $this->sendResponse([], 'Scheduler deleted successfully!');
    }
}
