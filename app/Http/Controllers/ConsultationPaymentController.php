<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateConsultationPaymentRequest;
use App\Models\Consultant;
use App\Models\ConsultationOrder;
use App\Models\ConsultationPayment;
use App\Models\ConsultationRequest;
use App\Models\Doctor;
use App\Models\ExternalDoctor;
use App\Services\PaymeraService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsultationPaymentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | 1. Create Consultation Payment
    |--------------------------------------------------------------------------
    */

    public function create(
        CreateConsultationPaymentRequest $request,
        PaymeraService $paymera
    ) {
        $request->validate([
            'consultant_id' => 'required|exists:consultants,id',

            'lang' => 'required|in:ar,en',
        ]);

        DB::beginTransaction();

        try {

            $consultant =
                Consultant::findOrFail(
                    $request->consultant_id
                );

            if (! $consultant->is_active) {

                return response()->json([
                    'error' => 'Consultant is not active',
                ], 422);
            }

            /*
            |--------------------------------------------------------------------------
            | السعر يؤخذ من الطبيب مباشرة
            |--------------------------------------------------------------------------
            */

            $amount =
                (int) $consultant
                    ->consultation_fee;

            /*
            |--------------------------------------------------------------------------
            | Consultation Request
            |--------------------------------------------------------------------------
            */

            $consultation =
                ConsultationRequest::create([
                    'user_id' => auth()->id(),

                    'consultant_id' => $consultant->id,

                    'amount' => $amount,

                    'status' => 'P',
                ]);

            /*
            |--------------------------------------------------------------------------
            | Consultation Order
            |--------------------------------------------------------------------------
            */

            $order =
                ConsultationOrder::create([
                    'user_id' => auth()->id(),

                    'consultation_request_id' => $consultation->id,

                    'amount' => $amount,

                    'status' => 'pending',
                ]);

            /*
            |--------------------------------------------------------------------------
            | ربط الطلب بالـ Order
            |--------------------------------------------------------------------------
            */

            // $consultation->update([
            //     'consultation_order_id' =>
            //         $order->id
            // ]);

            /*
            |--------------------------------------------------------------------------
            | Consultation Payment
            |--------------------------------------------------------------------------
            */

            $payment =
                ConsultationPayment::create([
                    'consultation_order_id' => $order->id,

                    'payment_id' => uniqid(),

                    'amount' => $amount,

                    'status' => 'P',
                ]);

            $base =
                config(
                    'services.payment_base_url'
                );

            /*
            |--------------------------------------------------------------------------
            | إرسال Paymera
            |--------------------------------------------------------------------------
            */

            $response =
                $paymera->createPayment([

                    'lang' => $request->lang,

                    'terminalId' => config(
                        'services.paymera.terminal_id'
                    ),

                    'amount' => $amount,

                    'callbackURL' => $base.
                        '/api/consultation-payment/callback/'.
                        $order->id,

                    'triggerURL' => $base.
                        '/api/consultation-payment/trigger/'.
                        $order->id,

                    'notes' => 'Consultation #'.
                        $consultation->id,
                ]);

            if (! $response) {

                DB::rollBack();

                return response()->json([
                    'error' => 'No response from Paymera',
                ], 500);
            }

            if (! isset($response['ErrorCode'])) {

                DB::rollBack();

                return response()->json([
                    'error' => 'Invalid response structure',

                    'response' => $response,
                ], 500);
            }

            if ($response['ErrorCode'] != 0) {

                DB::rollBack();

                return response()->json(
                    $response,
                    400
                );
            }

            if (! isset($response['Data'])) {

                DB::rollBack();

                return response()->json([
                    'error' => 'Missing Data field',

                    'response' => $response,
                ], 500);
            }

            $payment->update([
                'payment_id' => $response['Data']['paymentId'],
            ]);

            DB::commit();

            return response()->json([

                'consultation_id' => $consultation->id,

                'payment_url' => $response['Data']['url'],

                'payment_id' => $payment->payment_id,
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Trigger
    |--------------------------------------------------------------------------
    */

    public function trigger($orderId, PaymeraService $paymera)
    {
        \Log::info('TRIGGER HIT');
        \Log::info(
            "Consultation Trigger received for order: $orderId"
        );

        $order = ConsultationOrder::findOrFail($orderId);

        $payment = $order->payment;

        if (! $payment || ! $payment->payment_id) {
            return response()->json([
                'error' => 'Payment not found',
            ], 404);
        }

        $status = null;
        $response = null;

        for ($i = 0; $i < 5; $i++) {

            $response = $paymera->getStatus($payment->payment_id);

            if (! $response || ! isset($response['ErrorCode'])) {
                continue;
            }

            if ($response['ErrorCode'] != 0) {
                continue;
            }

            if (! isset($response['Data']['status'])) {
                continue;
            }

            $status = $response['Data']['status'];

            if ($status != 'P') {
                break;
            }

            sleep(2);
        }

        if ($status == 'A') {
            $payment->markSuccess(
                $response['Data']['rrn'] ?? null,
                $response
            );
        } elseif ($status == 'F') {
            $payment->markFailed(
                $response
            );
        } elseif ($status == 'C') {
            $payment->markCanceled(
                $response
            );
        }

        return response()->json(['ok' => true]);
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Callback
    |--------------------------------------------------------------------------
    */

    public function callback(
        $orderId,
        PaymeraService $paymera
    ) {
        $order =
            ConsultationOrder::findOrFail(
                $orderId
            );

        $payment =
            $order->payment;

        if (
            ! $payment ||
            ! $payment->payment_id
        ) {
            return response()->json([
                'error' => 'Payment not found',
            ], 404);
        }

        $status = null;
        $response = null;

        for ($i = 0; $i < 5; $i++) {

            $response = $paymera->getStatus($payment->payment_id);

            if (! $response || ! isset($response['ErrorCode'])) {

                \Log::error(
                    'Invalid Paymera response',
                    [
                        'response' => $response,
                    ]
                );

                continue;
            }

            if ($response['ErrorCode'] != 0) {

                \Log::error(
                    'Paymera Error',
                    $response
                );

                continue;
            }

            if (! isset($response['Data']['status'])) {
                continue;
            }

            $status =
                $response['Data']['status'];

            if ($status != 'P') {
                break;
            }

            sleep(2);
        }

        if (! $status) {

            return response()->json([
                'error' => 'No valid status received',

                'response' => $response,
            ], 500);
        }

        if ($status == 'A') {

            $payment->markSuccess(
                $response['Data']['rrn'] ?? null,
                $response
            );
        } elseif ($status == 'F') {

            $payment->markFailed(
                $response
            );
        } elseif ($status == 'C') {

            $payment->markCanceled(
                $response
            );
        }

        return response()->json([
            'status' => $status,
        ]);
    }

    // /*
    // |--------------------------------------------------------------------------
    // | Shared Status Logic
    // |--------------------------------------------------------------------------
    // */

    // private function processStatus(
    //     $orderId,
    //     PaymeraService $paymera
    // ) {
    //     $order =
    //         ConsultationOrder::findOrFail(
    //             $orderId
    //         );

    //     $payment =
    //         $order->payment;

    //     if (
    //         ! $payment ||
    //         ! $payment->payment_id
    //     ) {
    //         return response()->json([
    //             'error' =>
    //             'Payment not found'
    //         ], 404);
    //     }

    //     $status = null;
    //     $response = null;

    //     for ($i = 0; $i < 5; $i++) {

    //         $response =
    //             $paymera->getStatus(
    //                 $payment->payment_id
    //             );

    //         if (
    //             ! $response ||
    //             ! isset(
    //                 $response['ErrorCode']
    //             )
    //         ) {
    //             continue;
    //         }

    //         if (
    //             $response['ErrorCode']
    //             != 0
    //         ) {
    //             continue;
    //         }

    //         if (
    //             ! isset(
    //                 $response['Data']['status']
    //             )
    //         ) {
    //             continue;
    //         }

    //         $status =
    //             $response['Data']['status'];

    //         if ($status != 'P') {
    //             break;
    //         }

    //         sleep(2);
    //     }

    //     if ($status == 'A') {

    //         $payment->markSuccess(
    //             $response['Data']['rrn']
    //                 ?? null,
    //             $response
    //         );

    //     } elseif ($status == 'F') {

    //         $payment->markFailed(
    //             $response
    //         );

    //     } elseif ($status == 'C') {

    //         $payment->markCanceled(
    //             $response
    //         );
    //     }

    //     return response()->json([
    //         'status' => $status
    //     ]);
    // }

    /*
    |--------------------------------------------------------------------------
    | 4. Cancel Consultation Payment
    |--------------------------------------------------------------------------
    */

    public function cancel(
        $paymentId,
        PaymeraService $paymera
    ) {
        $payment =
            ConsultationPayment::query()
                ->where(
                    'payment_id',
                    $paymentId
                )
                ->firstOrFail();

        $response =
            $paymera->cancel(
                $paymentId
            );

        if (
            isset($response['ErrorCode'])
            &&
            $response['ErrorCode'] == 0
        ) {

            $payment->markCanceled(
                $response
            );
        }

        return response()->json(
            $response
        );
    }
    /*
    |--------------------------------------------------------------------------
    | 5. WhatsApp Link
    |--------------------------------------------------------------------------
    */

    public function whatsapp($consultationId)
    {
        $consultation = ConsultationRequest::with('consultant', 'order.payment', 'user')
            ->findOrFail($consultationId);

        $payment = $consultation->order?->payment;

        if (
            $consultation->status !== 'A' ||
            ! $payment ||
            $payment->status !== 'A'
        ) {
            return response()->json([
                'message' => 'Consultation not paid',
            ], 403);
        }

        // الرقم
        $number = preg_replace('/[^0-9]/', '', $consultation->consultant?->whatsapp_number);

        if (str_starts_with($number, '0')) {
            $number = '963'.substr($number, 1);
        }

        $consultant = $consultation->consultant;

        $doctorName = null;

        if ($consultant->consultable_type === Doctor::class) {
            $doctorName = $consultant->consultable?->user?->name;
        }

        if ($consultant->consultable_type === ExternalDoctor::class) {
            $doctorName = $consultant->consultable?->name;
        }

        $doctorName = $doctorName ?? 'الدكتور';
        $patientName = $consultation->user?->name ?? 'المريض';

        $message =
            "مرحباً دكتور $doctorName...\n"
            ."أنا المريض: *$patientName*\n"
            ."أرغب في طلب استشارة طبية بخصوص حالتي الصحية.\n"
            ."أرجو التكرم بالرد في أقرب وقت ممكن.\n"
            .'شكرًا جزيلًا لوقتكم واهتمامكم ';

        $encodedMessage = urlencode($message);

        return response()->json([
            'whatsapp_url' => "https://wa.me/$number?text=$encodedMessage",
        ]);
    }

    public function getPaymentStatus(
        $paymentId,
        PaymeraService $paymera
    ) {
        $payment =
            ConsultationPayment::query()
                ->where(
                    'payment_id',
                    $paymentId
                )
                ->first();

        $response =
            $paymera->getStatus(
                $paymentId
            );

        if (
            ! $response ||
            ! isset(
                $response['ErrorCode']
            )
        ) {
            return response()->json([
                'message' => 'لا يوجد رد من بيميرا',
            ], 500);
        }

        if (
            $response['ErrorCode'] != 0
        ) {
            return response()->json([
                'message' => $response['ErrorMessage']
                    ?? 'خطأ في الدفع',
            ], 400);
        }

        if (
            ! isset(
                $response['Data']
            )
        ) {
            return response()->json([
                'message' => 'Missing Data in response',
            ], 500);
        }

        return response()->json([

            'status' => $response['Data']['status'],

            'amount' => $response['Data']['amount'],

            'paid_at' => $payment?->paid_at,

            'consultation_order_id' => $payment?->consultation_order_id,

        ]);
    }
}
