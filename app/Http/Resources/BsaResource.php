<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BsaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'weight' => $this['weight'],
            'height' => $this['height'],
            'bsa' => round($this['bsa'], 2),
            'unit' => 'm²',
        ];
    }
}
