<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Treatment_session extends Model
{
    protected $fillable = [
        'treatment_plan_id',
        'session_date',
        'height',
        'weight',
        'bsa',
        'dosage',
        'notes',
        'lab_requested',
        'lab_tests_requested',
        'lab_results',
    ];

    /*
    |----------------------------
    | Relationships
    |----------------------------
    */

    // كل جلسة تتبع خطة علاج
    public function treatmentPlan()
    {
        return $this->belongsTo(Treatment_plan::class);
    }
}
