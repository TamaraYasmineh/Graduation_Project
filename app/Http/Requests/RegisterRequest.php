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
            'gender' => 'nullable|in:male,female',
            'phone' => 'nullable|string|max:20',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'role' => ['required', Rule::in(['doctor', 'patient', 'secretary'])],
    
            // Doctor
            'specialization' => 'required_if:role,doctor|string',
            'years_of_experience' => 'nullable|integer',
            'license_number' => 'required_if:role,doctor|unique:doctors,license_number',
            'bio' => 'nullable|string',
            'department' => 'nullable|string',
            // Patient
            'date_of_birth' => 'nullable|date',
            'country' => 'nullable|string',
            'city' => 'nullable|string',
            'emergency_contact' => 'nullable|string',
            // Secretary
            'hire_date' => 'nullable|date',
            'work_shift' => 'nullable|string',
        ];
    }
}
