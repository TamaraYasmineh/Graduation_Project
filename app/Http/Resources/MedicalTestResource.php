<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicalTestResource extends JsonResource
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

            'file_url' => asset('storage/' . $this->file_path),
            'file_type' => $this->file_type,

            'test_type' => $this->test_type,
            'notes' => $this->notes,

            //  uploader
            'uploaded_by_type' => $this->uploadable?->role,
            'uploaded_by_id' => $this->uploadable_id,

            //  معلومات من العلاقة
            'uploaded_by_name' => optional($this->uploadable)->name ?? null,

            'medical_record_id' => $this->medical_record_id,

            'created_at' => $this->created_at,
        ];
    }
}
