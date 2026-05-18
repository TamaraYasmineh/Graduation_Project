<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
class Employee extends Model
{
 protected $fillable = [
    'user_id',
    'role',
    'date_of_birth',
    'phone2',
    'academic_degree',
    'degree_image',
    'work_history',
    'chronic_diseases',
    'marital_status',
    'bank_account',
    'sham_cash_number',
    'salary',
    'shift',
    'work_days'
 ];
 protected $casts = [
        'age'    => 'integer',
        'salary' => 'decimal:2',
        'work_days' => 'array',
    ];

    protected $hidden = [
        'bank_account',
        'sham_cash_number',
    ];



public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}


 public function getDegreeImageUrlAttribute(): ?string
    {
        return $this->degree_image
            ? asset('storage/' . $this->degree_image)
            : null;
    }

    public function getRoleInArabicAttribute(): string
    {
        return match ($this->role) {
            'nurse'             => 'ممرض',
            'sanitation_worker' => 'عامل نظافة',
            default             => $this->role,
        };
    }

    public function getMaritalStatusInArabicAttribute(): string
    {
        return match ($this->marital_status) {
            'single'   => 'أعزب',
            'married'  => 'متزوج',
            'divorced' => 'مطلق',
            'widowed'  => 'أرمل',
            default    => $this->marital_status,
        };
    }
    public function getAgeAttribute()
{
    return Carbon::parse($this->date_of_birth)->age;
}

     public function scopeNurses($query)
    {
        return $query->where('role', 'nurse');
    }

    public function scopeSanitationWorkers($query)
    {
        return $query->where('role', 'sanitation_worker');
    }

}
