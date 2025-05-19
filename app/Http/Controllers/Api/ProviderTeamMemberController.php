<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProviderTeamMember;
use Illuminate\Http\Request;
use App\Http\Resources\ProviderTeamMemberResource;
use Exception;

class ProviderTeamMemberController extends Controller
{
    public function index()
    {
        try {
            $members = ProviderTeamMember::latest()->get();
            return $this->sendResponse(
                ProviderTeamMemberResource::collection($members),
                'Members fetched successfully.'
            );
        } catch (Exception $e) {
            return $this->sendError('Failed to fetch members.', ['error' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        try {
            $member = ProviderTeamMember::create($request->all());
            return $this->sendResponse(
                new ProviderTeamMemberResource($member),
                'Member created successfully.'
            );
        } catch (Exception $e) {
            return $this->sendError('Failed to create member.', ['error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        try {
            $member = ProviderTeamMember::findOrFail($id);
            return $this->sendResponse(
                new ProviderTeamMemberResource($member),
                'Member fetched successfully.'
            );
        } catch (Exception $e) {
            return $this->sendError('Failed to fetch member.', ['error' => $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $member = ProviderTeamMember::findOrFail($id);
            $member->update($request->all());
            return $this->sendResponse(
                new ProviderTeamMemberResource($member),
                'Member updated successfully.'
            );
        } catch (Exception $e) {
            return $this->sendError('Failed to update member.', ['error' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $member = ProviderTeamMember::findOrFail($id);
            $member->delete();
            return $this->sendResponse([], 'Member deleted successfully.');
        } catch (Exception $e) {
            return $this->sendError('Failed to delete member.', ['error' => $e->getMessage()]);
        }
    }
}
