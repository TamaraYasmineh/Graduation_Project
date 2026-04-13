<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreMedicalRecordRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'chronic_diseases' => 'nullable|string',
            'allergies' => 'nullable|string',
            'medications' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_smoker' => 'nullable|boolean',
            'height' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'blood_type' => 'nullable|string',
            'surgeries' => 'nullable|string',
            'family_history' => 'nullable|string',
            'blood_pressure' => 'nullable|string',
        ];
    }
}
