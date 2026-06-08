<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $specialization
 * @property int|null $years_of_experience
 * @property string $license_number
 * @property string|null $bio
 * @property string|null $department
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Appointment> $appointments * @property-read int|null $appointments_count
 * @property-read Collection<int, Schedule> $schedules
 * @property-read int|null $schedules_count
 * @property-read User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereBio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereDepartment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereLicenseNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereSpecialization($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Doctor whereYearsOfExperience($value)
 *
 * @mixin \Eloquent
 */
class Doctor extends Model
{
    protected $fillable = [
        'user_id',
        'specialization',
        'years_of_experience',
        'license_number',
        'bio',
        'department',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class)
            ->whereRaw('TIMESTAMP(date, end_time) > NOW()');
    }

    public function reviews()
    {
        return $this->hasMany(DoctorReview::class);
    }

    public function calculateAverageRating()
    {
        return round($this->reviews()->avg('rating'), 1);
    }

    public function patients()
    {
        return $this->belongsToMany(
            User::class,
            'appointments',
            'doctor_id',
            'patient_id'
        )->distinct();
    }

    public function consultant()
    {
        return $this->morphOne(
            Consultant::class,
            'consultable'
        );
    }
}
