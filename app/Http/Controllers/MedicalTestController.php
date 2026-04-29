<?php

namespace App\Http\Controllers;

use App\Http\Requests\MedicalTestRequest;
use App\Http\Resources\MedicalTestResource;
use App\Services\MedicalTestService;
use Illuminate\Http\Request;

class MedicalTestController extends Controller
{
    private $service;

    public function __construct(MedicalTestService $service)
    {
        $this->service = $service;
    }

    public function uploadMedicalTest(MedicalTestRequest $request)
    {
        $test = $this->service->upload($request);

        return response()->json([
            'status' => true,
            'message' => 'Uploaded successfully',
            'data' => new MedicalTestResource($test)
        ]);
    }

    public function getPatientMedicalTests($id)
    {
        $tests = $this->service->getPatientTests($id);

        return MedicalTestResource::collection($tests);
    }

    public function getByRecord($id)
    {
       return MedicalTestResource::collection(
            $this->service->getByRecord($id)
        );
    }

    public function deleteMedicalTest($id)
    {
        $this->service->delete($id);

        return response()->json([
            'status' => true,
            'message' => 'Deleted successfully'
        ]);
    }
}
