<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProviderModifierColorRequest;
use App\Models\Provider;
use App\Models\ProviderModifierColor;
use App\Models\Scheduler;
use App\Models\TrainingAndHiring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProviderModifierColorController extends Controller
{
    // public function index($providerId)
    // {
    //     try {
    //         $colors = ProviderModifierColor::where('provider_id', $providerId)
    //             ->select('modifier_id', 'color')
    //             ->get();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Modifier colors fetched successfully.',
    //             'data' => $colors
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to fetch modifier colors.',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }
    public function index()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: No user logged in.',
                ], 401);
            }

            // Default filter
            $query = ProviderModifierColor::query();

            // Apply role-based filtering
            switch ($user->user_role) {
                case 'provider':
                    $providerId = Provider::where('user_id', $user->id)->value('id');
                    $query->where('provider_id', $providerId);
                    break;

                case 'scheduler':
                    $schedulerId = Scheduler::where('user_id', $user->id)->value('id');
                    $query->where('scheduler_id', $schedulerId);
                    break;

                case 'training_and_hiring':
                    $traineeId = TrainingAndHiring::where('user_id', $user->id)->value('id');
                    $query->where('training_and_hiring_id', $traineeId);
                    break;

                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized: Your role is not allowed to fetch modifier colors.',
                    ], 403);
            }

            // Get modifier_id and color
            $colors = $query->select('modifier_id', 'color')->get();

            return response()->json([
                'success' => true,
                'message' => 'Modifier colors fetched successfully.',
                'data' => $colors,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch modifier colors.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }





    // public function store(StoreProviderModifierColorRequest $request)
    // {

    //     $user = Auth::user();

    //     if (!$user || $user->user_role != 'provider') {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Unauthorized: Only providers can assign modifier colors.'
    //         ], 403);
    //     }
    //     $providerId  = Provider::where('user_id', $user->id)->value('id');

    //     $traineeId   = TrainingAndHiring::where('user_id', $user->id)->value('id');

    //     $schedulerId = Scheduler::where('user_id', $user->id)->value('id');

    //     $validated = $request->validated();

    //     $entry = ProviderModifierColor::updateOrCreate(
    //         [
    //             'provider_id'                => $validated['provider_id'],
    //             'modifier_id'                => $validated['modifier_id'],
    //             'training_and_hiring_id'     => $validated['training_and_hirings'],
    //             'scheduler_id'               => $validated['scheduler_id'],
    //         ],
    //         ['color' => $validated['color']]
    //     );

    //     return response()->json(['data' => $entry, 'message' => 'Color saved successfully']);
    // }
    public function store(StoreProviderModifierColorRequest $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: No user logged in.'
            ], 401);
        }

        // Default IDs set to null
        $providerId = null;
        $trainingAndHiringId = null;
        $schedulerId = null;

        // Get IDs based on user role
        switch ($user->user_role) {
            case 'provider':
                $providerId = Provider::where('user_id', $user->id)->value('id');
                break;

            case 'training_and_hiring':
                $trainingAndHiringId = TrainingAndHiring::where('user_id', $user->id)->value('id');
                break;

            case 'scheduler':
                $schedulerId = Scheduler::where('user_id', $user->id)->value('id');
                break;

            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: Invalid role.'
                ], 403);
        }

        $validated = $request->validated();

        $entry = ProviderModifierColor::updateOrCreate(
            [
                'modifier_id' => $validated['modifier_id'],
                'provider_id' => $providerId,
                'training_and_hiring_id' => $trainingAndHiringId,
                'scheduler_id' => $schedulerId,
            ],
            ['color' => $validated['color']]
        );

        return response()->json([
            'data' => $entry,
            'message' => 'Color saved successfully'
        ]);
    }

    // public function destroy($id)
    // {
    //     $entry = ProviderModifierColor::findOrFail($id);
    //     $entry->delete();
    //     return response()->json(['message' => 'Color deleted']);
    // }
}
