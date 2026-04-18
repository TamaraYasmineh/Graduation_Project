<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $doctor_id
 * @property int $patient_id
 * @property string $date
 * @property string $start_time
 * @property string $end_time
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Doctor $doctor
 * @property-read \App\Models\User $patient
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointments newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointments newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointments query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointments whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointments whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointments whereDoctorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointments whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointments whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointments wherePatientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointments whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointments whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointments whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Appointments extends Model
{
    protected $fillable = [
        'doctor_id',
        'patient_id',
        'date',
        'start_time',
        'end_time',
        'status',
        'session_type'
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }
}
