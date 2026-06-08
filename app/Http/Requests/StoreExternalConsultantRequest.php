<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreExternalConsultantRequest extends FormRequest
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

            'name' => 'required|string|max:255',

            'phone' => 'required|string|max:20',

            'specialization' => 'required|string|max:255',

            'consultation_fee' => 'required|numeric|min:1',

            'whatsapp_number' => 'required|string|max:20',

            'years_of_experience' => 'nullable|integer|min:0',

            'license_number' => 'nullable|string|max:255',

            'bio' => 'nullable|string',

            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }
}
