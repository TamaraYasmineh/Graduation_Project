<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientReferralResource extends JsonResource
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
        
            'patient' => [
                'id' => $this->patient->id,
                'name' => $this->patient->user->name,
            ],
        
            'referred_by' => [
                'id' => $this->referredBy->id,
                'name' => $this->referredBy->user->name,
            ],
        
            'type' => $this->type,
        
            'referred_to_doctor' => $this->type === 'internal'
                ? [
                    'id' => $this->referredToDoctor->id,
                    'name' => $this->referredToDoctor->user->name,
                    'specialization' => $this->referredToDoctor->specialization,
                ]
                : null,
        
            'external_center' => $this->type === 'external'
                ? [
                    'name' => $this->external_center_name,
                    'phone' => $this->external_center_phone,
                    'address' => $this->external_center_address,
                ]
                : null,
        
            'reason' => $this->reason,
            'notes' => $this->notes,
            'status' => $this->status,
            'referred_at' => $this->referred_at,
        ];
    }
    }
