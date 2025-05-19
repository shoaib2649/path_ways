<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProviderRequest extends FormRequest
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
            // 'mrn' => 'required|string',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'full_name' => 'nullable|string|max:255',
            // 'email' => 'required|email|unique:users,email',
            'google_id' => 'nullable|string',
            'password' => 'nullable|string|min:6',
            'date_of_birth' => 'nullable|date',

            // Provider fields
            'specialization' => 'nullable|string|max:255',
            'license_number' => 'nullable|string|max:255',
            'license_expiry_date' => 'nullable|date',
            'experience_years' => 'nullable|integer|min:0',
            'education' => 'nullable|string|max:1000',
            'certifications' => 'nullable|string|max:1000',
            'clinic_name' => 'nullable|string|max:255',
            'clinic_address' => 'nullable|string|max:500',
            'is_verified' => 'nullable|boolean',
            'doctor_notes' => 'nullable|string|max:2000',
            'consultation_fee' => 'nullable|numeric|min:0',
            'profile_slug' => 'nullable|string|max:255',

        ];
    }
}
