<?php

namespace App\Http\Controllers;

use App\Http\Requests\MedicalTestRequest;
use App\Http\Resources\MedicalTestResource;
use App\Models\MedicalRecord;
use App\Models\MedicalTest;
use App\Services\MedicalTestService;
use Illuminate\Support\Facades\Auth;

class MedicalTestController extends Controller
{
    private $service;

    public function __construct(MedicalTestService $service)
    {
        $this->service = $service;
    }

    public function uploadMedicalTest(MedicalTestRequest $request)
    {
        if (! Auth::user()->hasRole('patient')) {
            return response()->json([
                'status' => false,
                'message' => 'غير مصرح',
            ], 403);
        }
        $user = Auth::user();

        if (! $user->medicalRecord) {
            return response()->json([
                'status' => false,
                'message' => 'لا يوجد سجل طبي للمريض',
            ], 422);
        }

        $tests = $this->service->upload(
            $request,
            $user->medicalRecord
        );

        return response()->json([
            'status' => true,
            'message' => 'Uploaded successfully',
            'data' => MedicalTestResource::collection($tests),
        ]);
    }

    /**
     * السكرتيرة ترفع لمريض محدد
     */
    public function uploadTestBySecretary(
        MedicalTestRequest $request,
        $recordId
    ) {
        if (! Auth::user()->hasRole('secretary')) {
            return response()->json([
                'status' => false,
                'message' => 'غير مصرح',
            ], 403);
        }

        if (empty($recordId)) {
            return response()->json([
                'status' => false,
                'message' => 'معرف السجل الطبي مطلوب',
            ], 422);
        }

        $record = MedicalRecord::query()->find($recordId);

        if (! $record) {
            return response()->json([
                'status' => false,
                'message' => 'لا يوجد سجل طبي بهذا الرقم',
            ], 404);
        }

        $tests = $this->service->upload($request, $record);

        return response()->json([
            'status' => true,
            'message' => 'Uploaded successfully',
            'data' => MedicalTestResource::collection($tests),
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
            'message' => 'Deleted successfully',
        ]);
    }

    public function downloadMedicalTest($id)
    {
        $test = MedicalTest::findOrFail($id);

        $path = storage_path('app/public/'.$test->file_path);

        if (! file_exists($path)) {
            abort(404);
        }

        return response()->download(
            $path,
            basename($path)
        );

        // $test = MedicalTest::findOrFail($id);

        // dd([
        //     'id' => $test->id,
        //     'file_path' => $test->file_path,
        //     'full_path' => storage_path('app/public/' . $test->file_path),
        //     'exists' => file_exists(storage_path('app/public/' . $test->file_path)),
        // ]);

        // return response()->json([
        //     'id' => $test->id,
        //     'path' => $path,
        //     'exists' => file_exists($path),
        //     'size' => filesize($path),
        // ]);
    }

    public function viewMedicalTest($id)
    {
        $test = MedicalTest::findOrFail($id);

        $path = storage_path('app/public/'.$test->file_path);

        if (! file_exists($path)) {
            abort(404);
        }

        return response()->file($path);
    }
}
