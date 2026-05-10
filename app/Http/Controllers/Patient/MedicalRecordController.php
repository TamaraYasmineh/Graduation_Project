<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\BookAppointmentRequest;
use App\Http\Requests\StoreMedicalRecordRequest;
use App\Http\Resources\BookAppointmentResource;
use App\Http\Resources\MedicalRecordResource;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\User;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use App\Models\Payment;
use App\Services\PaymeraService;

class MedicalRecordController extends BaseController
{
    public function storemedicalRecord(
        StoreMedicalRecordRequest $request,
        BookingService $bookingService,
        PaymeraService $paymera
    ) {
        return DB::transaction(function () use ($request, $bookingService, $paymera) {

            $user = $request->user();

            if ($user->medicalRecord) {
                return $this->sendError('Medical record already exists', [], 400);
            }

            $superDoctorUser = User::role('super_doctor')->first();

            $doctor = Doctor::query()->where('user_id', $superDoctorUser->id)->first();
            $slot = $bookingService->getFirstAvailableSlot($doctor->id);

            if (!$slot) {
                return $this->sendError('لا يوجد مواعيد متاحة حالياً', [], 200);
            }

            $record = MedicalRecord::create([
                'patient_id' => $user->id,
                ...$request->validated()
            ]);

            //  توليد وحفظ QR Code فور إنشاء السجل
            $record->generateAndSaveQrCode();

            $appointment = Appointment::create([
                'doctor_id' => $doctor->id,
                'patient_id' => $user->id,
                'date' => $slot['date'],
                'start_time' => $slot['start_time'],
                'end_time' => $slot['end_time'],
                'status' => 'pending',
                'session_type' => $request->session_type
            ]);
            // 3. أنشئ Order مرتبط بالموعد
            $order = Order::create([
                'amount' => $request->amount,
                'status' => 'pending',
                'appointment_id' => $appointment->id,
                'user_id' => $user->id
            ]);
            // 4. أنشئ Payment

            $payment = Payment::create([
                'order_id' => $order->id,
                'payment_id' => uniqid(),
                'amount' => $order->amount
            ]);
            // 5. أرسل طلب الدفع لـ Paymera
            $base = config('services.payment_base_url');
            $response = $paymera->createPayment([
                "lang" => $request->lang ?? 'ar',
                "terminalId" => config('services.paymera.terminal_id'),
                "amount" => $order->amount,
                "callbackURL" => $base . "/api/payment/callback/" . $order->id,
                "triggerURL" => $base . "/api/payment/trigger/" . $order->id,
                "notes" => "Appointment #" . $appointment->id
            ]);

            if (!$response || !isset($response['ErrorCode']) || $response['ErrorCode'] != 0) {
                return $this->sendError('فشل في إنشاء رابط الدفع', [], 500);
            }

            $payment->update([
                'payment_id' => $response['Data']['paymentId']
            ]);
            // 6. أرجع رابط الدفع للمريض

            return $this->sendResponse([
                'medical_record' => $record,
                'qr_code_url'    => $record->getQrCodeUrl(),
                'appointment' => $appointment,
                'payment_url' => $response['Data']['url'],
                'payment_id' => $payment->payment_id
            ], 'تم الحجز، يرجى إتمام الدفع');
        });
    }


    public function bookAppointment(
        BookAppointmentRequest $request,
        BookingService $service,
        PaymeraService $paymera
    ) {
        $user = $request->user();
        $result = $service->book(
            $request->user(),
            $request->doctor_id,
            $request->date,
            $request->start_time
        );

        if (!$result['success']) {
            return $this->sendError($result['message']);
        }
        $appointment = $result['data'];
        // إنشاء Order و Payment
        $order = Order::create([
            'amount' => $request->amount,
            'status' => 'pending',
            'appointment_id' => $appointment->id,
            'user_id' => $user->id
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_id' => uniqid(),
            'amount' => $order->amount
        ]);
        // إرسال طلب الدفع لـ Paymera
        $base = config('services.payment_base_url');
        $response = $paymera->createPayment([
            "lang" => $request->lang ?? 'ar',
            "terminalId" => config('services.paymera.terminal_id'),
            "amount" => $order->amount,
            "callbackURL" => $base . "/api/payment/callback/" . $order->id,
            "triggerURL" => $base . "/api/payment/trigger/" . $order->id,
            "notes" => "Appointment #" . $appointment->id
        ]);
        if (!$response || !isset($response['ErrorCode']) || $response['ErrorCode'] != 0) {
            return $this->sendError('فشل في إنشاء رابط الدفع', [], 500);
        }

        $payment->update([
            'payment_id' => $response['Data']['paymentId']
        ]);
        return $this->sendResponse([
            'appointment' => $appointment,
            'payment_url' => $response['Data']['url'],
            'payment_id' => $payment->payment_id
        ], 'تم الحجز، يرجى إتمام الدفع');
    }
    public function myAppointments(Request $request)
    {
        $user = $request->user();

        $appointments = Appointment::with(['doctor.user'])
            ->where('patient_id', $user->id)
            ->orderBy('date', 'desc')
            ->get();

        return $this->sendResponse($appointments, 'My appointments');
    }

    /**
     * جلب السجل الطبي مع رابط QR Code
     * GET /api/patient/medical-record/qr
     */
    public function showWithQr(Request $request)
    {
        $user = $request->user();

        $record = MedicalRecord::query()->where('patient_id', $user->id)
            ->with(['patient.patient'])
            ->first();

        if (!$record) {
            return $this->sendError('لا يوجد سجل طبي لهذا المريض', [], 404);
        }

        $appUrl  = rtrim(config('services.public_url'), '/');
        $token   = urlencode($record->getScanToken());

        return $this->sendResponse([
            'medical_record' => [
                'id'                 => $record->id,
                'patient_name'       => $record->patient->name ?? '',
                'blood_type'         => $record->blood_type,
                'blood_pressure'     => $record->blood_pressure,
                'height'             => $record->height,
                'weight'             => $record->weight,
                'is_smoker'          => $record->is_smoker ? 'نعم' : 'لا',
                'chronic_diseases'   => $record->chronic_diseases,
                'allergies'          => $record->allergies,
                'medications'        => $record->medications,
                'surgeries'          => $record->surgeries,
                'family_history'     => $record->family_history,
                'marital_status'     => $record->marital_status,
                'number_of_children' => $record->number_of_children,
                'notes'              => $record->notes,
                'email' => $record->patient->email ?? '',
                'phone' => $record->patient->phone ?? '',
                'gender' => $record->patient->gender ?? '',

                'profile_image' => $record->patient->profile_image
                    ? asset('storage/' . $record->patient->profile_image)
                    : null,
                'date_of_birth' =>
                optional($record->patient->patient)->date_of_birth,

                'age' =>
                optional($record->patient->patient)->date_of_birth
                    ? Carbon::parse(
                        $record->patient->patient->date_of_birth
                    )->age
                    : null,

                'country' =>
                optional($record->patient->patient)->country,

                'city' =>
                optional($record->patient->patient)->city,

                'address' =>
                optional($record->patient->patient)->country .
                    ' - ' .
                    optional($record->patient->patient)->city,

                'emergency_contact' =>
                optional($record->patient->patient)->emergency_contact,

            ],
            'qr_code_url' => $record->getQrCodeUrl(),
            'scan_url'    => $appUrl . '/scan?token=' . $token,
        ], 'تم جلب السجل الطبي مع QR Code');
    }

    /**
     * عند السكان الحقيقي بالهاتف
     * GET /api/medical-records/scan?token=XXXXX
     * ← هذا الرابط يُفتح تلقائياً عند مسح QR بالهاتف
     */
    public function scan(Request $request)
    {
        try {
            $decrypted = decrypt($request->token);
            [$patientId, $recordId] = explode('|', $decrypted);

            $record = MedicalRecord::query()->where('id', $recordId)
                ->where('patient_id', $patientId)
                ->with(['patient.patient'])
                ->first();

            if (!$record) {
                return $this->sendError('السجل الطبي غير موجود', [], 404);
            }

            return $this->sendResponse([
                'patient_name'       => $record->patient->name ?? '',
                'blood_type'         => $record->blood_type,
                'blood_pressure'     => $record->blood_pressure,
                'height'             => $record->height . ' سم',
                'weight'             => $record->weight . ' كغ',
                'is_smoker'          => $record->is_smoker ? 'نعم' : 'لا',
                'chronic_diseases'   => $record->chronic_diseases,
                'allergies'          => $record->allergies,
                'medications'        => $record->medications,
                'surgeries'          => $record->surgeries,
                'family_history'     => $record->family_history,
                'marital_status'     => $record->marital_status,
                'number_of_children' => $record->number_of_children,
                'notes'              => $record->notes,
                   'email' => $record->patient->email ?? '',
                'phone' => $record->patient->phone ?? '',
                'gender' => $record->patient->gender ?? '',

                'profile_image' => $record->patient->profile_image
                    ? asset('storage/' . $record->patient->profile_image)
                    : null,
                'date_of_birth' =>
                optional($record->patient->patient)->date_of_birth,

                'age' =>
                optional($record->patient->patient)->date_of_birth
                    ? Carbon::parse(
                        $record->patient->patient->date_of_birth
                    )->age
                    : null,

                'country' =>
                optional($record->patient->patient)->country,

                'city' =>
                optional($record->patient->patient)->city,

                'address' =>
                optional($record->patient->patient)->country .
                    ' - ' .
                    optional($record->patient->patient)->city,

                'emergency_contact' =>
                optional($record->patient->patient)->emergency_contact,

            ], 'بيانات السجل الطبي');
        } catch (\Exception $e) {
            return $this->sendError('QR Code غير صالح أو تالف', [], 400);
        }
    }

    /**
     * صفحة HTML جميلة تظهر عند السكان بالهاتف
     * GET /scan?token=XXXXX  (web route)
     */
    public function scanWeb(Request $request)
    {
        try {
            $decrypted = decrypt($request->token);
            [$patientId, $recordId] = explode('|', $decrypted);

            $record = MedicalRecord::query()->where('id', $recordId)
                ->where('patient_id', $patientId)
                ->with(['patient.patient'])
                ->firstOrFail();

            return view('medical-records.scan-result', compact('record'));
        } catch (\Exception $e) {
            abort(400, 'QR Code غير صالح');
        }
    }
}
