<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCenterInfoRequest extends FormRequest
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
            'location' => 'sometimes|string|max:255',
            'opening_hours' => 'sometimes|string|max:255',
            'address_on_map' => 'sometimes|string',
            'branches' => 'nullable|string',
            'services' => 'sometimes|string',
        ];
    }
}
