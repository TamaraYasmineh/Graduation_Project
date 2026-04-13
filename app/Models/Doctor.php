<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    return $this->hasMany(Appointments::class);
    }
    public function schedules()
   {
    return $this->hasMany(Schedule::class);
   }
}
