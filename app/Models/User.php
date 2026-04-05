<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable,HasApiTokens, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    protected $guard_name = 'web';
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'gender',
        'phone',
        'profile_image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function doctor()
    {
     return $this->hasOne(Doctor::class);
    }

    public function patient()
    {
     return $this->hasOne(Patient::class);
    }

    public function secretary()
    {
     return $this->hasOne(Secretary::class);
    }
    public function getRoleAttribute()
    {
    return $this->roles->first()?->name;
    }
    public function supports()
{
    return $this->hasMany(PsychologicalSupport::class, 'created_by');
}
public function advices()
{
    return $this->hasMany(Advice::class, 'created_by');
}
}
