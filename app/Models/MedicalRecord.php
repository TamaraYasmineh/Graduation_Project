<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $patient_id
 * @property string|null $chronic_diseases
 * @property string|null $allergies
 * @property string|null $medications
 * @property string|null $notes
 * @property int $is_smoker
 * @property float|null $height
 * @property float|null $weight
 * @property string|null $blood_type
 * @property string|null $surgeries
 * @property string|null $family_history
 * @property string|null $blood_pressure
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $patient
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalRecord whereAllergies($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalRecord whereBloodPressure($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalRecord whereBloodType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalRecord whereChronicDiseases($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalRecord whereFamilyHistory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalRecord whereHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalRecord whereIsSmoker($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalRecord whereMedications($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalRecord whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalRecord wherePatientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalRecord whereSurgeries($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalRecord whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalRecord whereWeight($value)
 * @mixin \Eloquent
 */
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
   

    public function medicalTests()
    {
        return $this->hasMany(MedicalTest::class);
    }
}
