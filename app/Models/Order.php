<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'status',
        'appointment_id',
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
    public function appointment()
{
    return $this->belongsTo(Appointment::class);
}
    public function markAccepted()
    {
        $this->update(['status' => 'accepted']);
        $this->appointment?->update(['status' => 'confirmed']);
    }

    public function markFailed()
    {
        $this->update(['status' => 'failed']);
        $this->appointment?->update(['status' => 'cancelled']);

    }

    public function markCanceled()
    {
        $this->update(['status' => 'canceled']);
        $this->appointment?->update(['status' => 'cancelled']);
    }

}
