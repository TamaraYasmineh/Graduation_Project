<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Services\PaymeraService;
use Illuminate\Http\Request;

class PaymentController extends BaseController
{
    // =========================
    // 1. Create Payment
    // =========================
    public function create(Request $request, PaymeraService $paymera)
    {
        $request->validate([
            'amount' => 'required|integer|min:1',
            'lang' => 'required|in:ar,en'
        ]);

        // إنشاء Order
        $order = Order::create([
            'amount' => $request->amount,
            'status' => 'pending'
        ]);

        // إنشاء Payment
        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_id' => uniqid(),
            'amount' => $request->amount
        ]);
        $base = config('services.payment_base_url');
        $orderId = $order->id;

        // إرسال Paymera
        $response = $paymera->createPayment([
            "lang" => $request->lang,
            "terminalId" => config('services.paymera.terminal_id'),
            "amount" => $request->amount,

            // مهم جداً: تضمين order_id
            // "callbackURL" => route('payment.callback', $order->id),
            // "triggerURL" => route('payment.trigger', $order->id),
            "callbackURL" => $base . "/api/payment/callback/" . $orderId,
            "triggerURL" => $base . "/api/payment/trigger/" . $orderId,
            "notes" => "Order #" . $order->id
        ]);

        //  تحقق أولاً
        if (!$response) {
            return response()->json([
                'error' => 'No response from Paymera'
            ], 500);
        }

        //  تحقق من ErrorCode
        if (!isset($response['ErrorCode'])) {
            return response()->json([
                'error' => 'Invalid response structure',
                'response' => $response
            ], 500);
        }

        //  تحقق من النجاح
        if ($response['ErrorCode'] != 0) {
            return response()->json($response, 400);
        }

        // تحقق من Data
        if (!isset($response['Data'])) {
            return response()->json([
                'error' => 'Missing Data field',
                'response' => $response
            ], 500);
        }

        // الآن آمن
        $payment->update([
            'payment_id' => $response['Data']['paymentId']
        ]);

        return response()->json([
            'payment_url' => $response['Data']['url'],
            'payment_id' => $payment->payment_id
        ]);
    }

    // =========================
    // 2. Trigger (إشعار فقط)
    // =========================

    // public function trigger($orderId)
    // {
    //     // تسجيل فقط
    //     \Log::info("Trigger received for order: $orderId");

    //     return response()->json(['ok' => true]);
    // }
    public function trigger($orderId, PaymeraService $paymera)
    {
        \Log::info("Trigger received for order: $orderId");

        $order = Order::findOrFail($orderId);
        $payment = $order->payment;

        if (!$payment || !$payment->payment_id) {
            return response()->json(['error' => 'Payment not found'], 404);
        }

        $status = null;
        $response = null;

        for ($i = 0; $i < 5; $i++) {
            $response = $paymera->getStatus($payment->payment_id);

            if (!$response || !isset($response['ErrorCode'])) continue;
            if ($response['ErrorCode'] != 0) continue;
            if (!isset($response['Data']['status'])) continue;

            $status = $response['Data']['status'];

            if ($status != 'P') break;

            sleep(2);
        }

        if ($status == 'A') {
            $payment->markSuccess($response['Data']['rrn'] ?? null, $response);
        } elseif ($status == 'F') {
            $payment->markFailed($response);
        } elseif ($status == 'C') {
            $payment->markCanceled($response);
        }

        return response()->json(['ok' => true]);
    }


    // =========================
    // 3. Callback + Polling
    // =========================
    public function callback($orderId, PaymeraService $paymera)
    {
        $order = Order::findOrFail($orderId);
        $payment = $order->payment;

        if (!$payment || !$payment->payment_id) {
            return response()->json([
                'error' => 'Payment not found'
            ], 404);
        }

        $status = null;
        $response = null;

        for ($i = 0; $i < 5; $i++) {

            $response = $paymera->getStatus($payment->payment_id);

            //  حماية من null
            if (!$response || !isset($response['ErrorCode'])) {
                \Log::error("Invalid Paymera response", [
                    'response' => $response
                ]);
                continue;
            }

            if ($response['ErrorCode'] != 0) {
                \Log::error("Paymera Error", $response);
                continue;
            }

            if (!isset($response['Data']['status'])) {
                continue;
            }

            $status = $response['Data']['status'];

            if ($status != 'P') {
                break;
            }

            sleep(2);
        }

        if (!$status) {
            return response()->json([
                'error' => 'No valid status received',
                'response' => $response
            ], 500);
        }

        if ($status == 'A') {
            $payment->markSuccess($response['Data']['rrn'] ?? null, $response);
        } elseif ($status == 'F') {
            $payment->markFailed($response);
        } elseif ($status == 'C') {
            $payment->markCanceled($response);
        }

        return response()->json([
            'status' => $status
        ]);

        // $order = Order::findOrFail($orderId);
        // $payment = $order->payment;

        // //  Polling (مطابق الوثيقة)
        // for ($i = 0; $i < 5; $i++) {

        //     $response = $paymera->getStatus($payment->payment_id);

        //     if ($response['ErrorCode'] != 0) {
        //         continue;
        //     }

        //     $status = $response['Data']['status'];

        //     // إذا لم يعد pending → توقف
        //     if ($status != 'P') {
        //         break;
        //     }

        //     sleep(2);
        // }

        // if ($status == 'A') {
        //         $payment->markSuccess($response['Data']['rrn'], $response);
        //     } elseif ($status == 'F') {
        //         $payment->markFailed($response);
        //     } elseif ($status == 'C') {
        //         $payment->markCanceled($response);
        //     }

        //     return response()->json($response);
    }
    // =========================
    // 4. Cancel Payment
    // =========================
    public function cancel($paymentId, PaymeraService $paymera)
    {
        $payment = Payment::query()->where('payment_id', $paymentId)->firstOrFail();

        $response = $paymera->cancel($paymentId);
        if ($response['ErrorCode'] == 0) {
            $payment->markCanceled($response);
        }

        return response()->json($response);
    }

    // =========================
    // 5. Paymera Dashboard
    // =========================
    public function dashboard()
    {
        return $this->sendResponse([
            'url' => 'https://fmp-t.paymera.cc'
        ], 'Paymera Dashboard');
    }
}
