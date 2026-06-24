<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultationRequest extends Model
{
    protected $fillable = [
        'user_id',
        'consultant_id',
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

    public function consultant()
    {
        return $this->belongsTo(
            Consultant::class
        );
    }

    public function user()
    {
        return $this->belongsTo(
            User::class
        );
    }

    //     public function consultationOrder()
    // {
    //     return $this->belongsTo(
    //         ConsultationOrder::class
    //     );
    // }
    public function order()
    {
        return $this->hasOne(
            ConsultationOrder::class,
            'consultation_request_id'
        );
    }
}
