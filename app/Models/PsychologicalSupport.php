<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $title
 * @property string $content
 * @property string $image
 * @property int $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $creator
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PsychologicalSupport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PsychologicalSupport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PsychologicalSupport query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PsychologicalSupport whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PsychologicalSupport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PsychologicalSupport whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PsychologicalSupport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PsychologicalSupport whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PsychologicalSupport whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PsychologicalSupport whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
