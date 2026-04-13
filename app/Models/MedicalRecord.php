<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    protected $fillable = [
        'patient_id',
        'chronic_diseases',
        'allergies',
        'medications',
        'notes',
        'is_smoker',
        'height',
        'weight',
        'blood_type',
        'surgeries',
        'family_history',
        'blood_pressure',
    ];

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }
}
