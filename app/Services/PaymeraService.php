<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PaymeraService
{
    private function client()
    {
        return Http::withBasicAuth(
            config('services.paymera.username'),
            config('services.paymera.password')
        );
    }

    public function createPayment($data)
    {
        return $this->client()->post(
            config('services.paymera.base_url') . '/api/create-payment',
            $data
        )->json();
    }

    public function getStatus($paymentId)
    {
        return $this->client()->get(
            config('services.paymera.base_url') . "/api/get-payment-status/$paymentId"
        )->json();
    }

    public function cancel($paymentId)
    {
        $response = Http::withBasicAuth(
            config('services.paymera.username'),
            config('services.paymera.password')
        )->post(config('services.paymera.base_url') . '/api/cancel-payment', [
            "lang" => "en",
            "payment_id" => $paymentId
        ]);

        return $response->json();
        // return $this->client()->post(
        //     config('services.paymera.base_url').'/api/cancel-payment',
        //     [
        //         "lang" => "ar",
        //         "payment_id" => $paymentId
        //     ]
        // )->json();
    }
}
