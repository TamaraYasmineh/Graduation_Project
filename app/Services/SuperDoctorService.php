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
    public function getAllDoctorsWithSpecialization($search = null)
    {
        $query = Doctor::with(['user', 'schedules']);
    
        if ($search) {
            $query->where(function ($q) use ($search) {
    
                // 🔍 البحث بالاختصاص
                $q->where('specialization', 'like', "%$search%")
    
                  // 🔍 البحث باسم الدكتور
                  ->orWhereHas('user', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%$search%");
                  });
            });
        }
    
        return $query->get();
    }

    public function toggleDoctorRole($id, $authUser)
    {
        //  تحقق أن المستخدم سوبر دكتور
        if (!$authUser->can('toggle_doctor_role')) {
            return [
                'success' => false,
                'message' => 'Unauthorized',
                'code' => 403
            ];
        }

        $targetUser = User::find($id);

        if ($targetUser->status !== User::STATUS_APPROVED) {
            return [
                'success' => false,
                'message' => 'User must be approved first',
                'code' => 403
            ];
        }
        
        if ($authUser->id === $targetUser->id) {
            return [
                'success' => false,
                'message' => 'You cannot change your own role',
                'code' => 403
            ];
        }

       
        if (!$targetUser->hasAnyRole(['doctor', 'super_doctor'])) {
            return [
                'success' => false,
                'message' => 'Only doctors can be promoted or demoted',
                'code' => 403
            ];
        }

        
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

       
        if ($targetUser->hasRole('doctor')) {

            $targetUser->syncRoles(['super_doctor']);

            return [
                'success' => true,
                'message' => 'Doctor promoted to Super Doctor'
            ];
        }

       
        if ($targetUser->hasRole('super_doctor')) {

            $targetUser->syncRoles(['doctor']);

            return [
                'success' => true,
                'message' => 'Super Doctor demoted to Doctor'
            ];
        }

        
        return [
            'success' => false,
            'message' => 'Unexpected error',
            'code' => 500
        ];
    }
}
