<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Working_hours extends Model
{
    protected $fillable = [
        'center_info_id',
        'day',
        'start_time',
        'end_time',
        'is_closed',
    ];

    protected $casts = [
        'is_closed' => 'boolean',
    ];

    public function centerInfo()
    {
        return $this->belongsTo(CenterInfo::class);
    }
}
