<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TreatmentSessionResource extends JsonResource
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
            'treatment_plan_id' => $this->treatment_plan_id,
            'session_type' => $this->session_type,
            'session_date' => $this->session_date,

            'height' => $this->height,
            'weight' => $this->weight,
            'bsa' => $this->bsa,
            'dosage' => $this->dosage,

            'lab_requested' => $this->lab_requested,
            'lab_tests_requested' => $this->lab_tests_requested,
            'lab_results' => $this->lab_results,

            'notes' => $this->notes,
        ];
    }
}
