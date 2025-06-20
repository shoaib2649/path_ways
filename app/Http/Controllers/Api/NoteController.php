<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNoteRequest;
use App\Http\Resources\NoteResource;
use App\Models\Note;
use Illuminate\Support\Facades\DB;

class NoteController extends Controller
{
    public function index()
    {
        $notes = Note::latest()->get();
        return $this->sendResponse(NoteResource::collection($notes), 'Notes retrieved successfully');
    }

    public function store(StoreNoteRequest $request)
    {
        DB::beginTransaction();
        try {
            $note = Note::create([
                'appointment_id'   => $request->appointment_id,
                'patient_id'       => $request->patient_id,
                'encounter'        => $request->encounter,
                'cpt_code'         => $request->cpt_code ?? null,
                'cpt_description'  => $request->cpt_description ?? null,
                'fees'             => $request->fees ?? 0.00,
                'submitted_by'     => $request->submitted_by ?? null,
                'sign'             => $request->sign ?? null,
                'status'           => $request->status ?? 'pending',
            ]);

            DB::commit();
            return $this->sendResponse(new NoteResource($note), 'Note created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Failed to create note.', ['error' => $e->getMessage()]);
        }
    }

    public function show(string $id)
    {
        $note = Note::find($id);

        if (!$note) {
            return $this->sendError('Note not found.');
        }

        return $this->sendResponse(new NoteResource($note), 'Note retrieved successfully.');
    }

    public function update(StoreNoteRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            $note = Note::findOrFail($id);

            $note->update([
                'encounter'        => $request->encounter ?? $note->encounter,
                'cpt_code'         => $request->cpt_code ?? $note->cpt_code,
                'cpt_description'  => $request->cpt_description ?? $note->cpt_description,
                'fees'             => $request->fees ?? $note->fees,
                'submitted_by'     => $request->submitted_by ?? $note->submitted_by,
                'sign'             => $request->sign ?? $note->sign,
                'status'           => $request->status ?? $note->status,
            ]);

            DB::commit();
            return $this->sendResponse(new NoteResource($note), 'Note updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Failed to update note.', ['error' => $e->getMessage()]);
        }
    }

    public function destroy(string $id)
    {
        $note = Note::find($id);

        if (!$note) {
            return $this->sendError('Note not found.');
        }

        try {
            $note->delete();
            return $this->sendResponse([], 'Note deleted successfully!');
        } catch (\Exception $e) {
            return $this->sendError('Failed to delete note.', ['error' => $e->getMessage()]);
        }
    }
}
