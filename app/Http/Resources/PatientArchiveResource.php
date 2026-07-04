<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientArchiveResource extends JsonResource
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
                'name' => $this->patient->name,
                'phone' => $this->patient->phone,
            ],
            'archived_by' => [
                'id' => $this->archivedBy->id,
                'name' => $this->archivedBy->name,
            ],
            'reason' => $this->reason,
            'reason_text' => $this->getReasonText(),
            'note' => $this->note,
            'archived_at' => $this->archived_at->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }

    private function getReasonText(): string
    {
        return match ($this->reason) {
            'recovered' => 'شفاء',
            'death' => 'وفاة',
            'follow_up_ended' => 'إنهاء متابعة',
            'final_transfer' => 'تحويل نهائي',
            'other' => 'سبب آخر',
            default => 'غير معروف',
        };
    }
    }

