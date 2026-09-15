<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\BaseController;
use App\Http\Requests\UpdatePatientProfileRequest;
use App\Http\Resources\InforPatientResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\PatientFullProfileResource;
use App\Models\Appointment;

class PatientController extends BaseController
{
    public function updateProfile(UpdatePatientProfileRequest $request)
    {
        $user = $request->user();

        if (! $user) {
            return $this->sendError('Unauthorized', [], 403);
        }
        if ($request->hasFile('profile_image')) {

            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }
            $path = $request->file('profile_image')->store('profiles', 'public');
        } else {
            $path = $user->profile_image;
        }

        $user->update([
            'name' => $request->name ?? $user->name,
            'email' => $request->email ?? $user->email,
            'gender' => $request->gender ?? $user->gender,
            'phone' => $request->phone ?? $user->phone,
            'profile_image' => $path,
        ]);
        if ($user->hasRole('patient')) {
            $user->patient?->update($request->only([
                'date_of_birth',
                'country',
                'city',
                'emergency_contact',
            ]));
        } elseif ($user->hasRole('doctor') || $user->hasRole('super_doctor')) {
            $user->doctor?->update($request->only([
                'specialization',
                'years_of_experience',
                'bio',
                'department',
            ]));
        } elseif ($user->hasRole('secretary')) {
            $user->secretary?->update($request->only([
                'hire_date',
                'work_shift',
            ]));
        }

        return $this->sendResponse(
            new UserResource($user->load(['patient', 'doctor', 'secretary'])),
            'Profile updated successfully'
        );
    }

    public function showProfile(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return $this->sendError('Unauthorized', [], 403);
        }

        $relations = [];

        if ($user->hasRole('patient')) {
            $relations = [
                'patient',
                'medicalRecord',
                'appointments.doctor.user',
            ];
        } elseif ($user->hasRole('doctor') || $user->hasRole('super_doctor')) {
            $relations = [
                'doctor',
                'appointments.patient.user',
            ];
        } elseif ($user->hasRole('secretary')) {
            $relations = [
                'secretary',
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
        $user = auth()->user();

        $query = User::role('patient')
            ->whereDoesntHave('patient.archives')
            ->with([
                'patient',
                'patient.latestApprovedInternalReferral.referredToDoctor.user',
                'patient.latestPendingInternalReferral.referredBy.user',
                'medicalRecord',
                'appointments.doctor.user',
            ]);

        /*
    |--------------------------------------------------------------------------
    | Super Doctor
    |--------------------------------------------------------------------------
    | السوبر دكتور يرى:
    | 1. المرضى الذين ليس لديهم تحويل داخلي accepted
    | 2. المرضى الذين تم تحويلهم إليه هو شخصياً
    |
    | لكنه لا يرى المريض بعد قبول تحويله إلى طبيب آخر.
    |--------------------------------------------------------------------------
    */

        if ($user->hasRole('super_doctor')) {

            $superDoctor = $user->doctor;

            if (!$superDoctor) {
                return $this->sendResponse(
                    [],
                    'Doctor profile not found',
                    404
                );
            }

            $query->where(function ($q) use ($superDoctor) {

                // الحالة الأولى:
                // لا يوجد تحويل داخلي accepted
                $q->whereDoesntHave(
                    'patient.latestApprovedInternalReferral'
                )

                    // الحالة الثانية:
                    // يوجد تحويل accepted ولكن إلى السوبر دكتور نفسه
                    ->orWhereHas(
                        'patient.latestApprovedInternalReferral',
                        function ($referralQuery) use ($superDoctor) {

                            $referralQuery->where(
                                'referred_to_doctor_id',
                                $superDoctor->id
                            );
                        }
                    );
            });
        }

        /*
    |--------------------------------------------------------------------------
    | Doctor
    |--------------------------------------------------------------------------
    | الطبيب العادي يرى مرضاه الحاليين فقط
    |--------------------------------------------------------------------------
    */

        if ($user->hasRole('doctor') && !$user->hasRole('super_doctor')) {

            $doctor = $user->doctor;

            if (!$doctor) {
                return $this->sendResponse(
                    [],
                    'Doctor profile not found',
                    404
                );
            }

            $query->where(function ($q) use ($doctor) {

                /*
            |--------------------------------------------------------------------------
            | الحالة الأولى:
            | المريض لديه آخر تحويل داخلي accepted إلى هذا الطبيب
            |--------------------------------------------------------------------------
            */

                $q->whereHas(
                    'patient.latestApprovedInternalReferral',
                    function ($referralQuery) use ($doctor) {

                        $referralQuery->where(
                            'referred_to_doctor_id',
                            $doctor->id
                        );
                    }
                )

                    /*
            |--------------------------------------------------------------------------
            | الحالة الثانية:
            | لا يوجد تحويل داخلي accepted
            | وبالتالي نعتمد على Appointment
            |--------------------------------------------------------------------------
            */

                    ->orWhere(function ($q) use ($doctor) {

                        // لا يوجد أي تحويل داخلي pending أو accepted
                        $q->whereDoesntHave('patient.referrals', function ($referralQuery) {

                            $referralQuery->where('type', 'internal')
                                ->whereIn('status', ['pending', 'accepted']);
                        })

                            ->whereHas('appointments', function ($appointmentQuery) use ($doctor) {

                                $appointmentQuery->where(
                                    'doctor_id',
                                    $doctor->id
                                );
                            });
                    });
            });
        }

        /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

        if ($request->search) {
            $query->where(
                'name',
                'like',
                '%' . $request->search . '%'
            );
        }

        $patients = $query->latest()->paginate(10);

        return $this->sendResponse(
            InforPatientResource::collection($patients),
            'Patients retrieved successfully',
            200
        );
    }
    public function showPatient($id)
    {
        $user = User::role('patient')
            ->whereHas('patient', function ($query) use ($id) {
                $query->where('id', $id);
            })
            ->with([
                'patient',
                'medicalRecord',
                'appointments.doctor.user',
                'appointments.order.payment',
            ])
            ->first();
        if (! $user) {
            return $this->sendError(
                'Patient not found',
                [],
                404
            );
        }

        return $this->sendResponse(
            new InforPatientResource($user),
            'Patient retrieved successfully'
        );
    }
    // public function fullProfile(User $patient)
    // {
    //     $user = auth()->user();

    //     // إذا كان دكتور
    //     if ($user->hasRole('doctor')) {

    //         $doctorId = $user->doctor->id;

    //         $hasAppointment = Appointment::where('doctor_id', $doctorId)
    //             ->where('patient_id', $patient->id)
    //             ->exists();

    //         abort_unless($hasAppointment, 403, 'Unauthorized.');
    //     }

    //     // إذا كان super_admin يشاهد الجميع

    //     $patient->load([
    //         'patient',
    //         'medicalRecord.treatmentPlan.protocol.drugs',
    //         'medicalRecord.treatmentPlan.sessions',
    //         'medicalRecord.medicalTests',
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'data' => new PatientFullProfileResource($patient),
    //     ]);
    // }
    public function fullProfile($id)
    {
        $currentUser = auth()->user();

        $patientUser = User::role('patient')
            ->whereHas('patient', function ($query) use ($id) {
                $query->where('id', $id);
            })
            ->with([
                'patient',
                'medicalRecord.treatmentPlan.protocol.drugs',
                'medicalRecord.treatmentPlan.sessions',
                'medicalRecord.medicalTests',
            ])
            ->first();

        if (!$patientUser) {
            return $this->sendError('Patient not found', [], 404);
        }

        if ($currentUser->hasRole('doctor')) {
            $doctorId = $currentUser->doctor->id;
            $patientId = $patientUser->patient->id;

            $hasAppointment = Appointment::where('doctor_id', $doctorId)
                ->where('patient_id', $patientId)
                ->exists();

            abort_unless($hasAppointment, 403, 'Unauthorized.');
        }

        return response()->json([
            'success' => true,
            'data' => new \App\Http\Resources\PatientFullProfileResource($patientUser),
        ]);
    }
}
