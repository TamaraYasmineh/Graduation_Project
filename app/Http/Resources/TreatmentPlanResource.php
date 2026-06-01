<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TreatmentPlanResource extends JsonResource
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
            'medical_record_id' => $this->medical_record_id,
            'diagnosis' => $this->diagnosis,
            'protocol_id' => $this->protocol_id,
            'medication' => $this->medication,
            'duration' => $this->duration,
            'created_at' => $this->created_at,
        ];
    }
}
