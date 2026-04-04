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
    /*  public function login($email, $password)
{
    // ⛔ تحقق إذا الحساب مقفول
    if (Cache::has($this->blockedKey($email))) {
        throw new \Exception('Too many attempts. Try again later.');
    }

    $user = User::where('email', $email)->first();

    if (!$user || !Hash::check($password, $user->password)) {

        // ❌ زيادة المحاولات
        $attempts = Cache::increment($this->attemptsKey($email));

        // ⛔ إذا وصل 5 → block
        if ($attempts >= 5) {
            Cache::put(
                $this->blockedKey($email),
                true,
                now()->addMinutes(15)
            );
        }

        // خلي attempts تنتهي بعد فترة
        Cache::put(
            $this->attemptsKey($email),
            $attempts,
            now()->addMinutes(10)
        );

        throw new \Exception('Invalid credentials');
    }

    // ✅ نجاح → تصفير المحاولات
    Cache::forget($this->attemptsKey($email));

    if ($user->status !== User::STATUS_APPROVED) {
        throw new \Exception('Account not approved');
    }

    $otp = $this->otpService->generate($user->email);

    return [
        'message' => 'OTP sent',
        'otp' => $otp,
    ];
}
    public function verifyOtp($email, $code)
   {
    $valid = $this->otpService->verify($email, $code);

    if (!$valid) {
        throw new \Exception('Invalid or expired OTP');
    }

    $user = User::where('email', $email)->first();

    if (!$user) {
        throw new \Exception('User not found');
    }

    $token = $user->createToken('auth_token')->plainTextToken;

    return [
        'user' => $user,
        'token' => $token,
    ];
   }
   private function attemptsKey($email)
{
    return 'login_attempts_' . md5($email);
}

private function blockedKey($email)
{
    return 'login_blocked_' . md5($email);
}*/
}
