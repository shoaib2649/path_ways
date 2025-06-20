<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Patient;

class StorePatientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $externalId = $this->route('patient'); // Corrected for apiResource binding
        $userId = optional(Patient::where('external_contact_id', $externalId)->first())->user_id;

        return [
            'emailAddresses' => 'required|array|min:1',
            'emailAddresses.*.value' => 'required|email',
            'emailAddresses.0.value' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($userId ?? 0),
            ],


            'phoneNumbers' => 'required|array|min:1',
            'phoneNumbers.*.value' => 'required|string',

            'givenName' => 'required|string',
            'familyName' => 'required|string',

            'dateOfBirth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'provider_id' => 'required|exists:providers,id',

            // 'caregivers' => 'nullable|array',
            // 'caregivers.*.dateOfBirth' => 'required|string',
            // 'caregivers.*.email' => 'required|string',
            // 'caregivers.*.firstName' => 'nullable|string',
            // 'caregivers.*.lastName' => 'nullable|string',
        ];
    }
}
