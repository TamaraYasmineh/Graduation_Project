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

        //  تحميل العلاقات
        $query = Appointments::with(['patient', 'doctor.user']);

        //  الصلاحيات
        if ($user->hasRole('super_doctor')) {

            //  السوبر دكتور يرى كل المواعيد (بما فيها مواعيده)
            // لا نضيف أي فلتر

        } else {

            //  الطبيب يرى مواعيده فقط
            $doctorId = $user->doctor->id;
            $query->where('doctor_id', $doctorId);
        }

        // الفلترة حسب النوع
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

        //  ترتيب النتائج
       $appointments = $query->orderBy('date')
                     ->orderBy('start_time')
                     ->get();

return response()->json([
    'success' => true,
    'type' => $type,
    'appointments' => AppointmentResource::collection($appointments)
]);
    }
    }

