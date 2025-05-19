<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Billing;
use Illuminate\Http\Request;
use Exception;
use App\Http\Resources\BillingResource;

class BillingController extends Controller
{
    public function index()
    {
        try {
            $billings = Billing::with('appointment')->get();
            return $this->sendResponse(BillingResource::collection($billings), 'Billings retrieved successfully.');
        } catch (Exception $e) {
            return $this->sendError('Failed to retrieve billings.', ['error' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        try {
            $data = $request->only([
                'appointment_id',
                'patient_id',
                'provider_id',
                'meeting_type',
                'time',
                'amount',
                'rate'
            ]);

            $billing = Billing::create($data);

            return $this->sendResponse(new BillingResource($billing), 'Billing created successfully!');
        } catch (Exception $e) {
            return $this->sendError('Failed to create billing.', ['error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        try {
            $billing = Billing::with('appointment')->findOrFail($id);
            return $this->sendResponse(new BillingResource($billing), 'Billing retrieved successfully.');
        } catch (Exception $e) {
            return $this->sendError('Failed to retrieve billing.', ['error' => $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $billing = Billing::findOrFail($id);

            $data = $request->only([
                'appointment_id',
                'patient_id',
                'provider_id',
                'meeting_type',
                'time',
                'amount',
                'rate'
            ]);

            $billing->update($data);

            return $this->sendResponse(new BillingResource($billing), 'Billing updated successfully!');
        } catch (Exception $e) {
            return $this->sendError('Failed to update billing.', ['error' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $billing = Billing::findOrFail($id);
            $billing->delete();

            return $this->sendResponse([], 'Billing deleted successfully.');
        } catch (Exception $e) {
            return $this->sendError('Failed to delete billing.', ['error' => $e->getMessage()]);
        }
    }
}
