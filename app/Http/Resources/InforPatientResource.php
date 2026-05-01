<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
class InforPatientResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'status' => $this->status,
            'phone' => $this->phone,
            'role' => $this->role,
            'age' => $this->patient?->date_of_birth
    ? Carbon::parse($this->patient->date_of_birth)->age
    : null,
            'profile_image' => $this->profile_image 
                ? asset('storage/' . $this->profile_image) 
                : null,
                
            'patient' => PatientResource::make($this->whenLoaded('patient')),
          'doctor' => $this->whenLoaded('appointments', function () {
    $appointment = $this->appointments->first();

    return $appointment?->doctor ? [
        'id' => $appointment->doctor->id,
        'name' => $appointment->doctor->user?->name,
        'email' => $appointment->doctor->user?->email,
        'specialization' => $appointment->doctor->specialization,
    ] : null;
}),
            'medical_record' => MedicalRecordResource::make(
                $this->whenLoaded('medicalRecord')
            ),
        ];
    }
}
