<?php

namespace App\Services;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\PatientReferral;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PatientReferralService
{
    public function referPatient(int $patientId, array $data, Doctor $doctor)
    {
        $patient = Patient::find($patientId);

        if (! $patient) {
            return [
                'success' => false,
                'message' => 'Patient not found',
                'code' => 404,
            ];
        }

        DB::beginTransaction();

        try {
            $referralData = [
                'patient_id' => $patientId,
                'referred_by' => $doctor->id, // هنا سيأخذ معرف جدول doctors
                'type' => $data['type'],
                'reason' => $data['reason'],
                'notes' => $data['notes'] ?? null,
                'referred_at' => now(),
                'status' => 'pending',
            ];

            // إضافة البيانات حسب نوع التحويل
            if ($data['type'] === 'internal') {
                $referralData['referred_to_doctor_id'] = $data['referred_to_doctor_id'];
            } else {
                $referralData['external_center_name'] = $data['external_center_name'];
                $referralData['external_center_phone'] = $data['external_center_phone'];
                $referralData['external_center_address'] = $data['external_center_address'];
            }

            $referral = PatientReferral::create($referralData);

            DB::commit();

            return [
                'success' => true,
                $referral->load([
                    'patient.user',
                    'referredBy.user',
                    'referredToDoctor.user',
                ]),
                'message' => 'Patient referred successfully',
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Failed to refer patient: ' . $e->getMessage(),
                'code' => 500,
            ];
        }
    }

    public function updateReferralStatus(int $referralId, string $status, User $user)
{
    $referral = PatientReferral::find($referralId);

    if (! $referral) {
        return [
            'success' => false,
            'message' => 'Referral not found',
            'code' => 404,
        ];
    }

    // إذا أردت السماح فقط للطبيب المحول إليه بتغيير الحالة
    /*
    if ($user->doctor?->id != $referral->referred_to_doctor_id) {
        return [
            'success' => false,
            'message' => 'Unauthorized',
            'code' => 403,
        ];
    }
    */

    $referral->status = $status;
    $referral->save();

    $referral->load([
        'patient.user',
        'referredBy.user',
        'referredToDoctor.user',
    ]);

    return [
        'success' => true,
        'data' => $referral,
        'message' => 'Referral status updated successfully',
    ];
}

    public function getReferrals(array $filters = [])
    {
        $query = PatientReferral::with(['patient', 'referredBy', 'referredToDoctor'])
            ->latest('referred_at');

        // فلترة حسب النوع
        if (isset($filters['type'])) {
            $query->byType($filters['type']);
        }

        // فلترة حسب الحالة
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $referrals = $query->paginate($filters['per_page'] ?? 15);

        return [
            'success' => true,
            'data' => $referrals,
        ];
    }

    public function getReferralStatistics()
    {
        $totalReferrals = PatientReferral::count();
        $internalReferrals = PatientReferral::internal()->count();
        $externalReferrals = PatientReferral::external()->count();
        $thisMonthReferrals = PatientReferral::thisMonth()->count();

        return [
            'success' => true,
            'data' => [
                'total_referrals' => $totalReferrals,
                'internal_referrals' => $internalReferrals,
                'external_referrals' => $externalReferrals,
                'this_month_referrals' => $thisMonthReferrals,
            ],
        ];
    }

    public function getCenterDoctors()
    {
        $doctors = Doctor::with('user')
        ->get();
        return [
            'success' => true,
            'data' => $doctors->map(function ($doctor) {
                return [
                    'id' => $doctor->id,
                    'name' => $doctor->user->name,
                    'specialty' => $doctor->specialty,
                ];
            }),
        ];
    }
}