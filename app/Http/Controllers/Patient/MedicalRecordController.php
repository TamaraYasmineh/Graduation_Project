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

            // $doctor = Doctor::where('user_id',$superDoctorUser->id)->first();
            $doctor = Doctor::query()->where('user_id', $superDoctorUser->id)->first();
            $slot = $bookingService->getFirstAvailableSlot($doctor->id);

            if (!$slot) {
                return $this->sendError('لا يوجد مواعيد متاحة حالياً', [], 200);
            }

            $record = MedicalRecord::create([
                'patient_id' => $user->id,
                ...$request->validated()
            ]);

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
}
