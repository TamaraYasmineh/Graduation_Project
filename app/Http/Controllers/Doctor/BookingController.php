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
class BookingController extends BaseController
{
    public function storeSchedule(StoreScheduleRequest $request)
    {
        $schedule = Schedule::create($request->validated());

        return $this->sendResponse(
            $schedule,
            'Schedule created successfully'
        );
    }

    public function updateSchedule($id, StoreScheduleRequest $request)
{
    $schedule = Schedule::find($id);

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
    $request->validate([
        'doctor_id' => 'required|exists:doctors,id',
        'date' => 'required|date'
    ]);

    $doctorId = $request->doctor_id;
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

    while ($start < $end) {

        $slotStart = $start->format('H:i');
        $slotEnd = $start->copy()->addMinutes($duration)->format('H:i');

        $exists = Appointments::where('doctor_id', $doctorId)
            ->where('date', $date)
            ->where('start_time', $slotStart)
            ->exists();

        if (!$exists) {
            $slots[] = [
                'start_time' => $slotStart,
                'end_time' => $slotEnd,
            ];
        }

        $start->addMinutes($duration);
    }

    return $this->sendResponse($slots, 'Available slots');
}
}