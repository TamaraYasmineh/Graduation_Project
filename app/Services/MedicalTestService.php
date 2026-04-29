<?php

namespace App\Services;

use App\Models\MedicalTest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MedicalTestService
{
    public function upload($request)
    {
       $user = Auth::user();
        $file = $request->file('file');

        // تحديد النوع
        $ext = $file->extension();

        $type = match ($ext) {
            'jpg', 'jpeg', 'png' => 'image',
            'pdf' => 'pdf',
            default => 'doc',
        };

        // تخزين الملف
        $path = $file->store('medical_tests', 'public');

        // $test = MedicalTest::create([
        //     'medical_record_id' => $request->medical_record_id,
        //     'file_path' => $path,
        //     'file_type' => $type,
        //     'test_type' => $request->test_type,
        //     'notes' => $request->notes,
        // ]);

        // // ربط الرافع
        // $test->uploadable()->associate($user);
        // $test->save();
        $test = new MedicalTest([
    'medical_record_id' => $request->medical_record_id,
    'file_path' => $path,
    'file_type' => $type,
    'test_type' => $request->test_type,
    'notes' => $request->notes,
]);

$test->uploadable()->associate($user);

$test->save();

        return $test;

    }

    public function getPatientTests($patientId)
    {
         return MedicalTest::with('uploadable')
        ->whereHas('medicalRecord', function ($q) use ($patientId) {
            $q->where('patient_id', $patientId);
        })
        ->latest()
        ->get();
    }

    public function getByRecord($recordId)
    {
        return MedicalTest::with('uploadable')
            ->where('medical_record_id', $recordId)
            ->latest()
            ->get();
    }

    public function delete($id)
    {
      $test = MedicalTest::findOrFail($id);

        Storage::disk('public')->delete($test->file_path);

        $test->delete();
    }
}
