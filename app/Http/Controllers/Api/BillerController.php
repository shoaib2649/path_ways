<?php

namespace App\Http\Controllers\Api;

use App\Enum\UserRole;
use App\Http\Requests\StoreBillerRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\BillerResource;
use App\Models\Biller;
use App\Services\UserService;
use Illuminate\Support\Facades\DB;

class BillerController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        $billers = Biller::with('user')->get();
        return $this->sendResponse(BillerResource::collection($billers), 'Biller Data');
    }

    public function store(StoreBillerRequest $request)
    {
        DB::beginTransaction();
        try {

            $data = $request->all();
            $data['user_role'] = UserRole::Biller;

            $user = $this->userService->createUser($data);

            $biller = Biller::create([
                'user_id' => $user->id,
                'department' => $request->department,
                'billing_code' => $request->billing_code,
                'is_active' => $request->is_active ?? null,
            ]);

            DB::commit();
            return $this->sendResponse(new BillerResource($biller), 'Biller created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Error creating biller.', ['error' => $e->getMessage()]);
        }
    }

    public function show(string $id)
    {
        $biller = Biller::with('user')->find($id);
        if ($biller) {
            return $this->sendResponse(new BillerResource($biller), 'Biller data retrieved successfully');
        }
        return $this->sendError('Biller not found.');
    }

    public function update(StoreBillerRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            $biller = Biller::findOrFail($id);
            $user = $biller->user;

            $userData = $request->only(['first_name', 'last_name', 'email', 'phone', 'user_role']);
            $userData['user_role'] = $userData['user_role'] ?? UserRole::Biller;
            $this->userService->updateUser($user, $userData);

            $biller->update([
                'department' => $request->department ?? $biller->department,
                'billing_code' => $request->billing_code ?? $biller->billing_code,
                'contact_number' => $request->contact_number ?? $biller->contact_number,
                'is_active' => $request->is_active ?? $biller->is_active,
            ]);

            DB::commit();
            return $this->sendResponse(new BillerResource($biller), 'Biller updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Error updating biller.', ['error' => $e->getMessage()]);
        }
    }

    public function destroy(string $id)
    {
        $biller = Biller::find($id);

        if (!$biller) {
            return $this->sendError('Biller not found.');
        }

        $user = $biller->user; 

        $biller->delete();

        if ($user) {
            $user->delete();
        }

        return $this->sendResponse([], 'Biller deleted successfully!');
    }
}
