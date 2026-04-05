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
            'location' => $this->location,
            'opening_hours' => $this->opening_hours,
            'address_on_map' => $this->address_on_map,
            'branches' => $this->branches,
            'services' => $this->services,
        ];
    }
}
