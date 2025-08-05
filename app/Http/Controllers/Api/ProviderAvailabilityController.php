<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProviderAvailabilityResource;
use App\Models\Appointment;
use App\Models\Event;
use App\Models\ListOption;
use App\Models\Provider;
use App\Models\ProviderException;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use App\Models\ProviderAvailability;
use App\Models\ProviderAvailabilitySlot;
use App\Models\TrainingAndHiring;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use SebastianBergmann\Diff\Exception;
use App\Services\DailyFormService;

class ProviderAvailabilityController extends Controller
{

    protected $dailyFormService;

    public function __construct(DailyFormService $dailyFormService)
    {
        $this->dailyFormService = $dailyFormService;
    }
    // 1. add the new Availability
    public function setAvailability(Request $request)
    {

        try {
            $validated = $request->validate([
                // 'provider_id' => 'required|exists:providers,id',
                'title' => 'required|string|max:255',

                'slots.*.*.start_time' => 'required|date_format:H:i',
                'slots.*.*.end_time' => 'required|date_format:H:i',
                // 'slots.*.*.date' => 'required',

                'type' => 'required|in:in_person,spruce',
                'location' => 'nullable|string|max:255',
                'recurrence' => 'nullable|string|max:255',

            ]);
            // $user = Auth::user();
            // if ($user->user_role == 'provider') {
            //     // If this logged-in user is a provider, grab their provider ID:
            //     $providerId = Provider::where('user_id', $user->id)->value('id');
            // } else {
            //     // Otherwise, fall back to taking provider_id from the incoming request:
            //     $providerId = $request->input('provider_id');
            // }
            // // 2) Validate we got a provider ID
            // if (! $providerId) {
            //     return response()->json([
            //         'message' => 'Failed to create availability.',
            //         'error'   => 'Provider not found.',
            //     ], 404);
            // }

            // // 3) (Optional) Validate that that provider actually exists
            // $exists = Provider::where('id', $providerId)->exists();
            // if (! $exists) {
            //     return response()->json([
            //         'message' => 'Failed to create availability.',
            //         'error'   => 'Invalid provider ID.',
            //     ], 404);
            // }

            DB::beginTransaction();

            // Create the Provider Availability
            $availability = ProviderAvailability::create([
                'provider_id' => $request->provider_id,
                'training_id' => $request->training_id,
                'title' => $request->title,
                'type' => $request->type,
                'location' => $request->location,
                'recurrence' => $request->recurrence,
            ]);



            // Add Slots without duplicates
            foreach ($request->slots as $day => $timeSlots) {
                foreach ($timeSlots as $slot) {
                    $duplicate = ProviderAvailabilitySlot::where('provider_availability_id', $availability->id)
                        ->where('day_of_week', $day)
                        ->where('start_time', $slot['start_time'])
                        ->where('end_time', $slot['end_time'])
                        ->exists();

                    if (!$duplicate) {
                        ProviderAvailabilitySlot::create([
                            'provider_availability_id' => $availability->id,
                            'day_of_week' => $day,
                            'start_time' => $slot['start_time'],
                            'end_time' => $slot['end_time'],
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json(['message' => 'Availability created successfully.'], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Error creating provider availability: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to create availability.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    // Get all the provider Availability and authenticated provider availablity
    public function getAllProvidersAvailability()
    {
        try {
            $user = Auth::user();
            $providerId = Provider::where('user_id', $user->id)->value('id');
            $trainingId = TrainingAndHiring::where('user_id', $user->id)->value('id');
            if ($providerId) {
                $availabilities = ProviderAvailability::where('provider_id', $providerId)->with(['slots'])->get();
            } elseif ($trainingId) {
                $availabilities = ProviderAvailability::where('training_id', $trainingId)->with(['slots'])->get();
            } else {
                $availabilities = ProviderAvailability::with(['slots'])->get();
            }
            return $this->sendResponse(ProviderAvailabilityResource::collection($availabilities), 'Availabilities retrieved successfully.');
        } catch (Exception $e) {
            return $this->sendError('Failed to retrieve availabilities.', ['error' => $e->getMessage()]);
        }
    }

    // **2. Check Provider Availability**
    public function checkAvailability(Request $request)
    {

        // dd($providerId = Provider::where('user_id', Auth::id())->value('id'));
        try {
            $providerId = Provider::where('user_id', Auth::id())->value('id');

            if (!$providerId) {
                return $this->sendError('Provider ID is required or user is not authenticated.', [], 401);
            }

            $availabilities = ProviderAvailability::where('provider_id', $providerId)->with(['slots'])->get();

            if ($availabilities->isEmpty() || $availabilities->every(fn($availability) => $availability->slots->isEmpty())) {
                return $this->sendError('This provider is not available.', [], 404);
            }

            return $this->sendResponse(ProviderAvailabilityResource::collection($availabilities), 'Availabilities retrieved successfully.');
        } catch (Exception $e) {
            return $this->sendError('Failed to retrieve availabilities.', ['error' => $e->getMessage()]);
        }
    }

    // Update the provider availability
    // public function updateAvailability(Request $request, $id)
    // {
    //     try {
    //         // $validated = $request->validate([
    //         //     'title' => 'sometimes|required|string|max:255',
    //         //     'type' => 'sometimes|required|in:in_person,telehealth',
    //         //     'location' => 'nullable|string|max:255',
    //         //     'recurrence' => 'nullable|string|max:255',
    //         //     'slots.*.*.start_time' => 'required_with:slots|date_format:H:i',
    //         //     'slots.*.*.end_time' => 'required_with:slots|date_format:H:i',
    //         // ]);

    //         // $providerId = Provider::where('user_id', Auth::id())->value('id');
    //         $user = Auth::user();
    //         $providerId = Provider::where('user_id', $user->id)->value('id');   
    //         if ($providerId) {
    //             $availability = ProviderAvailability::where('id', $id)
    //                 ->where('provider_id', $providerId)
    //                 ->first();

    //             // return response()->json(['message' => 'Unauthorized', 'error' => 'Provider not found.'], 403);
    //         } else {
    //             $availability = ProviderAvailability::where('id', $id)
    //                 ->where('provider_id', $request->provider_id)
    //                 ->first();
    //                 // dd($availability);
    //             // return response()->json(['message' => 'Unauthorized', 'error' => 'Provider not found.'], 403);
    //         }
    //         if (!$availability) {
    //             return response()->json(['message' => 'Availability not found.'], 404);
    //         }

    //         DB::beginTransaction();
    //         $availability->update($request->only(['title', 'type', 'location', 'recurrence']));

    //         if ($request->has('slots')) {
    //             // Delete old slots
    //             ProviderAvailabilitySlot::where('provider_availability_id', $availability->id)->delete();

    //             // Create new slots
    //             foreach ($request->slots as $day => $timeSlots) {
    //                 foreach ($timeSlots as $slot) {
    //                     ProviderAvailabilitySlot::create([
    //                         'provider_availability_id' => $availability->id,
    //                         'day_of_week' => $day,
    //                         'start_time' => $slot['start_time'],
    //                         'end_time' => $slot['end_time'],
    //                     ]);
    //                 }
    //             }
    //         }

    //         DB::commit();

    //         return response()->json(['message' => 'Availability updated successfully.']);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         \Log::error('Availability update error: ' . $e->getMessage());

    //         return response()->json([
    //             'message' => 'Failed to update availability.',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }

    public function updateAvailability(Request $request, $id)
    {

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|in:in_person,spruce',
            'location' => 'nullable|string|max:255',
            'recurrence' => 'nullable|string|max:255',
            'slots.*.*.start_time' => 'required_with:slots|date_format:H:i',
            'slots.*.*.end_time' => 'required_with:slots|date_format:H:i',
        ]);

        try {
            $user = Auth::user();
            $providerId = Provider::where('user_id', $user->id)->value('id');

            if ($providerId) {
                // If the logged-in user is a provider
                $availability = ProviderAvailability::where('id', $id)
                    ->where('provider_id', $providerId)
                    ->first();
            } else {
                // If the user is not a provider (admin or other role)
                if (!$request->has('provider_id')) {
                    return response()->json(['message' => 'Provider ID is required for this operation.'], 422);
                }

                $availability = ProviderAvailability::where('id', $id)
                    ->where('provider_id', $request->provider_id)
                    ->first();
            }

            if (!$availability) {
                return response()->json(['message' => 'Availability not found.'], 404);
            }

            DB::beginTransaction();

            // Update availability
            $availability->update($request->only(['title', 'type', 'location', 'recurrence']));

            // Update slots if provided
            if ($request->has('slots')) {
                // Delete old slots
                ProviderAvailabilitySlot::where('provider_availability_id', $availability->id)->delete();

                // Create new slots
                foreach ($request->slots as $day => $timeSlots) {
                    foreach ($timeSlots as $slot) {
                        ProviderAvailabilitySlot::create([
                            'provider_availability_id' => $availability->id,
                            'day_of_week' => $day,
                            'start_time' => $slot['start_time'],
                            'end_time' => $slot['end_time'],
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json(['message' => 'Availability updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Availability update error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to update availability.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // public function getAllProvidersAvailability()
    // {
    //     $providerRoleIds = User::where('user_role', 'provider')->pluck('id');
    //     $providers = Provider::whereIn('id', $providerRoleIds)->orderBy('created_at', 'DESC')->get();
    //     $data = $providers->map(function ($provider) {
    //         $today = Carbon::now()->dayOfWeek; // Get current day of the week (0 = Sunday, 6 = Saturday)
    //         $currentTime = Carbon::now()->format('H:i'); // Get current time in HH:MM format

    //         // Check if provider has availability for today
    //         $availability = ProviderAvailability::where('provider_id', $provider->id)
    //             ->where('day', $today)
    //             ->where('start_time', '<=', $currentTime)
    //             ->where('end_time', '>', $currentTime)
    //             ->exists();
    //         // Check if there are any exceptions today
    //         $hasException = ProviderException::where('provider_id', $provider->id)
    //             ->whereDate('date', Carbon::today())
    //             ->where('start_time', '<=', $currentTime)
    //             ->where('end_time', '>', $currentTime)
    //             ->exists();

    //         // Provider is available if scheduled and has no exception
    //         $isAvailable = $availability && !$hasException;

    //         return [
    //             'provider_id' => $provider->id,
    //             'name' => $provider->first_name . ' ' . $provider->last_name,
    //             'isAvailable' => $isAvailable
    //         ];
    //     });

    //     return response()->json($data, 200);
    // }

    // public function list_options()
    // {
    //     $data['options'] = ListOption::where('list_type', 'Type')->get();
    //     return $this->sendResponse($data, 'available options');
    // }

    public function dailyForm(Request $request)
    {

        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }

            // Handle availability logic
            $this->dailyFormService->handleDailyFormLogic($request, $user);

            // You can store additional data if needed here (therapy, assessment, etc.)
            return response()->json([
                'success' => true,
                'message' => 'Daily form handled successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing daily form',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function daily_form_count()
    {
        $user = Auth::user();
        return response(
            ['login_count_today' => $user->daily_login_count]
        );
    }
}
