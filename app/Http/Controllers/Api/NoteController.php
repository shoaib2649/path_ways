<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNoteRequest;
use App\Http\Resources\NoteResource;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Note;
use App\Models\Patient;
use App\Models\Provider;
use App\Models\TrainingAndHiring;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NoteController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $isProvider = Provider::where('user_id', $user->id)->first();
        $isTrainee = TrainingAndHiring::where('user_id', $user->id)->first();

        if ($isProvider) {
            // Fetch notes created assigned to the trainee 
            $notes = Note::where('provider_id', $isProvider->id)->latest()->get();
        } elseif ($isTrainee) {

            // Fetch notes assigned to the trainee
            $notes = Note::where('supervision_id', $isTrainee->id)->with('provider.user', 'appointment', 'patient.user', 'supervision')
                ->latest()
                ->get();
        } else {
            return $this->sendError('Unauthorized. Only providers or trainees can view notes.', 403);
        }

        return $this->sendResponse(NoteResource::collection($notes), 'Notes retrieved successfully');
    }


    public function store(StoreNoteRequest $request)
    {
        $user = Auth::user();
        $providerLogin = Provider::where('user_id', $user->id)->first();

        $status = ($request->filled('provider_id') && $request->filled('supervision_id'))
            ? 'pending'
            : 'complete';

        DB::beginTransaction();
        try {
            $note = Note::create([
                'appointment_id'   => $request->appointment_id,
                'provider_id'      => $request->provider_id,
                'supervision_id'   => $request->supervision_id,
                'patient_id'       => $request->patient_id,
                'encounter'        => $request->encounter,
                'cpt_code'         => $request->cpt_code ?? null,
                'cpt_description'  => $request->cpt_description ?? null,
                'fees'             => $request->fees ?? 0.00,
                'submitted_by'     => $user->user_role ?? null,
                'sign'             => $providerLogin ? true : false, // Automatically sign if provider
                'status'           => $status,
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
    public function get_appointment_note($id)
    {
        $note = Note::where('appointment_id', $id)->with('appointment.modifiers')->first();
        if (!$note) {
            return response()->json([
                'success' => false,
                'message' => 'This appointment has no note.'
            ], 404);
        }

        return $this->sendResponse(new NoteResource($note), 'Note retrieved successfully.');
    }
    public function getNotesByProvider($providerId)
    {
        try {
            $notes = Note::where('provider_id', $providerId)->get();

            return $this->sendResponse(NoteResource::collection($notes), 'Notes retrieved successfully!');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve notes.', $e->getMessage(), 500);
        }
    }


    // ===================

    // Create Note
    // public function create(StoreNoteRequest $request)
    // {

    //     $user = Auth::user();
    //     $isProvider = Provider::where('user_id', $user->id)->first();
    //     $isTrainee  = TrainingAndHiring::where('user_id', $user->id)->first();

    //     // $request->validate([
    //     //     'appointment_id' => 'required|integer',
    //     //     'patient_id'     => 'required|integer',
    //     //     'encounter'      => 'required|string',
    //     //     'fees'           => 'nullable|numeric',
    //     // ]);

    //     $status = $isProvider ? 'completed' : 'pending_review';

    //     DB::beginTransaction();
    //     try {
    //         $note = Note::create([
    //             'appointment_id' => $request->appointment_id,
    //             'provider_id'    => $isProvider ? $isProvider->id : null,
    //             // this is the trainee_id 
    //             'supervision_id' => $isTrainee ?  $isTrainee->id : null,
    //             'patient_id'     => $request->patient_id,
    //             'encounter'      => $request->encounter,
    //             'content'        => $request->content ?? null,
    //             'fees'           => $request->fees ?? 0.00,
    //             'status'         => $status,
    //             'submitted_by'   => $user->user_role,
    //             'sign'           => $isProvider ? true : false,
    //         ]);


    //         // if ($isProvider) {
    //         //     Invoice::create([
    //         //         'note_id'    => $note->id,
    //         //         'patient_id' => $note->patient_id,
    //         //         'amount'     => $note->fees ?? 0.00,
    //         //         'status'     => 'unpaid',
    //         //     ]);
    //         // }

    //         DB::commit();
    //         return response()->json(['message' => 'Note created', 'data' => $note]);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json(['error' => 'Creation failed.', 'details' => $e->getMessage()], 500);
    //     }
    // }
    public function create(StoreNoteRequest $request)
    {
        $user = Auth::user();

        $isProvider = Provider::where('user_id', $user->id)->first();
        $isTrainee  = TrainingAndHiring::where('user_id', $user->id)->first();

        DB::beginTransaction();

        try {
            // Determine if provider is assigning note to a trainee
            $supervisionId = $request->supervision_id ?? ($isTrainee ? $isTrainee->id : null);

            // Default values
            $status = 'pending_review';
            $encounter = $request->encounter ?? null;
            $sign = false;

            if ($isProvider) {
                if ($request->has('draft') && $request->boolean('draft') == true) {
                    // Provider saves as draft
                    $status = 'pending_review';
                    $sign = false;
                } elseif ($supervisionId) {
                    // Provider assigns to trainee
                    $status = 'pending_review';
                    $encounter = null; // Let trainee fill the note
                    $sign = false;
                } else {
                    // Provider completes the note directly
                    $status = 'completed';
                    $sign = true;

                    // ✅ Update appointment status to 'completed'
                    Appointment::where('id', $request->appointment_id)->update([
                        'status' => 'completed',
                    ]);
                }
            }
            $note = Note::create([
                'appointment_id' => $request->appointment_id,
                'provider_id'    => $isProvider ? $isProvider->id : $request->provider_id,
                'supervision_id' => $supervisionId,
                'patient_id'     => $request->patient_id,
                'encounter'      => $encounter,
                'status'         => $status,
                'submitted_by'   => $user->user_role,
                'sign'           => $sign,
            ]);


            if ($isProvider && ($supervisionId == null || $status === 'completed')) {

                Invoice::create([
                    'note_id'     => $note->id,
                    'patient_id'  => $note->patient_id,
                    'amount'      => $note->fees ?? 0.00,
                    'status'      => 'unpaid',
                    'issued_date' => now(),
                    'due_date'    => now()->addDays(30),
                ]);
            }

            // Optional: Create invoice if provider submitted directly
            // if ($isProvider && !$supervisionId) {
            //     Invoice::create([
            //         'note_id'    => $note->id,
            //         'patient_id' => $note->patient_id,
            //         'amount'     => $note->fees ?? 0.00,
            //         'status'     => 'unpaid',
            //     ]);
            // }

            DB::commit();

            return response()->json([
                'message' => 'Note created successfully.',
                'data'    => $note,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error'   => 'Note creation failed.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    // Submit by provider or trainee
    public function submit(StoreNoteRequest $request, $note_id)
    {
        $note = Note::findOrFail($note_id);
        $user = Auth::user();
        $isProvider = Provider::where('user_id', $user->id)->first();
        $isTrainee  = TrainingAndHiring::where('user_id', $user->id)->first();

        $request->validate([
            'encounter' => 'required|string',
        ]);

        if ($isProvider) {
            DB::beginTransaction();
            try {
                $note->update([
                    // "provider_id" => $isProvider->id,
                    // 'encounter' => $request->encounter,
                    'status'  => 'completed',
                    'sign'    => true,
                    // 'submitted_by'   => $user->user_role,
                ]);
                // ✅ Update appointment status to 'completed'
                Appointment::where('id', $request->appointment_id)->update([
                    'status' => 'completed',
                ]);

                Invoice::create([
                    'note_id'     => $note->id,
                    'patient_id'  => $note->patient_id,
                    'amount'      => $note->fees ?? 0.00,
                    'status'      => 'unpaid',
                    'issued_date' => now(),
                    'due_date'    => now()->addDays(30),
                ]);
                // if (!$note->invoice) {
                //     Invoice::create([
                //         'note_id'    => $note->id,
                //         'patient_id' => $note->patient_id,
                //         'amount'     => $note->fees ?? 0.00,
                //         'status'     => 'unpaid',
                //     ]);
                // }

                DB::commit();
                return response()->json(['message' => 'Submitted by provider', 'data' => $note]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['error' => 'Submission failed.', 'details' => $e->getMessage()], 500);
            }
        }

        if ($isTrainee) {
            $note->update([
                'supervision_id' => $isTrainee->id,
                'provider_id' => $request->provider_id,
                'encounter' => $request->encounter,
                'status'  => 'pending_review',
            ]);
            return response()->json(['message' => 'Submitted to provider', 'data' => $note]);
        }

        return response()->json(['error' => 'Unauthorized action.'], 403);
    }

    // Assign trainee
    public function assignTrainee(StoreNoteRequest $request, $note_id)
    {
        $note = Note::findOrFail($note_id);
        $user = Auth::user();
        $provider = Provider::where('user_id', $user->id)->first();

        if (!$provider) {
            return response()->json(['error' => 'Only providers can assign.'], 403);
        }

        $request->validate([
            'trainee_id'          => 'required|exists:training_and_hirings,id',
        ], [
            'trainee_id.required' => 'Trainee is required.',
            'trainee_id.exists'   => 'Selected trainee does not exist.',
        ]);


        $note->update([
            'supervision_id' => $request->trainee_id,
            'status'         => 'pending_review',
        ]);

        return response()->json(['message' => 'Note assigned to trainee.', 'data' => $note]);
    }

    // Reassign trainee
    public function reassignTrainee(StoreNoteRequest $request, $note_id)
    {
        return $this->assignTrainee($request, $note_id);
    }

    // Approve and generate invoice
    public function approve($note_id)
    {
        $note = Note::findOrFail($note_id);
        $user = Auth::user();
        $provider = Provider::where('user_id', $user->id)->first();

        if (!$provider) {
            return response()->json(['error' => 'Only providers can approve.'], 403);
        }

        DB::beginTransaction();
        try {
            $note->update([
                'status' => 'completed',
                'sign'   => true,
            ]);

            // if (!$note->invoice) {
            //     Invoice::create([
            //         'note_id'    => $note->id,
            //         'patient_id' => $note->patient_id,
            //         'amount'     => $note->fees ?? 0.00,
            //         'status'     => 'unpaid',
            //     ]);
            // }

            DB::commit();
            return response()->json(['message' => 'Note approved & invoice generated', 'data' => $note]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Approval failed.', 'details' => $e->getMessage()], 500);
        }
    }


    public function getInvoice($note_id)
    {
        $note = Note::with([
            'appointment.modifiers',
            'patient.user',
            'patient.caregivers',
            'supervision.user',
            'provider.user',
            'invoice'
        ])->find($note_id);

        if (!$note) {
            return response()->json([
                'success' => false,
                'message' => 'Note not found.',
            ], 404);
        }

        // Sum all appointment modifiers’ fee
        $totalAppointmentModifierFees = $note->appointment?->modifiers?->sum(function ($modifier) {
            return $modifier->pivot->fee ?? 0;
        }) ?? 0;

        // Determine bill_to info based on Self-pay or Insurance
        $isSelfPay = $note->patient?->individual_appointments === 'Self-pay';

        $bill_to = $isSelfPay
            ? [
                'first_name' => $note->patient?->user?->first_name,
                'last_name'  => $note->patient?->user?->last_name,
                'email'      => $note->patient?->user?->email,
            ]
            : [
                'groupId'         => $note->patient?->groupId ?? '',
                'insurance_payer' => $note->patient?->insurance_payer ?? '',
                'memberId'        => $note->patient?->memberId ?? '',
            ];

        return response()->json([
            'note_id'         => $note->id,
            'patient_name'    => $note->patient?->user?->first_name . ' ' . $note->patient?->user?->last_name,
            'patient_email'   => $note->patient?->user?->email,
            'patient_phone'   => $note->patient?->user?->phone,
            'caregivers'      => $note->patient?->caregivers?->map(fn($c) => [
                'first_name' => $c->first_name,
                'last_name'  => $c->last_name,
                'email'      => $c->email,
            ]) ?? [],

            'provider_name'   => $note->provider?->user?->first_name . ' ' . $note->provider?->user?->last_name,
            'provider_email'  => $note->provider?->user?->email,

            'supervision'     => [
                'first_name' => $note->supervision?->user?->first_name,
                'last_name'  => $note->supervision?->user?->last_name,
                'email'      => $note->supervision?->user?->email,
                'phone'      => $note->supervision?->user?->phone,
            ],

            'appointment_modifiers' => $note->appointment?->modifiers?->map(fn($modifier) => [
                'description' => $modifier->description ?? null,
                'pivot_fee'   => $modifier->pivot->fee ?? 0,
                'date'        => optional($modifier->pivot->created_at)->format('Y-m-d'),
            ]) ?? [],

            'appointment_modifier_fees' => $totalAppointmentModifierFees,
            'invoice_number'            => $note->invoice?->invoice_number,
            'invoice_status'            => $note->invoice?->status,
            'issue_date' => $note->invoice?->issued_date?->format('Y-m-d'),
            'due_date'   => $note->invoice?->due_date?->format('Y-m-d'),
            'bill_to'                   => $bill_to,
        ]);
    }
    public function getAllInvoicesByPatient($patient_id)
    {
        // Fetch patient with all invoices, their notes, and related models

        $patient = Patient::with([
            'user',
            'caregivers',
            'invoices.note.appointment.modifiers',
            'invoices.note.supervision.user',
            'invoices.note.provider.user'
        ])->find($patient_id);

        if (!$patient) {
            return response()->json([
                'success' => false,
                'message' => 'Patient not found.',
            ], 404);
        }

        $invoices = $patient->invoices->map(function ($invoice) use ($patient) {
            $note = $invoice->note;

            // Total modifier fees
            $totalModifierFees = $note->appointment?->modifiers?->sum(fn($mod) => $mod->pivot->fee ?? 0) ?? 0;

            // Check if self-pay
            $isSelfPay = $note->patient?->individual_appointments === 'Self-pay';

            $bill_to = $isSelfPay
                ? [
                    'first_name' => $note->patient?->user?->first_name,
                    'last_name'  => $note->patient?->user?->last_name,
                    'email'      => $note->patient?->user?->email,
                ]
                : [
                    'groupId'         => $note->patient?->groupId ?? '',
                    'insurance_payer' => $note->patient?->insurance_payer ?? '',
                    'memberId'        => $note->patient?->memberId ?? '',
                ];

            return [
                'invoice_id'      => $invoice->id,
                'invoice_number'  => $invoice->invoice_number,
                'status'          => $invoice->status,
                'issued_date'     => $invoice->issued_date,
                'due_date'        => $invoice->due_date,
                'note_id'         => $note?->id,

                'patient_name'    => $note->patient?->user?->first_name . ' ' . $note->patient?->user?->last_name,
                'patient_email'   => $note->patient?->user?->email,
                'patient_phone'   => $note->patient?->user?->phone,

                'caregivers'      => $note->patient?->caregivers?->map(fn($c) => [
                    'first_name' => $c->first_name,
                    'last_name'  => $c->last_name,
                    'email'      => $c->email,
                ]) ?? [],

                'provider_name'   => $note->provider?->user?->first_name . ' ' . $note->provider?->user?->last_name,
                'provider_email'  => $note->provider?->user?->email,

                'supervision'     => [
                    'first_name' => $note->supervision?->user?->first_name,
                    'last_name'  => $note->supervision?->user?->last_name,
                    'email'      => $note->supervision?->user?->email,
                    'phone'      => $note->supervision?->user?->phone,
                ],

                'appointment_modifiers' => $note->appointment?->modifiers?->map(fn($mod) => [
                    'description' => $mod->description,
                    'pivot_fee'   => $mod->pivot->fee ?? 0,
                    'date'        => optional($mod->pivot->created_at)->format('Y-m-d'),
                ]) ?? [],

                'appointment_modifier_fees' => $totalModifierFees,
                'bill_to'                   => $bill_to,
            ];
        });

        return response()->json([
            'patient_id' => $patient->id,
            'patient_name' => $patient->user?->first_name . ' ' . $patient->user?->last_name,
            'patient_email' => $patient->user?->email,
            'invoices' => $invoices
        ]);
    }
}
