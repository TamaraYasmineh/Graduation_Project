<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultationPayment extends Model
{
    protected $fillable = [
        'consultation_order_id',
        'payment_id',
        'amount',
        'status',
        'rrn',
        'paid_at',
        'raw_response',
    ];

    protected $casts = [
        'raw_response' => 'array',
        'paid_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(
            ConsultationOrder::class,
            'consultation_order_id'
        );
    }

    public function markSuccess(
        $rrn,
        $response
    ) {
        $this->update([

            'status' => 'A',

            'rrn' => $rrn,

            'paid_at' => now(),

            'raw_response' => $response,
        ]);

        $this->order->markAccepted();

        $this->order
            ->consultationRequest
            ->update([

                'status' => 'A',

                'rrn' => $rrn,

                'paid_at' => now(),

                'raw_response' => $response,
            ]);
    }

    public function markFailed(
        $response
    ) {
        $this->update([

            'status' => 'F',

            'raw_response' => $response,
        ]);

        $this->order->markFailed();

        $this->order
            ->consultationRequest
            ->update([

                'status' => 'F',

                'raw_response' => $response,
            ]);
    }

    public function markCanceled(
        $response
    ) {
        $this->update([

            'status' => 'C',

            'raw_response' => $response,
        ]);

        $this->order->markCanceled();

        $this->order
            ->consultationRequest
            ->update([

                'status' => 'C',

                'raw_response' => $response,
            ]);
    }
}
