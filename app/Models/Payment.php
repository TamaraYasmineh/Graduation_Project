<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'payment_id',
        'amount',
        'status',
        'rrn',
        'paid_at',
        'raw_response'
    ];

    protected $casts = [
        'raw_response' => 'array',
        'paid_at' => 'datetime',
    ];


    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function markSuccess($rrn, $response)
    {
        $this->update([
            'status' => 'A',
            'rrn' => $rrn,
            'paid_at' => now(),
            'raw_response' => $response
        ]);

        $this->order->markAccepted();
    }

    public function markFailed($response)
    {
        $this->update([
            'status' => 'F',
            'raw_response' => $response
        ]);

        $this->order->markFailed();
    }

    public function markCanceled($response)
    {
        $this->update([
            'status' => 'C',
            'raw_response' => $response
        ]);

        $this->order->markCanceled();
    }
}
