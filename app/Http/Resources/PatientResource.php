<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'date_of_birth' => $this->date_of_birth,
            'country' => $this->country,
            'city' => $this->city,
            'emergency_contact' => $this->emergency_contact,
        ];
    }
}
