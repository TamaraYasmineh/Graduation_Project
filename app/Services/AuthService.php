<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Secretary;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Services\OTPService;

class AuthService
{
    public function __construct(
        private OTPService $otpService
    ) {}
    public function register(array $data)
    {
        return DB::transaction(function () use ($data) {

            $status = match ($data['role']) {
                'patient' => User::STATUS_APPROVED,
                default => User::STATUS_PENDING,
            };

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'status' => $status,
                'gender' => $data['gender'] ?? null,
                'phone' => $data['phone'] ?? null,
                'profile_image' => $data['profile_image'] ?? null,
            ]);

            $user->assignRole($data['role']);

            $this->createRoleData($user, $data);

            return [
                'user' => $user,
                'status' => $status,
            ];
        });
    }

    private function createRoleData($user, $data)
    {
        switch ($data['role']) {

            case 'doctor':
                Doctor::create([
                    'user_id' => $user->id,
                    'specialization' => $data['specialization'],
                    'years_of_experience' => $data['years_of_experience'] ?? null,
                    'license_number' => $data['license_number'],
                    'bio' => $data['bio'],
                    'department' => $data['department']
                ]);
                break;

            case 'patient':
                Patient::create([
                    'user_id' => $user->id,
                    'date_of_birth' => $data['date_of_birth'] ?? null,
                    'country' => $data['country'] ?? null,
                    'city' => $data['city'] ?? null,
                    'emergency_contact' => $data['emergency_contact'] ?? null,
                ]);
                break;

            case 'secretary':
                Secretary::create([
                    'user_id' => $user->id,
                    'hire_date' => $data['hire_date'] ?? null,
                    'work_shift' => $data['work_shift'] ?? null,
                ]);
                break;
        }
    }
  
}
