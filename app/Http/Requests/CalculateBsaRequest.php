<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CalculateBsaRequest extends FormRequest
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
            'weight' => 'required|numeric|min:1',
            'weight_unit' => 'required|in:kg,lbs',

            'height' => 'required|numeric|min:1',
            'height_unit' => 'required|in:cm,m,in',

            'bsa_based_dose' => 'required|numeric|min:0.1',
            'dose_unit' => 'required|in:mg/m2,g/m2,mcg/m2',

            'formula' => 'required|in:mosteller,dubois',

            'desired_unit' => 'required|in:mg,g,mcg',
        ];
    }
}
