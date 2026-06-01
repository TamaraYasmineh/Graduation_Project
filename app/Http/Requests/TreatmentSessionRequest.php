<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TreatmentSessionRequest extends FormRequest
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

            'treatment_plan_id' => 'required|exists:treatment_plans,id',
    
           // 'session_type' => 'required|in:lab_request,treatment',
    
            'session_date' => 'required|date',
    
            // treatment session
            'height' => 'required_if:session_type,treatment|nullable|numeric',
            'weight' => 'required_if:session_type,treatment|nullable|numeric',
            'bsa' => 'required_if:session_type,treatment|nullable|numeric',
            'dosage' => 'required_if:session_type,treatment|nullable|numeric',
    
            // labs
            'lab_requested' => 'boolean',
    
            'lab_tests_requested' => 'nullable|string',
    
            'lab_results' => 'nullable|string',
    
            'notes' => 'nullable|string',
        ];
    }
}
