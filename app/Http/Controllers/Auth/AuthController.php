<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Http\Resources\UserResource;
use App\Models\DeviceToken;
use App\Models\Patient;
use App\Models\User;
use App\Services\AuthService;
use App\Services\FirebaseService;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends BaseController
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request, FirebaseService $firebase)
    {
        $data = $request->validated();
        if ($request->hasFile('profile_image')) {
            $data['profile_image'] = $request->file('profile_image')->store('profiles', 'public');
        }
        $result = $this->authService->register($data);

        $user = $result['user'];
        $status = $result['status'];
        $this->saveFcmToken($user, $request->fcm_token);
        if ($status === User::STATUS_PENDING) {
            $superDoctors = User::role('super_doctor')->get();

            $tokens = DeviceToken::whereIn('user_id', $superDoctors->pluck('id'))
                ->pluck('token');
            foreach ($tokens as $token) {
                $firebase->sendNotification(
                    $token,
                    'New joining request🔔',
                    "There is a new request from{$user->name}"
                );
            }
        }
        if ($status === User::STATUS_APPROVED) {
            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->sendResponse(
                [
                    'user' => new UserResource($user),
                    'token' => $token,
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
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return $this->sendError('Invalid credentials', [], 401);
        }
        $this->saveFcmToken($user, $request->fcm_token);
        $result = $otpService->send($request->email);

        if (! $result['status']) {
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
        $code = trim($request->code);

        $result = $otpService->verify($email, $code);

        if (! $result['status']) {
            return $this->sendError(
                $result['message'],
                $result['data'] ?? [],
                $result['code']
            );
        }
        $user = $result['data']['user'];
        $this->saveFcmToken($user, $request->fcm_token);

        return $this->sendResponse(
            [
                'token' => $result['data']['token'],
                'user' => new UserResource($result['data']['user']),
            ],
            $result['message']
        );
    }

    public function resendOtp(Request $request, OtpService $otpService)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $result = $otpService->resend($request->email);

        if (! $result['status']) {
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
            'email' => 'required|email|exists:users,email',
        ]);

        $result = $otpService->send($request->email);

        if (! $result['status']) {
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
            'password' => 'required|min:6|confirmed',
        ]);

        $result = $otpService->verify($request->email, $request->code, false);

        if (! $result['status']) {
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
            'password' => Hash::make(trim($request->password)),
        ]);

        $user->tokens()->delete();

        return $this->sendResponse(null, 'Password reset successfully');
    }

    private function saveFcmToken($user, $token)
    {
        if (! $token) {
            return;
        }
        DeviceToken::where('token', $token)->delete();
        DeviceToken::create([
            'user_id' => $user->id,
            'token' => $token,
        ]);
    }

    public function createPatientBySecretary(Request $request)
    {
        $actor = $request->user();
        if (! $actor->hasRole('secretary')) {
            return $this->sendError('Unauthorized', [], 403);
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'phone' => 'required|string|max:20|unique:users,phone',
            'gender' => 'nullable|in:male,female',
            'date_of_birth' => 'nullable|date',
            'country' => 'nullable|string',
            'city' => 'nullable|string',
            'emergency_contact' => 'nullable|string',
        ]);
        $plainPassword = Str::random(8);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'password' => Hash::make($plainPassword),
            'status' => User::STATUS_APPROVED,
        ]);
        $user->assignRole('patient');
        $patient = Patient::create([
            'user_id' => $user->id,
            'date_of_birth' => $request->date_of_birth,
            'country' => $request->country,
            'city' => $request->city,
            'emergency_contact' => $request->emergency_contact,
        ]);
        $token = $user->createToken('patient_token')->plainTextToken;

        return $this->sendResponse([
            'user' => new UserResource($user),
            'patient' => $patient,
            'token' => $token,
            'generated_password' => $plainPassword,
        ], 'Patient created successfully');
    }
}
