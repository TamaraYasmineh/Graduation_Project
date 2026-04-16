<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $title
 * @property string $content
 * @property string|null $icon
 * @property int $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $creator
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Advice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Advice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Advice query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Advice whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Advice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Advice whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Advice whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Advice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Advice whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Advice whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
