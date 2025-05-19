<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InsuranceProvider;
use App\Http\Resources\InsuranceProviderResource;
use Illuminate\Http\Request;
use Exception;

class InsuranceProviderController extends Controller
{
    public function index()
    {
        try {
            $providers = InsuranceProvider::all();
            return $this->sendResponse(
                InsuranceProviderResource::collection($providers),
                'Insurance Providers fetched successfully.'
            );
        } catch (Exception $e) {
            return $this->sendError('Failed to fetch Insurance Providers.', ['error' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        try {
            $provider = InsuranceProvider::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'website_url' => $request->website_url,
                'logo' => $request->logo,
                'status' => $request->status ?? 'Active',
                'description' => $request->description,
            ]);

            return $this->sendResponse(
                new InsuranceProviderResource($provider),
                'Insurance Provider created successfully.'
            );
        } catch (Exception $e) {
            return $this->sendError('Failed to create Insurance Provider.', ['error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        try {
            $provider = InsuranceProvider::findOrFail($id);
            return $this->sendResponse(
                new InsuranceProviderResource($provider),
                'Insurance Provider fetched successfully.'
            );
        } catch (Exception $e) {
            return $this->sendError('Insurance Provider not found.', ['error' => $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $provider = InsuranceProvider::findOrFail($id);

            $provider->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'website_url' => $request->website_url,
                'logo' => $request->logo,
                'status' => $request->status ?? $provider->status,
                'description' => $request->description,
            ]);

            return $this->sendResponse(
                new InsuranceProviderResource($provider),
                'Insurance Provider updated successfully.'
            );
        } catch (Exception $e) {
            return $this->sendError('Failed to update Insurance Provider.', ['error' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $provider = InsuranceProvider::findOrFail($id);
            $provider->delete();

            return $this->sendResponse([], 'Insurance Provider deleted successfully.');
        } catch (Exception $e) {
            return $this->sendError('Failed to delete Insurance Provider.', ['error' => $e->getMessage()]);
        }
    }
}
