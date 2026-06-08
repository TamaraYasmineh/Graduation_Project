<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CenterInfoResource extends JsonResource
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
            'location' => $this->location,
            'address_on_map' => $this->address_on_map,
            'branches' => $this->branches,
            'services' => $this->services,
            'contact' => $this->contact,

            'working_hours' => WorkingHourResource::collection(
                $this->whenLoaded('workingHours')
            ),
        ];
    }
}
