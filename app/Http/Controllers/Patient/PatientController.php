<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Http\Requests\UpdatePatientProfileRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Storage;

class PatientController extends BaseController
{
    public function updateProfile(UpdatePatientProfileRequest $request)
    {
        $user = $request->user();

        if (!$user || !$user->hasRole('patient')) {
            return $this->sendError('Unauthorized', [], 403);
        }

        $patient = $user->patient;

        if (!$patient) {
            return $this->sendError('Patient not found', [], 404);
        }

        if ($request->hasFile('profile_image')) {

            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }

            // رفع الصورة الجديدة
            $path = $request->file('profile_image')->store('profiles', 'public');
        } else {
            $path = $user->profile_image;
        }

        // تحديث بيانات المستخدم
        $user->update([
            'gender' => $request->gender ?? $user->gender,
            'phone' => $request->phone ?? $user->phone,
            'profile_image' => $path,
        ]);

        $patient->update($request->only([
            'date_of_birth',
            'country',
            'city',
            'emergency_contact',
        ]));

        return $this->sendResponse(
            new UserResource($user->load('patient')),
            'Profile updated successfully'
        );
    }
}
