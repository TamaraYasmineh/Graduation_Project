<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ArchivePatientRequest extends FormRequest
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
            'reason' => 'required|in:recovered,death,follow_up_ended,final_transfer,other',
            'note' => 'nullable|string|max:1000',
        ];
    }
    public function messages(): array
    {
        return [
            'reason.required' => 'سبب الأرشفة مطلوب',
            'reason.in' => 'سبب الأرشفة غير صحيح',
        ];
    }
}
