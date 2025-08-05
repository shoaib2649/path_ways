<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProviderModifierColorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // ✅ Or add auth logic if needed
    }

    public function rules(): array
    {
        return [
            // 'provider_id' => 'required|exists:providers,id',
            'modifier_id' => 'required|exists:modifiers,id',
            'color' => 'required|string',
        ];
    }
}
