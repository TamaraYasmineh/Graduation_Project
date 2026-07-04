<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientArchive extends Model
{
    protected $fillable = [
        'patient_id',
        'archived_by',
        'reason',
        'note',
        'archived_at',
        'is_active',
    ];

    protected $casts = [
        'archived_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function archivedBy()
    {
        return $this->belongsTo(User::class, 'archived_by');
    }

    // Scope للحصول على الأرشفات النشطة فقط
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope للفلترة حسب السبب
    public function scopeByReason($query, $reason)
    {
        return $query->where('reason', $reason);
    }
}
