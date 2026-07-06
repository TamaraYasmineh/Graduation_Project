<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Doctor;
class PatientReferral extends Model
{
    protected $fillable = [
        'patient_id',
        'referred_by',
        'type',
        'referred_to_doctor_id',
        'external_center_name',
        'external_center_phone',
        'external_center_address',
        'reason',
        'notes',
        'status',
        'referred_at',
    ];

    protected $casts = [
        'referred_at' => 'datetime',
    ];

    // العلاقات
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function referredBy()
    {
        return $this->belongsTo(Doctor::class, 'referred_by');
    }

    public function referredToDoctor()
    {
        return $this->belongsTo(Doctor::class, 'referred_to_doctor_id');
    }

    // Scopes
    public function scopeInternal($query)
    {
        return $query->where('type', 'internal');
    }

    public function scopeExternal($query)
    {
        return $query->where('type', 'external');
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('referred_at', now()->month)
                     ->whereYear('referred_at', now()->year);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}
