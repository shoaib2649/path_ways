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
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use SebastianBergmann\Diff\Exception;

class ProviderAvailabilityController extends Controller
{
    public function getAvailability()
    {
        $weeklySchedule = [];

        // $user_role = auth()->user()->user_role;
        // if ($user_role == 'provider') {
        //     $result['provider_id'] = auth()->user()->id;
        //     for ($day = 0; $day <= 6; $day++) {
        //         $timeSlots = ProviderAvailability::where('provider_id', $result['provider_id'])
        //             ->where('day', $day)
        //             ->get(['start_time as start', 'end_time as end']);

        //         // Add each day's schedule into the array
        //         $weeklySchedule[] = [
        //             'dayOfWeek' => $day,
        //             'isAvailable' => !$timeSlots->isEmpty(),
        //             'timeSlots' => $timeSlots
        //         ];
        //     }

        //     // Save all weekly schedule in result
        //     $result['weeklySchedule'] = $weeklySchedule;

        //     // Fetch exceptions
        //     $result['exceptions'] = ProviderException::where('provider_id', $result['provider_id'])
        //         ->get(['date', 'start_time as start', 'end_time as end']);

        //     return $this->sendResponse($result);
        // } else {
        //     return $this->sendError('Sorry you are unauthorized');
        // }
        try {

            $availabilities = ProviderAvailability::with(['slots'])->get();

            return $this->sendResponse(ProviderAvailabilityResource::collection($availabilities), 'Availabilities retrieved successfully.');
        } catch (Exception $e) {
            return $this->sendError('Failed to retrieve availabilities.', ['error' => $e->getMessage()]);
        }
    }


    // 1. Set Availability
    public function setAvailability(Request $request)
    {
        // try {
        //     $request->validate([
        //         'provider_id' => 'required|exists:providers,id',
        //         'schedule' => 'required|array|min:7', // 7 days of the week
        //         'schedule.*.dayOfWeek' => 'required|integer|between:0,6',
        //         'schedule.*.isAvailable' => 'required|boolean'
        //     ]);

        //     // Save schedule
        //     ProviderAvailability::where('provider_id', $request->provider_id)->delete();

        //     foreach ($request->schedule as $day) {

        //         if ($day['isAvailable']) {
        //             foreach ($day['timeSlots'] as $slot) {
        //                 ProviderAvailability::create([
        //                     'provider_id' => $request->provider_id,
        //                     'day' => $day['dayOfWeek'],
        //                     'start_time' => $slot['start'],
        //                     'end_time' => $slot['end'],
        //                 ]);
        //             }
        //         }
        //     }

        //     // Save exceptions
        //     ProviderException::where('provider_id', $request->provider_id)->delete();
        //     if (!empty($request->exceptions)) {
        //         foreach ($request->exceptions as $exception) {
        //             $exception['day_off'] = false;
        //             if (empty($exception['start_time']) && empty($exception['end_time'])) {
        //                 $exception['start_time'] = '00:00:00';
        //                 $exception['end_time'] = '00:00:00';
        //                 $exception['day_off'] = true;
        //             }
        //             ProviderException::create([
        //                 'provider_id' => $request->provider_id,
        //                 'date' => $exception['date'],
        //                 'start_time' => $exception['start_time'],
        //                 'end_time' => $exception['end_time'],
        //                 'day_off' => $exception['day_off'],
        //             ]);
        //         }
        //     }


        //     return response()->json(['message' => 'Availability updated successfully'], 200);
        // } catch (Exception $e) {
        //     return response()->json(['message' => $e->getMessage()], 500);
        // }

        try {
            $validated = $request->validate([
                'provider_id' => 'required|exists:providers,id',
                'title' => 'required|string|max:255',

                'slots.*.*.start_time' => 'required|date_format:H:i',
                'slots.*.*.end_time' => 'required|date_format:H:i',
                'slots.*.*.date' => 'required',

                'type' => 'required|in:in_person,telehealth',
                'location' => 'nullable|string|max:255',
                'recurrence' => 'nullable|string|max:255',
                'service_ids' => 'required|array|min:1',
                'service_ids.*' => 'exists:services,id',
            ]);

            $providerId = Provider::where('user_id', Auth::id())->value('id');

            if (!$providerId) {
                return response()->json([
                    'message' => 'Failed to create availability.',
                    'error' => 'Provider does not exist in the system.',
                ], 404);
            }

            DB::beginTransaction();

            // Create the Provider Availability
            $availability = ProviderAvailability::create([
                'provider_id' => $providerId,
                'title' => $request->title,
                'type' => $request->type,
                'location' => $request->location,
                'recurrence' => $request->recurrence,
            ]);

            // Sync services (will attach new and remove unselected ones)
            if ($request->has('service_ids')) {
                $availability->services()->sync($request->service_ids);
            }

            // Add Slots without duplicates
            foreach ($request->slots as $day => $timeSlots) {
                foreach ($timeSlots as $slot) {
                    $duplicate = ProviderAvailabilitySlot::where('provider_availability_id', $availability->id)
                        ->where('day_of_week', $day)
                        ->where('start_time', $slot['start_time'])
                        ->where('end_time', $slot['end_time'])
                        ->where('date', $slot['date'])
                        ->exists();

                    if (!$duplicate) {
                        ProviderAvailabilitySlot::create([
                            'provider_availability_id' => $availability->id,
                            'day_of_week' => $day,
                            'start_time' => $slot['start_time'],
                            'end_time' => $slot['end_time'],
                            'date' => $slot['date'],
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

    // **2. Check Provider Availability**
    public function checkAvailability(Request $request)
    {

        // dd($providerId = Provider::where('user_id', Auth::id())->value('id'));
        try {
            $providerId = Provider::where('user_id', Auth::id())->value('id');

            if (!$providerId) {
                return $this->sendError('Provider ID is required or user is not authenticated.', [], 401);
            }

            $availabilities = ProviderAvailability::where('provider_id', $providerId)->with(['slots', 'services'])->get();

            if ($availabilities->isEmpty() || $availabilities->every(fn($availability) => $availability->slots->isEmpty())) {
                return $this->sendError('This provider is not available.', [], 404);
            }

            return $this->sendResponse(ProviderAvailabilityResource::collection($availabilities), 'Availabilities retrieved successfully.');
        } catch (Exception $e) {
            return $this->sendError('Failed to retrieve availabilities.', ['error' => $e->getMessage()]);
        }
        // $today = now()->format('l'); // e.g., 'Monday'

        // $availabilities = ProviderAvailability::get();

        // $todaySlots = [];

        // foreach ($availabilities as $availability) {
        //     $slots = $availability->slots;

        //     if (isset($slots[$today])) {
        //         $todaySlots[] = [
        //             'availability_id' => $availability->id,
        //             'title' => $availability->title,
        //             'type' => $availability->type,
        //             'location' => $availability->location,
        //             'recurrence' => $availability->recurrence,
        //             'slots' => $slots[$today]
        //         ];
        //     }
        // }

        // return response()->json($todaySlots);
        // $request->validate([
        //     // 'provider_id' => 'required|exists:users,id',
        //     'provider_id' => 'required|exists:providers,id',
        //     'date' => 'required|date',
        //     'time' => 'required|date_format:H:i',
        // ]);

        // $dayOfWeek = Carbon::parse($request->date)->dayOfWeek; // 0 = Sunday, 6 = Saturday
        // // Check if provider is available for the given day & time
        // $isAvailable = ProviderAvailability::where('provider_id', $request->provider_id)
        //     ->where('day', $dayOfWeek)
        //     ->where('start_time', '<=', $request->time)
        //     ->where('end_time', '>=', $request->time)
        //     ->exists();

        // if (!$isAvailable) {
        //     return response()->json(['available' => false, 'message' => 'Provider is not available on this day/time'], 200);
        // }
        // // check for dayoff
        // $dayOff = ProviderException::where('provider_id', $request->provider_id)
        //     ->whereDate('date', $request->date)
        //     ->where('day_off', true)
        //     ->exists();
        // if ($dayOff) {
        //     return response()->json(['available' => false, 'message' => 'Provider is off on this day'], 200);
        // }
        // // Check for exceptions (e.g., diff hours, day off)
        // $isException = ProviderException::where('provider_id', $request->provider_id)
        //     ->whereDate('date', $request->date)
        //     ->whereTime('start_time', '<=', $request->time)
        //     ->whereTime('end_time', '>=', $request->time)
        //     ->exists();

        // if (!$isException) {
        //     return response()->json(['available' => false, 'message' => 'Provider is unavailable due to an exception'], 200);
        // }

        // // Check for conflicting events (appointments)
        // $hasConflict = Appointment::where('provider_id', $request->provider_id)
        //     ->whereDate('start', $request->date)
        //     ->whereTime('start', '<=', $request->time)
        //     ->whereTime('end', '>', $request->time)
        //     ->exists();

        // if ($hasConflict) {
        //     return response()->json(['available' => false, 'message' => 'Provider has another appointment at this time'], 200);
        // }

        return response()->json(['available' => true, 'message' => 'Provider is available'], 200);
    }
    public function getAllProvidersAvailability()
    {
        $providerRoleIds = User::where('user_role', 'provider')->pluck('id');
        $providers = Provider::whereIn('id', $providerRoleIds)->orderBy('created_at', 'DESC')->get();
        $data = $providers->map(function ($provider) {
            $today = Carbon::now()->dayOfWeek; // Get current day of the week (0 = Sunday, 6 = Saturday)
            $currentTime = Carbon::now()->format('H:i'); // Get current time in HH:MM format

            // Check if provider has availability for today
            $availability = ProviderAvailability::where('provider_id', $provider->id)
                ->where('day', $today)
                ->where('start_time', '<=', $currentTime)
                ->where('end_time', '>', $currentTime)
                ->exists();
            // Check if there are any exceptions today
            $hasException = ProviderException::where('provider_id', $provider->id)
                ->whereDate('date', Carbon::today())
                ->where('start_time', '<=', $currentTime)
                ->where('end_time', '>', $currentTime)
                ->exists();

            // Provider is available if scheduled and has no exception
            $isAvailable = $availability && !$hasException;

            return [
                'provider_id' => $provider->id,
                'name' => $provider->first_name . ' ' . $provider->last_name,
                'isAvailable' => $isAvailable
            ];
        });

        return response()->json($data, 200);
    }

    // public function list_options()
    // {
    //     $data['options'] = ListOption::where('list_type', 'Type')->get();
    //     return $this->sendResponse($data, 'available options');
    // }
}
