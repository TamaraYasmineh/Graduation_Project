<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Secretary extends Model
{
    protected $fillable = [
        'user_id',
        'hire_date',
        'work_shift'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
