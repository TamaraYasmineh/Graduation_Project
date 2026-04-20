<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SuperDoctorResource extends JsonResource
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

            'name' => $this->user->name,
            'email' => $this->user->email,

            'specialization' => $this->specialization,
            'years_of_experience' => $this->years_of_experience,
            'license_number' => $this->license_number,
            'bio' => $this->bio,
            'department' => $this->department,
            'profile_image'=>$this->profile_image ? asset('storage/' . $this->image): null,
        ];
    }

    }
