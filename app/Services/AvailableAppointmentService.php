<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Schedule;
use Carbon\Carbon;

class AvailableAppointmentService
{
    public function getAvailable($doctorId, $date)
    {
        $schedule = Schedule::where('doctor_id', $doctorId)
            ->where('date', $date)
            ->first();

        if (!$schedule) {
            return [
                'success' => false,
                'message' => 'لا يوجد دوام لهذا اليوم'
            ];
        }

        $start = Carbon::parse($schedule->start_time);
        $end = Carbon::parse($schedule->end_time);
        $duration = $schedule->slot_duration;

        $availableSlots = [];

        while ($start < $end) {

            $slotStart = $start->format('H:i');
            $slotEnd = $start->copy()->addMinutes($duration)->format('H:i');

            $exists = Appointment::where('doctor_id', $doctorId)
                ->where('date', $date)
                ->where('start_time', $slotStart)
                ->exists();

            if (!$exists) {
                $availableSlots[] = [
                    'start_time' => $slotStart,
                    'end_time' => $slotEnd
                ];
            }

            $start->addMinutes($duration);
        }

        return [
            'success' => true,
            'date' => $date,
            'day' => Carbon::parse($date)->translatedFormat('l'),
            'slots' => $availableSlots
        ];
    }
}
