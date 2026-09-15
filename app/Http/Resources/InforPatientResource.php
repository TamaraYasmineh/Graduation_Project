<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'doctor' => $this->getCurrentDoctor(),

            'medical_record' => MedicalRecordResource::make(
                $this->whenLoaded('medicalRecord')
            ),
        ];
    }
    private function getCurrentDoctor(): ?array
    {
        /*
    |--------------------------------------------------------------------------
    | Accepted referral
    |--------------------------------------------------------------------------
    */

        if ($this->patient?->relationLoaded('latestApprovedInternalReferral')) {

            $referral = $this->patient->latestApprovedInternalReferral;

            if ($referral?->referredToDoctor) {

                return [
                    'id' => $referral->referredToDoctor->id,
                    'name' => $referral->referredToDoctor->user?->name,
                    'email' => $referral->referredToDoctor->user?->email,
                    'specialization' => $referral->referredToDoctor->specialization,
                ];
            }
        }


        /*
    |--------------------------------------------------------------------------
    | Pending referral
    | المريض ما زال عند الطبيب الذي طلب التحويل
    |--------------------------------------------------------------------------
    */

        if ($this->patient?->relationLoaded('latestPendingInternalReferral')) {

            $referral = $this->patient->latestPendingInternalReferral;

            if ($referral?->referredBy) {

                return [
                    'id' => $referral->referredBy->id,
                    'name' => $referral->referredBy->user?->name,
                    'email' => $referral->referredBy->user?->email,
                    'specialization' => $referral->referredBy->specialization,
                ];
            }
        }


        /*
    |--------------------------------------------------------------------------
    | No referral
    | استخدام الطبيب من Appointment
    |--------------------------------------------------------------------------
    */

        if ($this->relationLoaded('appointments')) {

            $appointment = $this->appointments->first();

            if ($appointment?->doctor) {

                return [
                    'id' => $appointment->doctor->id,
                    'name' => $appointment->doctor->user?->name,
                    'email' => $appointment->doctor->user?->email,
                    'specialization' => $appointment->doctor->specialization,
                ];
            }
        }

        return null;
    }
}
