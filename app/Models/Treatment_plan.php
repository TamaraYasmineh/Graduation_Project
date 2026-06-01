<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Treatment_plan extends Model
{
    protected $fillable = [
        'medical_record_id',
        'diagnosis',
        'protocol_id',
        'medication',
        'duration',
        'session_date'
    ];

    /*
    |----------------------------
    | Relationships
    |----------------------------
    */

    // كل خطة علاج تنتمي لملف طبي واحد
    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    // كل خطة تحتوي عدة جلسات
    public function sessions()
    {
        return $this->hasMany(Treatment_session::class);
    }
}
