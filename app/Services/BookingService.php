<?php
namespace App\Services;

use App\Models\Schedule;
use App\Models\Appointment;
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

                $exists = Appointment::where('doctor_id', $doctorId)
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
    public function book($user, $doctorId, $date, $startTime)
    {
        // ✅ جيب الدوام
        $schedule = Schedule::where('doctor_id', $doctorId)
            ->where('date', $date)
            ->first();

        if (!$schedule) {
            return ['success' => false, 'message' => 'لا يوجد دوام'];
        }

        $duration = $schedule->slot_duration;

        // 🔥 احسب end_time من الباك
        $endTime = Carbon::parse($startTime)
            ->addMinutes($duration)
            ->format('H:i');

        // ❗ تحقق إذا محجوز
        $exists = Appointment::where('doctor_id', $doctorId)
            ->where('date', $date)
            ->where('start_time', $startTime)
            ->exists();

        if ($exists) {
            return ['success' => false, 'message' => 'هذا الموعد تم حجزه'];
        }

        // ❗ شرط: مرة بالشهر
        $alreadyBooked = Appointment::where('patient_id', $user->id)
            ->whereMonth('date', Carbon::parse($date)->month)
            ->exists();

        if ($alreadyBooked) {
            return ['success' => false, 'message' => 'لديك حجز مسبق هذا الشهر'];
        }

        // ✅ إنشاء الحجز
        $appointment = Appointment::create([
            'doctor_id' => $doctorId,
            'patient_id' => $user->id,
            'date' => $date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => 'pending'
        ]);

        return [
            'success' => true,
            'data' => $appointment
        ];
    }
}
