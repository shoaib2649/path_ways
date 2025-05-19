<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PatientEncounterResource;
use App\Models\PatientEncounter;
use Illuminate\Http\Request;
use Exception;

class PatientEncounterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $patientEncounters = PatientEncounter::get();
            return $this->sendResponse(PatientEncounterResource::collection($patientEncounters), 'Patient Encounters retrieved successfully!');
        } catch (Exception $e) {
            return $this->sendError('Error retrieving patient encounters.', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $encounter = PatientEncounter::create([
                'provider_id' => $request->provider_id,
                'patient_id' => $request->patient_id,
                'facility_id' => $request->facility_id,
                'speciality_id' => $request->speciality_id,
                'encounter_type_id' => $request->encounter_type_id,
                'encounter_date' => $request->encounter_date,
                'visit_reason' => $request->visit_reason,
                'provider' => $request->provider,
                'speciality' => $request->speciality,
                'encounter_type' => $request->encounter_type,
                'encounter_status' => $request->encounter_status ?? 'draft',
            ]);

            return $this->sendResponse(new PatientEncounterResource($encounter), 'Patient Encounter created successfully!');
        } catch (Exception $e) {
            return $this->sendError('Unauthorized.', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $patientEncounter = PatientEncounter::with('patient', 'provider')->findOrFail($id);
            return $this->sendResponse(new PatientEncounterResource($patientEncounter), 'Patient Encounter retrieved successfully!');
        } catch (Exception $e) {
            return $this->sendError('Patient Encounter not found.', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $encounter = PatientEncounter::findOrFail($id);

            $encounter->update([
                'provider_id' => $request->provider_id,
                'patient_id' => $request->patient_id,
                'facility_id' => $request->facility_id,
                'speciality_id' => $request->speciality_id,
                'encounter_type_id' => $request->encounter_type_id,
                'encounter_date' => $request->encounter_date,
                'visit_reason' => $request->visit_reason,
                'provider' => $request->provider,
                'speciality' => $request->speciality,
                'encounter_type' => $request->encounter_type,
                'encounter_status' => $request->encounter_status ?? 'draft',
            ]);

            return $this->sendResponse(new PatientEncounterResource($encounter), 'Patient Encounter updated successfully!');
        } catch (Exception $e) {
            return $this->sendError('Error updating the patient encounter.', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $patientEncounter = PatientEncounter::findOrFail($id);

            $patientEncounter->delete();

            return $this->sendResponse([], 'Patient Encounter deleted successfully!');
        } catch (Exception $e) {
            return $this->sendError('Error deleting the patient encounter.', ['error' => $e->getMessage()]);
        }
    }
}
