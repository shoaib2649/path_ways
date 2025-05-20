<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
        return [
            // User fields
            'mrn' => 'nullable|string',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'full_name' => 'nullable|string|max:255',
            // 'email' => 'required|email|unique:users,email',
            'google_id' => 'nullable|string',
            // 'password' => 'nullable|string|min:6',
            'date_of_birth' => 'nullable|date',
            'patient_type' => 'required|string',

            // Patient fields
            // 'provider_id' => 'nullable|exists:providers,id',
            'mr' => 'nullable|string',
            'suffix' => 'nullable|string',
            'social_security_number' => 'nullable|string',
            'blood_score' => 'nullable|numeric',
            'lifestyle_score' => 'nullable|numeric',
            'supplement_medication_score' => 'nullable|numeric',
            'physical_vital_sign_score' => 'nullable|numeric',
            'image' => 'nullable|string',
            'module_level' => 'nullable|string',
            'qualification' => 'nullable|string',
            // 'provider_name' => 'required|string',
            'status' => 'nullable|string',
            'wait_list' => 'nullable|boolean',
            'group_appointments' => 'nullable|boolean',
            'individual_appointments' => 'nullable|boolean',
            'location' => 'nullable|string',
        ];
    }
}
