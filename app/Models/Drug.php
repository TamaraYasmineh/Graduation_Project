<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Drug extends Model
{
    protected $fillable = [
        'protocol_id',
        'name',
        'dose',
        'dose_basis',
        'route',
    ];

    public function protocol()
    {
        return $this->belongsTo(Protocol::class);
    }
}
