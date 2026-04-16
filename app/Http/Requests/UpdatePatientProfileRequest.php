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
                'gender' => 'nullable|in:male,female',
                'phone' => 'nullable|string|max:20',
                'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'date_of_birth' => 'required|date|before:today',
                'country' => 'nullable|string',
                'city' => 'nullable|string',
                'emergency_contact' => 'required|string|max:20',
            ];
        }
    
}
