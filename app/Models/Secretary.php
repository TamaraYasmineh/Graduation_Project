<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string|null $hire_date
 * @property string|null $work_shift
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Secretary newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Secretary newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Secretary query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Secretary whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Secretary whereHireDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Secretary whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Secretary whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Secretary whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Secretary whereWorkShift($value)
 *
 * @mixin \Eloquent
 */
class Secretary extends Model
{
    protected $fillable = [
        'user_id',
        'hire_date',
        'work_shift',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function medicalTests()
    {
        return $this->morphMany(MedicalTest::class, 'uploadable');
    }
}
