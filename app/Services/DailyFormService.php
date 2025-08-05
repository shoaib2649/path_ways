<?php

namespace App\Services;

use App\Models\DailyAvailabilitySummary;
use App\Models\Provider;
use App\Models\TrainingAndHiring;
use App\Models\ProviderAvailability;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DailyFormService
{

    public function handleDailyFormLogic(Request $request, $user): void
    {

        $provider = Provider::where('user_id', $user->id)->first();
        $trainee = TrainingAndHiring::where('user_id', $user->id)->first();
        if ($request->has('none') && $request->boolean('none') === true) {
            $today = Carbon::today();
            // Delete provider's availability by created_at
            if ($provider) {

                ProviderAvailability::where('provider_id', $provider->id)
                    ->whereDate('created_at', $today)
                    ->delete();
            }

            // Delete trainer's availability by created_at
            if ($trainee) {

                ProviderAvailability::where('training_id', $trainee->id)
                    ->whereDate('created_at', $today)
                    ->delete();
            }
        } else {
            DailyAvailabilitySummary::create([
                'provider_id' => $provider?->id,
                'trainee_id' => $trainee?->id,
                'therapy' => $request->therapy,
                'assessment' => $request->assessment,
                'therapy_patients' => $request->therapyPatients,
                'assessment_patients' => $request->assessmentPatients,
            ]);
        }
    }
}
