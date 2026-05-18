<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('super_doctor');
    }

    public function rules(): array
    {
        $isNurse = $this->input('role') === 'nurse';

        return [
            // ---- بيانات users ----
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', 'unique:users,email'],
            'phone'         => ['required', 'string', 'max:20'],
            'gender'        => ['required', 'in:male,female'],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:3072'],

            // ---- بيانات employees ----
            'role'             => ['required', 'in:nurse,sanitation_worker'],
            'date_of_birth' => 'required|date|before:today',
            'phone2'          => ['nullable', 'string', 'max:20'],
            'academic_degree'  => ['required', 'string', 'max:255'],
            'degree_image'     => $isNurse
                ? ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120']
                : ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'work_history'     => ['nullable', 'string'],
            'chronic_diseases' => ['nullable', 'string'],
            'marital_status'   => ['required', 'in:single,married,divorced,widowed'],
            'bank_account'     => ['nullable', 'string', 'max:50'],
            'sham_cash_number' => ['nullable', 'string', 'max:50'],
            'salary'           => ['required', 'numeric', 'min:0'],
            'shift' => ['required', 'in:morning,evening'],
            'work_days' => ['required', 'array'],
            'work_days.*' => ['in:sunday,monday,tuesday,wednesday,thursday,friday,saturday'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'            => 'الاسم مطلوب.',
            'name.max'                 => 'الاسم لا يتجاوز 255 حرف.',
            'email.required'           => 'البريد الإلكتروني مطلوب.',
            'email.email'              => 'صيغة البريد الإلكتروني غير صحيحة.',
            'email.unique'             => 'البريد الإلكتروني مستخدم مسبقاً.',
            'phone.required'           => 'رقم الهاتف الأول مطلوب.',
            'gender.required'          => 'الجنس مطلوب.',
            'gender.in'                => 'الجنس يجب أن يكون male أو female.',
            'profile_image.image'      => 'صورة الموظف يجب أن تكون صورة صالحة.',
            'profile_image.mimes'      => 'صورة الموظف يجب أن تكون jpg أو jpeg أو png.',
            'profile_image.max'        => 'حجم صورة الموظف لا يتجاوز 3 ميغابايت.',
            'role.required'            => 'الدور مطلوب.',
            'role.in'                  => 'الدور يجب أن يكون nurse أو sanitation_worker.',
            'age.required'             => 'العمر مطلوب.',
            'age.integer'              => 'العمر يجب أن يكون رقماً صحيحاً.',
            'age.min'                  => 'العمر لا يقل عن 18 سنة.',
            'age.max'                  => 'العمر لا يتجاوز 70 سنة.',
            'academic_degree.required' => 'الشهادة العلمية مطلوبة.',
            'degree_image.required'    => 'صورة الشهادة مطلوبة للممرض.',
            'degree_image.mimes'       => 'صورة الشهادة يجب أن تكون jpg أو png أو pdf.',
            'degree_image.max'         => 'حجم صورة الشهادة لا يتجاوز 5 ميغابايت.',
            'marital_status.required'  => 'الحالة الاجتماعية مطلوبة.',
            'marital_status.in'        => 'الحالة الاجتماعية: single أو married أو divorced أو widowed.',
            'salary.required'          => 'الراتب مطلوب.',
            'salary.numeric'           => 'الراتب يجب أن يكون رقماً.',
            'salary.min'               => 'الراتب لا يكون سالباً.',
            'date_of_birth.required' => 'تاريخ الميلاد مطلوب.',
            'date_of_birth.date' => 'صيغة تاريخ الميلاد غير صحيحة.',
            'date_of_birth.before' => 'يجب أن يكون تاريخ الميلاد قبل اليوم.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone'   => $this->phone   ? trim($this->phone)   : null,
            'phone_2' => $this->phone_2 ? trim($this->phone_2) : null,
        ]);
    }
}
