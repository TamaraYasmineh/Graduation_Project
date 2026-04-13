<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advice extends Model
{
    protected $table = 'advices';
    protected $fillable = [
        'title',
        'content',
        'created_by',
        'icon'
        ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
