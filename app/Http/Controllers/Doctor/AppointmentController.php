<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\GetAvailableAppointmentsRequest;
use App\Http\Resources\AppointmentResource;
use App\Http\Resources\AvailableAppointmentResource;
use App\Models\Appointment;
use App\Models\Schedule;
use App\Services\AvailableAppointmentService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends BaseController
{
    public function getAppointments(Request $request)
    {
        $type = $request->query('type', 'daily');

        $user = Auth::user();
        $query = Appointment::with(['patient', 'doctor.user']);

        if ($user->hasRole('super_doctor')) {

        } else {
            $doctorId = $user->doctor->id;
            $query->where('doctor_id', $doctorId);
        }
        if ($type === 'daily') {

            $query->whereDate('date', Carbon::today());

        } elseif ($type === 'weekly') {

            $query->whereBetween('date', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ]);

        } elseif ($type === 'monthly') {

            $query->whereMonth('date', Carbon::now()->month)
                  ->whereYear('date', Carbon::now()->year);
        }
       $appointments = $query->orderBy('date')
                     ->orderBy('start_time')
                     ->get();

return response()->json([
    'success' => true,
    'type' => $type,
   // 'appointments' => AppointmentResource::collection($appointments)
]);
    }


    public function getAvailableAppointments(
        GetAvailableAppointmentsRequest $request,
        AvailableAppointmentService $service
    ) {
        $result = $service->getAvailable(
            $request->doctor_id,
            $request->date
        );

        if (!$result['success']) {
            return $this->sendError(
                $result['message'],
                [],
                400
            );
        }

        return $this->sendResponse(
            new AvailableAppointmentResource($result),
            'Available appointments fetched successfully'
        );
    }
    }




