<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Http\Resources\ServiceResource;
use Illuminate\Http\Request;
use Exception;

class ServiceController extends Controller
{
    public function index()
    {
        try {
            $services = Service::all();
            return $this->sendResponse(ServiceResource::collection($services), 'Services retrieved successfully!');
        } catch (Exception $e) {
            return $this->sendError('Failed to fetch services.', ['error' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        try {
            $service = Service::create([
                'name' => $request->name,
                'duration' => $request->duration,
                'price' => $request->price,
                'description' => $request->description,
                'category' => $request->category,
                'requires_approval' => $request->requires_approval ?? false,
            ]);

            return $this->sendResponse(new ServiceResource($service), 'Service created successfully!');
        } catch (Exception $e) {
            return $this->sendError('Failed to create service.', ['error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        try {
            $service = Service::findOrFail($id);
            return $this->sendResponse(new ServiceResource($service), 'Service retrieved successfully!');
        } catch (Exception $e) {
            return $this->sendError('Service not found.', ['error' => $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $service = Service::findOrFail($id);

            $service->update([
                'name' => $request->name,
                'duration' => $request->duration,
                'price' => $request->price,
                'description' => $request->description,
                'category' => $request->category,
                'requires_approval' => $request->requires_approval ?? $service->requires_approval,
            ]);

            return $this->sendResponse(new ServiceResource($service), 'Service updated successfully!');
        } catch (Exception $e) {
            return $this->sendError('Failed to update service.', ['error' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $service = Service::findOrFail($id);
            $service->delete();
            return $this->sendResponse([], 'Service deleted successfully!');
        } catch (Exception $e) {
            return $this->sendError('Failed to delete service.', ['error' => $e->getMessage()]);
        }
    }
}
