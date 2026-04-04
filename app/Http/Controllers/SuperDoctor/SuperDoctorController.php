<?php

namespace App\Http\Controllers\SuperDoctor;

use App\Http\Controllers\Controller;
use App\Http\Resources\SuperDoctorResource;
use App\Services\SuperDoctorService;
use Illuminate\Http\Request;

class SuperDoctorController extends Controller
{
    protected $doctorService;

    public function __construct(SuperDoctorService $doctorService)
    {
        $this->doctorService = $doctorService;
    }

    public function getDoctors()
    {
        $doctors = $this->doctorService->getAllDoctors();

        return response()->json([
            'success' => true,
            'data' => SuperDoctorResource::collection($doctors)
        ]);
    }

    public function getDoctorsWithSpecialization(Request $request)
    {
        $doctors = $this->doctorService->getAllDoctorsWithSpecialization($request->specialization);

        return response()->json([
            'success' => true,
            'data' => SuperDoctorResource::collection($doctors)
        ]);
    }
}
