<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateConsultantRequest extends FormRequest
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

            'name' => 'sometimes|string|max:255',

            'phone' => 'sometimes|string|max:20',

            'specialization' => 'sometimes|string|max:255',

            'bio' => 'nullable|string',

            'years_of_experience' => 'nullable|integer|min:0',

            'consultation_fee' => 'sometimes|numeric|min:1',

            'whatsapp_number' => 'sometimes|string|max:20',

            'is_active' => 'sometimes|boolean',

            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }
}
