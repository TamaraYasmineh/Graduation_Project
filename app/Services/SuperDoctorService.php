<?php
namespace App\Services;
namespace App\Services;

use App\Models\Doctor;

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
}
