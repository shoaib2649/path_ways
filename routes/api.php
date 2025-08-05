<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\BillerController;
use App\Http\Controllers\Api\GoogleAuthController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\PatientEncounterController;
use App\Http\Controllers\Api\EncounterSectionController;
use App\Http\Controllers\Api\ProviderController;
use App\Http\Controllers\Api\FacilityController;
use App\Http\Controllers\Api\ProviderTeamMemberController;
use App\Http\Controllers\Api\ProviderAvailabilityController;
use App\Http\Controllers\Api\InsuranceProviderController;
use App\Http\Controllers\Api\ListOptionController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\LoginLogController;
use App\Http\Controllers\Api\ModifierController;
use App\Http\Controllers\Api\NoteController;
use App\Http\Controllers\Api\OperationAndDirectorController;
use App\Http\Controllers\Api\ProviderModifierColorController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\SchedulerController;
use App\Http\Controllers\Api\TrainingAndHiringController;
use App\Http\Controllers\SpruceNoteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SpruceWebhookController;
use Symfony\Component\Mailer\Transport\Smtp\Auth\LoginAuthenticator;

// Auth Controller
Route::post('/login', [AuthController::class, 'login']);
Route::get('auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);
Route::get('/login-logs',                       [LoginLogController::class, 'index']);


Route::post('/add_biller',                      [BillerController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout',                      [AuthController::class, 'logout'])->name('logout');

    Route::post('assign-roles/{userId}',        [RoleController::class, 'assignToUser']);

    Route::apiResource('patients-encounter',    PatientEncounterController::class);
    Route::get('/patients/search',              [PatientController::class, 'patient_search']);


    Route::apiResource('encounter-sections',     EncounterSectionController::class);
    Route::apiResource('providers-members',      ProviderTeamMemberController::class);
    Route::apiResource('insurance-providers',    InsuranceProviderController::class);

    Route::get('appointments/upcoming',          [AppointmentController::class, 'upcoming_appointments'])->name('appointments.upcoming');
    Route::apiResource('appointments',           AppointmentController::class);
    Route::get('/patients/{id}/appointments',    [AppointmentController::class, 'patient_appointment_details']);
    Route::get('/patients/{id}/appointments/summary', [AppointmentController::class, 'patient_appointmen_summary']);

    Route::delete('/appointment/modifier/{id}/delete',       [AppointmentController::class, 'modifer_delete']);



    Route::apiResource('facilities',            FacilityController::class);
    Route::apiResource('services',              ServiceController::class);
    Route::apiResource('events',                           EventController::class);


    Route::post('/set-availability',                       [ProviderAvailabilityController::class, 'setAvailability']);
    Route::get('/get-availability',                        [ProviderAvailabilityController::class, 'getAllProvidersAvailability']);
    Route::post('/daily-form',                              [ProviderAvailabilityController::class, 'dailyForm']);
    Route::get('/daily-form/login/count',                  [ProviderAvailabilityController::class, 'daily_form_count']);

    Route::get('/check-availability',                      [ProviderAvailabilityController::class, 'checkAvailability']);
    Route::patch('/provider/availability/{id}',            [ProviderAvailabilityController::class, 'updateAvailability']);

    Route::get('/check-all-availability',                  [ProviderAvailabilityController::class, 'getAllProvidersAvailability']);

    Route::apiResource('patients',                         PatientController::class);
    Route::get('/patients/{patientId}/conversations',      [PatientController::class, 'getPatientConversations']);
    Route::delete('/patient/caregiver/{id}',               [PatientController::class, 'care_giver_delete']);
    Route::delete('/all/patients/delete',                  [PatientController::class, 'deleteAllPatients']);


    Route::get('/spruce-notes/sync/{contactId}',           [SpruceNoteController::class, 'syncSpruceNotes']);

    Route::get('/conversations/{conversationId}/messages', [PatientController::class, 'getConversationMessages']);


    Route::apiResource('providers',                       ProviderController::class);
    // Provider change colour 
    Route::patch('provider/status',                   [ProviderController::class, 'change_provider_status']);

    Route::apiResource('schedulers',                      SchedulerController::class);
    Route::apiResource('billers',                         BillerController::class);
    Route::apiResource('training-and-hirings',            TrainingAndHiringController::class);
    Route::apiResource('operation-and-directors',         OperationAndDirectorController::class);

    Route::apiResource('modifiers',                       ModifierController::class);
    Route::patch('modifier/colour/{cpt_code}',            [ModifierController::class, 'change_modifier_colour']);

    // Route::get('/provider/{providerId}/modifier-colors', [ProviderModifierColorController::class, 'index']);
    Route::get('/provider/modifier-colors',              [ProviderModifierColorController::class, 'index']);
    Route::post('/provider/modifier-color',              [ProviderModifierColorController::class, 'store']);
    // Route::delete('/provider/modifier-color/{id}',       [ProviderModifierColorController::class, 'destroy']);

    Route::apiResource('notes',                           NoteController::class);
    Route::get('appointment/note/{id}',                   [NoteController::class, 'get_appointment_note']);
    Route::get('/notes/provider/{providerId}',            [NoteController::class, 'getNotesByProvider']);
    Route::get('/notes/invoice/{note_id}',                [NoteController::class, 'getInvoice']);
    Route::get('/notes/invoice/patient/{patient_id}',     [NoteController::class, 'getAllInvoicesByPatient']);

    Route::prefix('notes')->group(function () {
        Route::post('/create', [NoteController::class, 'create']);                  // Provider or Trainee
        Route::post('/submit/{note_id}', [NoteController::class, 'submit']);        // Submit by either
        Route::post('/assign-trainee/{note_id}', [NoteController::class, 'assignTrainee']); // Assign to trainee
        Route::post('/reassign/{note_id}', [NoteController::class, 'reassignTrainee']);     // Reassign trainee
        Route::post('/approve/{note_id}', [NoteController::class, 'approve']);      // Final approval by provider
    });





    // Dropdown List Options
    Route::get('/list/options',                           [ListOptionController::class, 'list_options']);
});
Route::post('/webhook/spruce',                            [SpruceWebhookController::class, 'handle']);
Route::get('/webhook/register',                           [SpruceWebhookController::class, 'register']);
