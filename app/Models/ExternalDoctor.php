<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalDoctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'specialization',
        'years_of_experience',
        'license_number',
        'bio',
        'profile_image',
    ];

    public function consultant()
    {
        return $this->morphOne(
            Consultant::class,
            'consultable'
        );
    }

    public function getProfileImageUrlAttribute()
    {
        return $this->profile_image
            ? asset('storage/'.$this->profile_image)
            : null;
    }
}
