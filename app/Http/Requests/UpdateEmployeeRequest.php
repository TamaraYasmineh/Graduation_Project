<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('super_doctor');
    }

    public function rules(): array
    {
        $employeeId = $this->route('employee');

        return [

            // ---- users ----
            'name' => ['sometimes', 'string', 'max:255'],

            'email' => [
                'sometimes',
                'email',
                Rule::unique('users', 'email')->ignore($employeeId, 'id'),
            ],

            'phone' => ['sometimes', 'string', 'max:20'],

            'gender' => ['sometimes', 'in:male,female'],

            'profile_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:3072'
            ],

            // ---- employees ----
            'role' => ['sometimes', 'in:nurse,sanitation_worker'],

            'date_of_birth' => [
                'sometimes',
                'date',
                'before:today'
            ],

            'phone2' => ['nullable', 'string', 'max:20'],

            'academic_degree' => ['sometimes', 'string', 'max:255'],

            'degree_image' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:5120'
            ],

            'work_history' => ['nullable', 'string'],

            'chronic_diseases' => ['nullable', 'string'],

            'marital_status' => [
                'sometimes',
                'in:single,married,divorced,widowed'
            ],

            'bank_account' => ['nullable', 'string', 'max:50'],

            'sham_cash_number' => ['nullable', 'string', 'max:50'],

            'salary' => ['sometimes', 'numeric', 'min:0'],

            'shift' => ['sometimes', 'in:morning,evening'],

            'work_days' => ['sometimes', 'array'],

            'work_days.*' => [
                'in:sunday,monday,tuesday,wednesday,thursday,friday,saturday'
            ],
        ];
    }
}
