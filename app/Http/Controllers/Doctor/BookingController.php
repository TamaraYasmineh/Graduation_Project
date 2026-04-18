<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\BookingService;
use App\Http\Requests\StoreScheduleRequest;
use App\Models\Schedule;
use App\Models\Appointments;
use Carbon\Carbon;
use App\Http\Resources\ScheduleResource;
use Illuminate\Support\Facades\Auth;
use App\Models\Doctor;
use App\Http\Resources\DoctorScheduleResource;
class BookingController extends BaseController

{
    public function storeSchedule(StoreScheduleRequest $request)
    {
        $user = Auth::user();

        if (!$user) {
            return $this->sendError('Unauthenticated', [], 401);
        }

        if (!$user->doctor()->exists()) {
            return $this->sendError('هذا المستخدم ليس دكتور', [], 403);
        }

        $doctor = $user->doctor;

        $data = $request->validated();
        $data['doctor_id'] = $doctor->id;

        $schedule = Schedule::create($data);

        return $this->sendResponse(
            $schedule,
            'Schedule created successfully'
        );
    }

    public function updateSchedule($id, StoreScheduleRequest $request)
    {
        $user = Auth::user();
        $doctor = $user->doctor;

        $schedule = Schedule::where('id', $id)
            ->where('doctor_id', $doctor->id)
            ->first();

        if (!$schedule) {
            return $this->sendError('Schedule not found', [], 404);
        }

        $schedule->update($request->validated());

        return $this->sendResponse(
            $schedule,
            'Schedule updated successfully'
        );
    }
    public function deleteSchedule($id)
    {
        $schedule = Schedule::find($id);

        if (!$schedule) {
            return $this->sendError('Schedule not found', [], 404);
        }

        $schedule->delete();

        return $this->sendResponse(
            null,
            'Schedule deleted successfully'
        );
    }

    public function getAvailableSlots(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->doctor()->exists()) {
            return $this->sendError('Unauthorized', [], 403);
        }

        $request->validate([
            'date' => 'required|date'
        ]);

        $doctorId = $user->doctor->id;
        $date = $request->date;

        $schedule = Schedule::where('doctor_id', $doctorId)
            ->where('date', $date)
            ->first();

        if (!$schedule) {
            return $this->sendError('No schedule for this date', [], 404);
        }

        $start = Carbon::parse($schedule->start_time);
        $end = Carbon::parse($schedule->end_time);
        $duration = $schedule->slot_duration;

        $slots = [];

        while ($start->copy()->addMinutes($duration) <= $end) {

            $slotStart = $start->format('H:i');
            $slotEnd = $start->copy()->addMinutes($duration)->format('H:i');

            $isBooked = Appointments::where('doctor_id', $doctorId)
                ->where('date', $date)
                ->where('start_time', $slotStart)
                ->exists();

            if (!$isBooked) {
                $slots[] = [
                    'start_time' => $slotStart,
                    'end_time' => $slotEnd,
                ];
            }

            $start->addMinutes($duration);
        }

        return $this->sendResponse($slots, 'Available slots');
    }
    public function getMySchedules()
    {
        $user = Auth::user();

        if (!$user || !$user->doctor()->exists()) {
            return $this->sendError('Unauthorized', [], 403);
        }

        $doctorId = $user->doctor->id;

        $schedules = Schedule::where('doctor_id', $doctorId)
            ->orderBy('date', 'asc')
            ->get();

        return $this->sendResponse(
            $schedules,
            'Doctor schedules'
        );
    }
    public function getAllSchedules()
    {
        $schedules = Schedule::with('doctor.user')
            ->orderBy('date')
            ->get();

        return $this->sendResponse(
            ScheduleResource::collection($schedules),
            'All schedules'
        );
    }
    public function getDoctorsWithSchedules()
{
    $doctors = Doctor::with(['user', 'schedules'])->get();

    return $this->sendResponse(
        DoctorScheduleResource::collection($doctors),
        'Doctors with schedules'
    );
}
}
