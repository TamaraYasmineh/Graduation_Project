<?php

namespace App\Http\Resources;

use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConsultantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        $doctor = $this->consultable;

        $isInternal = $doctor instanceof Doctor;

        return [
            'id' => $this->id,

            'name' => $isInternal
                ? $doctor->user->name
                : $doctor->name,

            'specialization' => $doctor->specialization,

            'bio' => $doctor->bio,

            'years_of_experience' => $doctor->years_of_experience,

            'consultation_fee' => $this->consultation_fee,

            'profile_image' => $isInternal
            ? $doctor->user->profile_image_url
            : $doctor->profile_image_url,
        ];

    }
}
