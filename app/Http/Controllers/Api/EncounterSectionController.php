<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\EncounterSectionResource;

use App\Http\Controllers\Controller;

use App\Models\EncounterSection;
use Illuminate\Http\Request;
use Exception;

class EncounterSectionController extends Controller
{
    public function index()
    {
        try {
            $sections = EncounterSection::all();
            return $this->sendResponse(EncounterSectionResource::collection($sections), 'Encounter sections retrieved successfully.');
        } catch (Exception $e) {
            return $this->sendError('Failed to retrieve encounter sections.', ['error' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        try {
            $section = EncounterSection::create([
                'provider_id' => $request->provider_id,
                'patient_id' => $request->patient_id,
                'facility_id' => $request->facility_id,
                'encounter_id' => $request->encounter_id,
                'chief_complaint' => $request->chief_complaint,
                'history' => $request->history,
                'medical_history' => $request->medical_history,
                'surgical_history' => $request->surgical_history,
                'family_history' => $request->family_history,
                'social_history' => $request->social_history,
                'allergies' => $request->allergies,
                'medications' => $request->medications,
                'review_of_systems' => $request->review_of_systems,
                'physical_exam' => $request->physical_exam,
                'vital_sign' => $request->vital_sign,
                'assessments' => $request->assessments,
                'procedure' => $request->procedure,
                'follow_up' => $request->follow_up,
                'json_dump' => $request->json_dump,
                'status' => $request->status ?? 'active',
            ]);

            return $this->sendResponse(new EncounterSectionResource($section), 'Encounter section created successfully.');
        } catch (Exception $e) {
            return $this->sendError('Failed to create encounter section.', ['error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        try {
            $section = EncounterSection::findOrFail($id);
            return $this->sendResponse(new EncounterSectionResource($section), 'Encounter section retrieved successfully.');
        } catch (Exception $e) {
            return $this->sendError('Failed to retrieve encounter section.', ['error' => $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $section = EncounterSection::findOrFail($id);

            $section->update([
                'provider_id' => $request->provider_id,
                'patient_id' => $request->patient_id,
                'facility_id' => $request->facility_id,
                'encounter_id' => $request->encounter_id,
                'chief_complaint' => $request->chief_complaint,
                'history' => $request->history,
                'medical_history' => $request->medical_history,
                'surgical_history' => $request->surgical_history,
                'family_history' => $request->family_history,
                'social_history' => $request->social_history,
                'allergies' => $request->allergies,
                'medications' => $request->medications,
                'review_of_systems' => $request->review_of_systems,
                'physical_exam' => $request->physical_exam,
                'vital_sign' => $request->vital_sign,
                'assessments' => $request->assessments,
                'procedure' => $request->procedure,
                'follow_up' => $request->follow_up,
                'json_dump' => $request->json_dump,
                'status' => $request->status ?? $section->status,
            ]);

            return $this->sendResponse(new EncounterSectionResource($section), 'Encounter section updated successfully.');
        } catch (Exception $e) {
            return $this->sendError('Failed to update encounter section.', ['error' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $section = EncounterSection::findOrFail($id);
            $section->delete();

            return $this->sendResponse([], 'Encounter section deleted successfully.');
        } catch (Exception $e) {
            return $this->sendError('Failed to delete encounter section.', ['error' => $e->getMessage()]);
        }
    }
}
