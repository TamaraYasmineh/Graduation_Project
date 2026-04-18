<?php
namespace App\Services;

use App\Models\Schedule;
use App\Models\Appointments;
use Carbon\Carbon;

class BookingService
{
    public function getFirstAvailableSlot($doctorId)
    {
        
        for ($i = 0; $i < 14; $i++) {

            $date = Carbon::today()->addDays($i);
            $dayName = $date->format('l');

            $schedule = Schedule::where('doctor_id', $doctorId)
            ->where('date', $date->toDateString())
                ->first();

            if (!$schedule) {
                continue; 
            }

            $start = Carbon::parse($schedule->start_time);
            $end = Carbon::parse($schedule->end_time);
            $duration = $schedule->slot_duration;

            while ($start < $end) {

                $slotStart = $start->format('H:i');
                $slotEnd = $start->copy()->addMinutes($duration)->format('H:i');

                $exists = Appointments::where('doctor_id', $doctorId)
                    ->where('date', $date->toDateString()) 
                    ->where('start_time', $slotStart)
                    ->exists();

                if (!$exists) {
                    return [
                        'date' => $date->toDateString(),
                        'start_time' => $slotStart,
                        'end_time' => $slotEnd,
                    ];
                }

                $start->addMinutes($duration);
            }
        }

        return null; 
    }
    public function bookAppointment($user, $doctorId, $date)
{
    $existsThisMonth = \App\Models\Appointments::where('patient_id', $user->id)
        ->whereMonth('date', \Carbon\Carbon::parse($date)->month)
        ->whereYear('date', \Carbon\Carbon::parse($date)->year)
        ->exists();

    if ($existsThisMonth) {
        return [
            'error' => true,
            'message' => 'يمكنك الحجز مرة واحدة فقط في الشهر'
        ];
    }

    $schedule = \App\Models\Schedule::where('doctor_id', $doctorId)
        ->where('date', $date)
        ->first();

    if (!$schedule) {
        return [
            'error' => true,
            'message' => 'لا يوجد دوام لهذا التاريخ'
        ];
    }

    $start = Carbon::parse($schedule->start_time);
    $end = Carbon::parse($schedule->end_time);
    $duration = $schedule->slot_duration;

    while ($start < $end) {

        $slotStart = $start->format('H:i');
        $slotEnd = $start->copy()->addMinutes($duration)->format('H:i');

        $exists = \App\Models\Appointments::where('doctor_id', $doctorId)
            ->where('date', $date)
            ->where('start_time', $slotStart)
            ->exists();

        if (!$exists) {

            $appointment = \App\Models\Appointments::create([
                'doctor_id' => $doctorId,
                'patient_id' => $user->id,
                'date' => $date,
                'start_time' => $slotStart,
                'end_time' => $slotEnd,
                'status' => 'confirmed',
            ]);

            return [
                'error' => false,
                'data' => $appointment
            ];
        }

        $start->addMinutes($duration);
    }

    return [
        'error' => true,
        'message' => 'لا يوجد مواعيد متاحة في هذا اليوم'
    ];
}
}
