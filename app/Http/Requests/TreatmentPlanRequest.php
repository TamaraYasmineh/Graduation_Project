<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TreatmentPlanRequest extends FormRequest
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
            'medical_record_id' => 'required|exists:medical_records,id',
            'diagnosis' => 'required|string',
            'protocol_id' => 'nullable|exists:protocols,id',
            'medication' => 'nullable|string',
            'duration' => 'nullable|integer',
            'session_date' => 'required|date',
        ];
    }
}
