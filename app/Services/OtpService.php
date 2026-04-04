<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Mail\UserLoginOtp;
use App\Models\User;

class OtpService
{

    public function send($email)
    {

        if (Cache::has('otp_block_' . $email)) {
            return $this->error('Account temporarily locked', 403);
        }
        if (Cache::has('otp_cooldown_' . $email)) {
            return $this->error('Wait before requesting OTP', 429);
        }

        $otp = rand(100000, 999999);

        Cache::put('otp_data_' . $email, [
            'code' => Hash::make($otp),
            'attempts' => 0,
        ], now()->addMinutes(10));

        Cache::put('otp_cooldown_' . $email, true, now()->addSeconds(60));

        Mail::to($email)->queue(new UserLoginOtp($otp));

        return $this->success('OTP sent successfully');
    }

    public function verify($email, $code)
    {
        $key = 'otp_data_' . $email;

        $otpData = Cache::get($key);

        if (!$otpData) {
            return $this->error('OTP expired', 422);
        }

        if ($otpData['attempts'] >= 5) {
            Cache::forget($key);
            Cache::put('otp_block_' . $email, true, now()->addMinutes(15));

            return $this->error('Too many attempts, account locked', 429);
        }

        if (!Hash::check($code, $otpData['code'])) {
            $otpData['attempts']++;

            Cache::put($key, $otpData, now()->addMinutes(10));

            return $this->error('Invalid OTP', 422, [
                'remaining_attempts' => 5 - $otpData['attempts']
            ]);
        }

        Cache::forget($key);

        $user = User::where('email', $email)->first();

        $token = $user->createToken('user-login')->plainTextToken;

        return $this->success('OTP verified', [
            'token' => $token,
            'user' => $user
        ]);
    }

    public function resend($email)
    {
        if (Cache::has('otp_cooldown_' . $email)) {
            return $this->error('Wait before resend', 429);
        }

        if (!Cache::has('otp_data_' . $email)) {
            return $this->error('No OTP request found', 404);
        }

        return $this->send($email);
    }

    private function success($message, $data = [])
    {
        return [
            'status' => true,
            'message' => $message,
            'data' => $data
        ];
    }

    private function error($message, $code, $extra = [])
    {
        return [
            'status' => false,
            'message' => $message,
            'code' => $code,
            'data' => $extra
        ];
    }
}
