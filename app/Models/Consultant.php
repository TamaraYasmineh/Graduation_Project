<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultant extends Model
{
    use HasFactory;

    protected $fillable = [
        'consultation_fee',
        'whatsapp_number',
        'is_active',
    ];

    public function consultable()
    {
        return $this->morphTo();
    }
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
