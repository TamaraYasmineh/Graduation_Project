<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreReferralRequest extends FormRequest
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
            'type' => 'required|in:internal,external',
            
            // للتحويل الداخلي
            'referred_to_doctor_id' => 'required_if:type,internal|nullable|exists:doctors,id',
            
            // للتحويل الخارجي
            'external_center_name' => 'required_if:type,external|nullable|string|max:255',
            'external_center_phone' => 'required_if:type,external|nullable|string|max:20',
            'external_center_address' => 'required_if:type,external|nullable|string|max:500',
            
            // معلومات عامة
            'reason' => 'required|string|max:1000',
            'notes' => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'نوع التحويل مطلوب',
            'type.in' => 'نوع التحويل يجب أن يكون داخلي أو خارجي',
            'referred_to_doctor_id.required_if' => 'يجب اختيار الطبيب المحول إليه في التحويل الداخلي',
            'referred_to_doctor_id.exists' => 'الطبيب غير موجود',
            'external_center_name.required_if' => 'اسم المركز الخارجي مطلوب',
            'external_center_phone.required_if' => 'رقم التواصل مطلوب',
            'external_center_address.required_if' => 'العنوان مطلوب',
            'reason.required' => 'سبب التحويل مطلوب',
        ];
    }
    }

