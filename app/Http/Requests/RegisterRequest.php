<?php

namespace App\Http\Requests;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'gender' => 'sometimes|nullable|in:male,female',
            'phone' => 'sometimes|nullable|string|max:20',
            'profile_image' => 'sometimes|nullable|image|mimes:jpg,jpeg,png|max:2048',
            'role' => ['required', Rule::in(['doctor', 'patient', 'secretary'])],
            'fcm_token' => 'nullable|string',
            // Doctor
            'specialization' => 'required_if:role,doctor|string',
            'years_of_experience' => 'sometimes|nullable|integer',
            'license_number' => 'required_if:role,doctor|unique:doctors,license_number',
            'bio' => 'nullable|string',
            'department' => 'sometimes|nullable|string',
            // Patient
            'date_of_birth' => 'sometimes|nullable|date',
            'country' => 'sometimes|nullable|string',
            'city' => 'sometimes|nullable|string',
            'emergency_contact' => 'sometimes|nullable|string',
            // Secretary
            'hire_date' => 'sometimes|nullable|date',
            'work_shift' => 'sometimes|nullable|string',
        ];
    }
}
