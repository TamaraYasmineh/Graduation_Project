<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultationOrder extends Model
{
    protected $fillable = [

        'user_id',
        'consultation_request_id',
        'amount',
        'status',
    ];

    public function consultationRequest()
    {
        return $this->belongsTo(
            ConsultationRequest::class
        );
    }

    public function payment()
    {
        return $this->hasOne(
            ConsultationPayment::class,
            'consultation_order_id'
        );
    }

    public function user()
    {
        return $this->belongsTo(
            User::class
        );
    }

    public function markAccepted()
    {
        $this->update([
            'status' => 'accepted',
        ]);
    }

    public function markFailed()
    {
        $this->update([
            'status' => 'failed',
        ]);
    }

    public function markCanceled()
    {
        $this->update([
            'status' => 'canceled',
        ]);
    }
}
