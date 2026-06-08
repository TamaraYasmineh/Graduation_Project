<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'id' => $this->id,

            'name' => $this->user->name,
            'email' => $this->user->email,

            'specialization' => $this->specialization,
            'years_of_experience' => $this->years_of_experience,
            'license_number' => $this->license_number,
            'bio' => $this->bio,
            'department' => $this->department,
            'profile_image' => $this->user->profile_image
    ? asset('storage/'.$this->user->profile_image)
    : null,
            'patients_count' => $this->appointments_count,
            'average_rating' => round(
                $this->reviews_avg_rating ?? 0,
                1
            ),
            'schedules' => $this->schedules->map(function ($schedule) {
                return [
                    'date' => $schedule->date,
                    'day' => Carbon::parse($schedule->date)
                        ->locale('ar')
                        ->translatedFormat('l'),
                    'start_time' => $schedule->start_time,
                    'end_time' => $schedule->end_time,
                ];

            }),
        ];

    }
}
