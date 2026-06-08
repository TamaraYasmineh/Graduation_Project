<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            // ---- من جدول users ----
            'name' => $this->user->name,
            'email' => $this->user->email,
            'phone1' => $this->user->phone,
            'gender' => $this->user->gender,
            'status' => $this->user->status,
            'is_active' => $this->user->is_active,
            'profile_image' => $this->user->profile_image
                                ? asset('storage/'.$this->user->profile_image)
                                : null,

            // ---- من جدول employees ----
            'role' => $this->role,
            'role_in_arabic' => $this->role_in_arabic,
            'age' => $this->age,
            'phone2' => $this->phone2,
            'academic_degree' => $this->academic_degree,
            'degree_image_url' => $this->degree_image_url,
            'work_history' => $this->work_history,
            'chronic_diseases' => $this->chronic_diseases,
            'marital_status' => $this->marital_status,
            'marital_status_in_arabic' => $this->marital_status_in_arabic,
            'salary' => $this->salary,

            // ---- حقول حساسة — للسوبر أدمن فقط ----
            'bank_account' => $this->when(
                auth()->user()?->hasRole('super_doctor'),
                $this->bank_account
            ),
            'sham_cash_number' => $this->when(
                auth()->user()?->hasRole('super_doctor'),
                $this->sham_cash_number
            ),

            'created_at' => $this->created_at->format('Y-m-d H:i'),
            'shift' => $this->shift,
            'work_days' => $this->work_days,
        ];
    }
}
