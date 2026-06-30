<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientFullProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

        public function toArray(Request $request): array
        {
            $record = $this->medicalRecord;
            $plan = optional($record)->treatmentPlan;
            $sessions = $plan?->sessions ?? collect();
            $tests = $record?->medicalTests ?? collect();
    
            return [
    
                'patient' => [
    
                    'id' => $this->id,
                    'name' => $this->name,
                    'email' => $this->email,
                    'phone' => $this->phone,
                    'gender' => $this->gender,
                    'status' => $this->status,
                    'profile_image' => $this->profile_image_url,
    
                    'age' => optional($this->patient)->age,
                    'country' => optional($this->patient)->country,
                    'city' => optional($this->patient)->city,
                    'emergency_contact' => optional($this->patient)->emergency_contact,
                ],
    
                'medical_record' => [
    
                    'id' => optional($record)->id,
    
                    'height' => optional($record)->height,
                    'weight' => optional($record)->weight,
                    'blood_type' => optional($record)->blood_type,
                    'blood_pressure' => optional($record)->blood_pressure,
    
                    'chronic_diseases' => optional($record)->chronic_diseases,
                    'allergies' => optional($record)->allergies,
                    'surgeries' => optional($record)->surgeries,
                    'family_history' => optional($record)->family_history,
                    'is_smoker' => optional($record)->is_smoker,
                    'notes' => optional($record)->notes,
    
                    'qr_code' => $record ? $record->getQrCodeUrl() : null,
                ],
    
                'Treatment_plan' => [
    
                    'diagnosis' => optional($plan)->diagnosis,
                    'protocol_id' => optional($plan)->protocol_id,
                    'medication' => optional($plan)->medication,
                    'duration' => optional($plan)->duration,
                    'session_date' => optional($plan)->session_date,
                ],
    
                'sessions' => $sessions->map(function ($session) {
    
                    return [
    
                        'id' => $session->id,
    
                        'session_date' => $session->session_date,
    
                        'session_time' => null,
    
                        'treatment_type' => null,
    
                        'dose' => $session->dosage,
    
                        'doctor_notes' => $session->notes,
    
                        'status' => null,
    
                        'bsa' => $session->bsa,
    
                        'height' => $session->height,
    
                        'weight' => $session->weight,
    
                        'lab_requested' => $session->lab_requested,
    
                        'lab_tests_requested' => $session->lab_tests_requested,
    
                        'lab_results' => $session->lab_results,
                    ];
    
                }),
    
                'medications' => $plan?->protocol?->drugs?->map(function ($drug) use ($plan) {

    return [

        'id' => $drug->id,

        'name' => $drug->name,

        'dosage' => $drug->dose,

        'dose_basis' => $drug->dose_basis,

        'route' => $drug->route,

        'frequency' => null,

        'start_date' => $plan->session_date,

        'end_date' => null,

        'status' => 'active',

        'notes' => null,
    ];

}) ?? [],
                'lab_tests' => $tests->map(function ($test) {
    
                    return [
    
                        'id' => $test->id,
    
                        'test_name' => $test->test_type,
    
                        'test_date' => optional($test->created_at)?->toDateString(),
    
                        'result' => $test->notes,
    
                        'file_url' => $test->file_path
                            ? asset('storage/'.$test->file_path)
                            : null,
    
                        'notes' => $test->notes,
                    ];
    
                }),
    
                
            ];
        
    }
}
