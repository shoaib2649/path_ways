<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ModifierResource;
use App\Models\Modifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ModifierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $modifiers = Modifier::get();
        return $this->sendResponse(ModifierResource::collection($modifiers), 'Modifier Record');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function change_modifier_colour(Request $request, $cpt_code)
    {
        DB::beginTransaction();

        try {
            $modifier = Modifier::where('cpt_code', $cpt_code)->first();

            if (!$modifier) {
                return $this->sendError('Modifier not found.', [], 404);
            }

            // Update colour
            $modifier->update([
                'colour' => $request->input('colour')
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Modifier colour updated successfully.',
                'data' => [
                    'cpt_code' => $modifier->cpt_code,
                    'colour' => $modifier->colour,
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update modifier colour.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
