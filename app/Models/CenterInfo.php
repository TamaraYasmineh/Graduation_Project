<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CenterInfo extends Model
{
     protected $table = 'center_info';

    protected $fillable = [
        'location',
        'opening_hours',
        'address_on_map',
        'branches',
        'services',
        'contact'
    ];
}
