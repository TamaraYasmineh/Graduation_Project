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
        // return [
        //     'doctor_id' => $this->id,
        //     'name' => $this->user->name,
        //     'email' => $this->user->email,
        //     'phone' => $this->user->phone ?? null,
        //     'specialization' => $this->specialization,
        //     'experience_years' => $this->experience_years,
        //     'bio' => $this->bio,
        //     'image' => $this->image ?? null,
        //     'schedules' => $this->schedules->map(function ($schedule) {
        //         return [
        //             'date' => $schedule->date,
        //             'day' => Carbon::parse($schedule->date)->locale('ar')->translatedFormat('l'),
        //             'start_time' => $schedule->start_time,
        //             'end_time' => $schedule->end_time,
        //         ];
        //     })
        // ];
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
    ? asset('storage/' . $this->user->profile_image) 
    : null,
    'patients_count' => $this->appointments_count,
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

