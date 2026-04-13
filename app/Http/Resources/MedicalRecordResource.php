<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicalRecordResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'chronic_diseases' => $this->chronic_diseases,
            'allergies' => $this->allergies,
            'medications' => $this->medications,
            'is_smoker' => $this->is_smoker,
            'height' => $this->height,
            'weight' => $this->weight,
        ];
    }
}
