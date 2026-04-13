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
                throw new \Exception('No available slots');
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
            ]);
    
            return $this->sendResponse([
                'medical_record' => $record,
                'appointment' => $appointment
            ], 'Created + booked');
        });
    }
}
