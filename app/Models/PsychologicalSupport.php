<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PsychologicalSupport extends Model
{
    protected $table = 'psychological_support';

    protected $fillable = [
        'title',
        'content',
        'image',
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
