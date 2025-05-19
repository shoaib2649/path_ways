<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Http\Resources\FacilityResource;
use Illuminate\Http\Request;
use Exception;

class FacilityController extends Controller
{
    public function index()
    {
        try {
            $facilities = Facility::all();
            return $this->sendResponse(FacilityResource::collection($facilities), 'Facilities retrieved successfully!');
        } catch (Exception $e) {
            return $this->sendError('Failed to retrieve facilities.', ['error' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        try {
            $facility = Facility::create([
                'name' => $request->name,
                'slug' => $request->slug,
                'address' => $request->address,
                'contact_information' => $request->contact_information,
                'facility_type' => $request->facility_type,
                'facility_capacity' => $request->facility_capacity,
                'status' => $request->status ?? 'Active',
            ]);

            return $this->sendResponse(new FacilityResource($facility), 'Facility created successfully!');
        } catch (Exception $e) {
            return $this->sendError('Failed to create facility.', ['error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        try {
            $facility = Facility::findOrFail($id);
            return $this->sendResponse(new FacilityResource($facility), 'Facility retrieved successfully!');
        } catch (Exception $e) {
            return $this->sendError('Facility not found.', ['error' => $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $facility = Facility::findOrFail($id);

            $facility->update([
                'name' => $request->name,
                'slug' => $request->slug,
                'address' => $request->address,
                'contact_information' => $request->contact_information,
                'facility_type' => $request->facility_type,
                'facility_capacity' => $request->facility_capacity,
                'status' => $request->status ?? $facility->status,
            ]);

            return $this->sendResponse(new FacilityResource($facility), 'Facility updated successfully!');
        } catch (Exception $e) {
            return $this->sendError('Failed to update facility.', ['error' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $facility = Facility::findOrFail($id);
            $facility->delete();

            return $this->sendResponse([], 'Facility deleted successfully!');
        } catch (Exception $e) {
            return $this->sendError('Failed to delete facility.', ['error' => $e->getMessage()]);
        }
    }
}
