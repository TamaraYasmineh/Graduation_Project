<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $location
 * @property string $opening_hours
 * @property string $address_on_map
 * @property string|null $branches
 * @property string $services
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CenterInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CenterInfo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CenterInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CenterInfo whereAddressOnMap($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CenterInfo whereBranches($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CenterInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CenterInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CenterInfo whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CenterInfo whereOpeningHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CenterInfo whereServices($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CenterInfo whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CenterInfo extends Model
{
     protected $table = 'center_info';

    protected $fillable = [
        'location',
        'opening_hours',
        'address_on_map',
        'branches',
        'services',
    ];
}
