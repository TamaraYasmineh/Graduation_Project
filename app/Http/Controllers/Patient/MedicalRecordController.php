<?php

namespace App\Http\Controllers\Patient;
use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use App\Models\User;
use App\Models\Appointments;
use App\Models\Doctor;
use Carbon\Carbon;
use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Services\BookingService;
use App\Http\Requests\StoreMedicalRecordRequest;
use App\Http\Resources\MedicalRecordResource;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\BookAppointmentRequest;
use App\Http\Resources\BookAppointmentResource;
class MedicalRecordController extends BaseController
{
    public function storemedicalRecord(
        StoreMedicalRecordRequest $request,
        BookingService $bookingService
    ) {
        return DB::transaction(function () use ($request, $bookingService) {

            $user = $request->user();

            if ($user->medicalRecord) {
                return $this->sendError('Medical record already exists', [], 400);
            }

            $superDoctorUser = User::role('super_doctor')->first();
            $doctor = Doctor::where('user_id', $superDoctorUser->id)->first();

            $slot = $bookingService->getFirstAvailableSlot($doctor->id);

            if (!$slot) {
                return $this->sendError('لا يوجد مواعيد متاحة حالياً', [], 200);
            }

            $record = MedicalRecord::create([
                'patient_id' => $user->id,
                ...$request->validated()
            ]);

            $appointment = Appointments::create([
                'doctor_id' => $doctor->id,
                'patient_id' => $user->id,
                'date' => $slot['date'],
                'start_time' => $slot['start_time'],
                'end_time' => $slot['end_time'],
                'status' => 'confirmed',
                'session_type' => $request->session_type
            ]);

            return $this->sendResponse([
                'medical_record' => $record,
                'appointment' => $appointment
            ], 'Created + booked');
        });
    }
   
    
    public function bookAppointment(
        BookAppointmentRequest $request,
        BookingService $bookingService
    ) {
        $user = $request->user();
        if (!$user->medicalRecord) {
            return $this->sendError('يجب إنشاء سجل طبي أولاً', [], 400);
        }
    
        $result = $bookingService->bookAppointment(
            $user,
            $request->doctor_id,
            $request->date
        );
    
        if ($result['error']) {
            return $this->sendError($result['message'], [], 200);
        }
    
        return $this->sendResponse(
            new BookAppointmentResource($result['data']),
            'تم الحجز بنجاح'
        );
    }
}
