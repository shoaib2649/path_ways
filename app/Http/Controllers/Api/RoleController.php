<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::select('name')->get();
        return $this->sendResponse($roles, 'Roles fetched successfully.');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:roles,name',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $role = Role::create([
            'name' => $request->name,
        ]);

        return $this->sendResponse($role, 'Role created successfully.');
    }

    public function show($id)
    {
        $role = Role::find($id);
        if (!$role) {
            return $this->sendError('Role not found.');
        }

        return $this->sendResponse($role, 'Role fetched successfully.');
    }

    public function update(Request $request, $id)
    {
        $role = Role::find($id);
        if (!$role) {
            return $this->sendError('Role not found.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:roles,name,' . $id,
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $role->update([
            'name' => $request->name,
        ]);

        return $this->sendResponse($role, 'Role updated successfully.');
    }

    public function destroy($id)
    {
        $role = Role::find($id);
        if (!$role) {
            return $this->sendError('Role not found.');
        }

        $role->delete();

        return $this->sendResponse([], 'Role deleted successfully.');
    }

    public function assignToUser(Request $request, $userId)
    {
        $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'string|exists:roles,name',
        ]);

        $user = User::findOrFail($userId);

        $roleIds = Role::whereIn('name', $request->roles)->pluck('id');
        $user->roles()->sync($roleIds);

        return response()->json([
            'message' => 'Roles assigned to user',
            'roles' => $user->roles()->pluck('name')
        ]);
    }
}
