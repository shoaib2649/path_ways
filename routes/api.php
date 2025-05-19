<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AppointmentController;
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
use App\Http\Controllers\Api\BillingController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\EventController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Auth Controller
Route::post('/login', [AuthController::class, 'login']);
Route::get('auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);

Route::apiResource('patients', PatientController::class);
Route::apiResource('providers', ProviderController::class);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::apiResource('patients-encounter', PatientEncounterController::class);
    Route::apiResource('encounter-sections', EncounterSectionController::class);
    Route::apiResource('providers-members', ProviderTeamMemberController::class);
    Route::apiResource('insurance-providers', InsuranceProviderController::class);
    Route::apiResource('appointments', AppointmentController::class);
    Route::apiResource('facilities', FacilityController::class);
    Route::apiResource('billings', BillingController::class);
    Route::apiResource('services', ServiceController::class);
    Route::apiResource('events', EventController::class);


    Route::post('/set-availability', [ProviderAvailabilityController::class, 'setAvailability']);
    Route::get('/get-availability', [ProviderAvailabilityController::class, 'getAvailability']);
    Route::get('/check-availability', [ProviderAvailabilityController::class, 'checkAvailability']);
    Route::get('/check-all-availability', [ProviderAvailabilityController::class, 'getAllProvidersAvailability']);

    // Dropdown List Options
    Route::get('/list/options', [ListOptionController::class, 'list_options']);
});
