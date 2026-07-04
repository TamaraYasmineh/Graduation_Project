<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCenterInfoRequest extends FormRequest
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
        //    return [
        //         'location' => 'required|string|max:255',
        //         'opening_hours' => 'required|string|max:255',
        //         'address_on_map' => 'required|string',
        //         'branches' => 'nullable|string',
        //         'services' => 'required|string',
        //         'contact' => 'required|string',
        //     ];
        return [
            'location' => 'required|string|max:255',
            'address_on_map' => 'required|string',
            'branches' => 'nullable|string',
            'services' => 'required|string',
            'contact' => 'required|string',

            'working_hours' => 'required|array|min:1',

            'working_hours.*.day' => 'required|string',
            'working_hours.*.start_time' => 'nullable|date_format:H:i',
            'working_hours.*.end_time' => 'nullable|date_format:H:i',
            'working_hours.*.is_closed' => 'required|boolean',

            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ];
    }
}
