<?php

namespace App\Services;

use App\Models\Doctor;
use App\Models\User;

class SuperDoctorService
{
    public function getAllDoctors()
    {
        return Doctor::with('user')->get();
    }
    public function getAllDoctorsWithSpecialization($specialization = null)
    {
        $query = Doctor::with('user');

        if ($specialization) {
            $query->where('specialization', 'like', "%$specialization%");
        }

        return $query->get();
    }

    public function toggleDoctorRole($id, $authUser)
    {
        //  تحقق أن المستخدم سوبر دكتور
        if (!$authUser->hasRole('super_doctor')) {
            return [
                'success' => false,
                'message' => 'Unauthorized',
                'code' => 403
            ];
        }

        $targetUser = User::find($id);

        if (!$targetUser) {
            return [
                'success' => false,
                'message' => 'User not found',
                'code' => 404
            ];
        }

        //  منع تعديل نفسك
        if ($authUser->id === $targetUser->id) {
            return [
                'success' => false,
                'message' => 'You cannot change your own role',
                'code' => 403
            ];
        }

        //  السماح فقط للأطباء
        if (!$targetUser->hasAnyRole(['doctor', 'super_doctor'])) {
            return [
                'success' => false,
                'message' => 'Only doctors can be promoted or demoted',
                'code' => 403
            ];
        }

        //  منع حذف آخر سوبر دكتور
        if (
            $targetUser->hasRole('super_doctor') &&
            User::role('super_doctor')->count() === 1
        ) {

            return [
                'success' => false,
                'message' => 'Cannot demote the last Super Doctor',
                'code' => 400
            ];
        }

        //  ترقية
        if ($targetUser->hasRole('doctor')) {

            $targetUser->syncRoles(['super_doctor']);

            return [
                'success' => true,
                'message' => 'Doctor promoted to Super Doctor'
            ];
        }

        //  تخفيض
        if ($targetUser->hasRole('super_doctor')) {

            $targetUser->syncRoles(['doctor']);

            return [
                'success' => true,
                'message' => 'Super Doctor demoted to Doctor'
            ];
        }

        // fallback
        return [
            'success' => false,
            'message' => 'Unexpected error',
            'code' => 500
        ];
    }
}
