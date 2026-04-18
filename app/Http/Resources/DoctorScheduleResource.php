<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
class DoctorScheduleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'doctor_id' => $this->id,
            'doctor_name' => $this->user->name,

            'schedules' => $this->schedules->map(function ($schedule) {
                return [
                    'date' => $schedule->date,
                    'day' => Carbon::parse($schedule->date)->locale('ar')->translatedFormat('l'),
                    'start_time' => $schedule->start_time,
                    'end_time' => $schedule->end_time,
                ];
            })
        ];
    }
}
