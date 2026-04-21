<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'status',
    ];

    // =========================
    //  العلاقات
    // =========================

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function markAccepted()
    {
        $this->update(['status' => 'accepted']);
    }

    public function markFailed()
    {
        $this->update(['status' => 'failed']);
    }

    public function markCanceled()
    {
        $this->update(['status' => 'canceled']);
    }
}
