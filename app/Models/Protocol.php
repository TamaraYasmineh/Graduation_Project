<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Protocol extends Model
{
    protected $fillable = [
        'name',
        'disease_type',
        'therapeutic_intent',
        'cycle_length_days',
        'administration_days',
        'suggested_number_of_cycles',
        'pre_medications',
        'mandatory_tests',
    ];

    public function drugs()
    {
        return $this->hasMany(Drug::class);
    }
}
