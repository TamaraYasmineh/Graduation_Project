<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePatientProfileRequest extends FormRequest
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
                'gender' => 'sometimes|in:male,female',
                'phone' => 'sometimes|string|max:20',
                'profile_image' => 'sometimes|image|mimes:jpg,jpeg,png|max:2048',
                'date_of_birth' => 'sometimes|date|before:today',
                'country' => 'sometimes|string',
                'city' => 'sometimes|string',
                'emergency_contact' => 'sometimes|string|max:20',
            ];
        }

}
