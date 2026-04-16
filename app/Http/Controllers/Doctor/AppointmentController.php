<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointments;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function getAppointments(Request $request)
    {
        $type = $request->query('type', 'daily');

        $user = Auth::user();
        $query = Appointments::with(['patient', 'doctor.user']);

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
    }

