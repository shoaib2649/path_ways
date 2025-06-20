<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOperationAndDirectorRequest;
use App\Http\Resources\OperationAndDirectorResource;
use App\Models\OperationAndDirector;
use App\Services\UserService;
use App\Enum\UserRole;
use Illuminate\Support\Facades\DB;

class OperationAndDirectorController extends Controller
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
        $items = OperationAndDirector::with('user')->get();
        return $this->sendResponse(OperationAndDirectorResource::collection($items), 'Operation and Director records retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOperationAndDirectorRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->all();
            $data['user_role'] = UserRole::OPD; // Constant for Operation & Director role
            $user = $this->userService->createUser($data);

            $item = OperationAndDirector::create([
                'user_id' => $user->id,
                'title' => $request->title ?? '',
                'description' => $request->description ?? '',
            ]);

            DB::commit();

            return $this->sendResponse(new OperationAndDirectorResource($item), 'Operation and Director record created successfully!');
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
        $item = OperationAndDirector::with('user')->find($id);
        if (!empty($item)) {
            return $this->sendResponse(new OperationAndDirectorResource($item), 'Operation and Director record retrieved successfully');
        } else {
            return $this->sendError('Error occurred while showing the record.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreOperationAndDirectorRequest $request, string $id)
    {
        DB::beginTransaction();

        try {
            $item = OperationAndDirector::findOrFail($id);
            $user = $item->user;

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
        
            $userData['user_role'] = $userData['user_role'] ?? UserRole::OPD;
            $user = $this->userService->updateUser($user, $userData);

            $item->update([
                'title' => $request->title ?? '',
                'description' => $request->description ?? '',
            ]);

            DB::commit();

            return $this->sendResponse(new OperationAndDirectorResource($item), 'Operation and Director record updated successfully!');
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
        $item = OperationAndDirector::find($id);

        if (!$item) {
            return $this->sendError('Operation and Director record not found.');
        }

        $user = $item->user;
        $item->delete();

        if ($user) {
            $user->delete();
        }

        return $this->sendResponse([], 'Operation and Director record deleted successfully!');
    }
}
