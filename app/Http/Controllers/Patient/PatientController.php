<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Http\Requests\UpdatePatientProfileRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\InforPatientResource;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
class PatientController extends BaseController
{
    public function updateProfile(UpdatePatientProfileRequest $request)
    {
        $user = $request->user();

        if (!$user /*|| !$user->hasRole('patient')*/) {
            return $this->sendError('Unauthorized', [], 403);
        }

        // $patient = $user->patient;
        // if (!$patient) {
        //     return $this->sendError('Patient not found', [], 404);
        // }

        if ($request->hasFile('profile_image')) {

            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }
            $path = $request->file('profile_image')->store('profiles', 'public');
        } else {
            $path = $user->profile_image;
        }

        $user->update([
            'gender' => $request->gender ?? $user->gender,
            'phone' => $request->phone ?? $user->phone,
            'profile_image' => $path,
        ]);

       /* $patient->update($request->only([
            'date_of_birth',
            'country',
            'city',
            'emergency_contact',
        ]));*/
        if ($user->hasRole('patient')) {
            $user->patient?->update($request->only([
                'date_of_birth',
                'country',
                'city',
                'emergency_contact',
            ]));
        }
    
        elseif ($user->hasRole('doctor') || $user->hasRole('super_doctor')) {
            $user->doctor?->update($request->only([
                'specialization',
                'years_of_experience',
                'bio',
                'department',
            ]));
        }
    
        elseif ($user->hasRole('secretary')) {
            $user->secretary?->update($request->only([
                'hire_date',
                'work_shift',
            ]));
        }
        return $this->sendResponse(
            new UserResource($user->load(['patient','doctor','secretary'])),
            'Profile updated successfully'
        );
    }
   /* public function showProfile(Request $request)
   {
    $user = $request->user();

    if (!$user) {
        return $this->sendError('Unauthorized', [], 403);
    }

    $user->load([
        'patient',
        'medicalRecord',
        'appointments.doctor.user'
    ]);

    return $this->sendResponse(
        new UserResource($user),
        'Profile fetched successfully'
    );
   }*/
   public function showProfile(Request $request)
   {
       $user = $request->user();
   
       if (!$user) {
           return $this->sendError('Unauthorized', [], 403);
       }
   
       $relations = [];
   
       if ($user->hasRole('patient')) {
           $relations = [
               'patient',
               'medicalRecord',
               'appointments.doctor.user'
           ];
       }
   
       elseif ($user->hasRole('doctor') || $user->hasRole('super_doctor')) {
           $relations = [
               'doctor',
               'appointments.patient.user'
           ];
       }
   
       elseif ($user->hasRole('secretary')) {
           $relations = [
               'secretary'
           ];
       }
   
       $user->load($relations);
   
       return $this->sendResponse(
           new UserResource($user),
           'Profile fetched successfully'
       );
   }
    public function getPatient(Request $request)
    {
       
        $query = User::role('patient')
        ->with(['patient', 'medicalRecord','appointments.doctor.user']);
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        $patients = $query->latest()->paginate(10);

        return $this->sendResponse(
            InforPatientResource::collection($patients),
            'Patients retrieved successfully',
            200
        );
    
    }
}
