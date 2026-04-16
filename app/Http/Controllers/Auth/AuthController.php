<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AuthService;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends BaseController

{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    // public function register(RegisterRequest $request)
    // {
    //     $result = $this->authService->register($request->validated());
    //     $user = $result['user'];
    //     $status = $result['status'];
    //     if ($status === User::STATUS_APPROVED) {
    //         $token = $user->createToken('auth_token')->plainTextToken;
    //         return $this->sendResponse(
    //             [
    //                 'user' => new UserResource($user),
    //                 'token' => $token
    //             ],
    //             'Registered successfully'
    //         );
    //     }
    //     return $this->sendResponse(
    //         null,
    //         'Account created, waiting for admin approval'
    //     );
    // }
    public function register(RegisterRequest $request)
{
    $data = $request->validated();
    if ($request->hasFile('profile_image')) {
        $data['profile_image'] = $request->file('profile_image')->store('profiles', 'public');
    }
    $result = $this->authService->register($data);

    $user = $result['user'];
    $status = $result['status'];

    if ($status === User::STATUS_APPROVED) {
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->sendResponse(
            [
                'user' => new UserResource($user),
                'token' => $token
            ],
            'Registered successfully'
        );
    }

    return $this->sendResponse(
        null,
        'Account created, waiting for admin approval'
    );
}
    public function login(LoginRequest $request, OtpService $otpService)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->sendError('Invalid credentials', [], 401);
        }
        $result = $otpService->send($request->email);

        if (!$result['status']) {
            return $this->sendError(
                $result['message'],
                $result['data'] ?? [],
                $result['code']
            );
        }

        return $this->sendResponse(
            null,
            $result['message']
        );
    }
    public function verifyOtp(VerifyOtpRequest $request, OtpService $otpService)
    {
        $email = trim($request->email);
        $code  = trim($request->code);

        $result = $otpService->verify($email, $code);

        if (!$result['status']) {
            return $this->sendError(
                $result['message'],
                $result['data'] ?? [],
                $result['code']
            );
        }

        return $this->sendResponse(
            [
                'token' => $result['data']['token'],
                'user'  => new UserResource($result['data']['user'])
            ],
            $result['message']
        );
    }

    public function resendOtp(Request $request, OtpService $otpService)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $result = $otpService->resend($request->email);

        if (!$result['status']) {
            return $this->sendError(
                $result['message'],
                $result['data'] ?? [],
                $result['code']
            );
        }

        return $this->sendResponse(
            null,
            $result['message']
        );
    }
    public function forgotPassword(Request $request, OtpService $otpService)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $result = $otpService->send($request->email);

        if (!$result['status']) {
            return $this->sendError(
                $result['message'],
                $result['data'] ?? [],
                $result['code']
            );
        }

        return $this->sendResponse(null, 'OTP sent for password reset');
    }
    public function resetPassword(Request $request, OtpService $otpService)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required',
            'password' => 'required|min:6|confirmed'
        ]);

        $result = $otpService->verify($request->email, $request->code, false);

        if (!$result['status']) {
            return $this->sendError(
                $result['message'],
                $result['data'] ?? [],
                $result['code']
            );
        }

        $user = User::where('email', $request->email)->first();

        if (Hash::check(trim($request->password), $user->password)) {
            return $this->sendError(
                'You cannot use your old password',
                [],
                422
            );
        }

        $user->update([
            'password' => Hash::make(trim($request->password))
        ]);

        $user->tokens()->delete();

        return $this->sendResponse(null, 'Password reset successfully');
    }
}
