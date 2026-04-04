<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Http\Requests\UpdatePatientProfileRequest;
use App\Http\Resources\UserResource;

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

        $user->update([
            'gender' => $request->gender,
            'phone' => $request->phone,
            'profile_image' => $request->profile_image,
        ]);

        $patient->update([
            'date_of_birth' => $request->date_of_birth,
            'country' => $request->country,
            'city' => $request->city,
            'emergency_contact' => $request->emergency_contact,
        ]);

        return $this->sendResponse(
            new UserResource($user->load('patient')),
            'Profile updated successfully'
        );
    }
}
