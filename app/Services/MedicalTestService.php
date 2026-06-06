<?php

namespace App\Services;

use App\Models\MedicalRecord;
use App\Models\MedicalTest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MedicalTestService
{

public function upload($request, MedicalRecord $record)
{
     if (!$record->exists) {
        throw new \Exception('Medical record not found');
    }
   $user = Auth::user();

    // if (!$user->medicalRecord) {
    //     throw new \Exception('لا يوجد سجل طبي لهذا المستخدم');
    // }

    $tests = [];

    foreach ($request->file('files') as $file) {

        $ext = strtolower($file->extension());

        $type = match ($ext) {
            'jpg', 'jpeg', 'png', 'webp' => 'image',
            'pdf' => 'pdf',
            default => 'doc',
        };

        $path = $file->store('medical_tests', 'public');

        $tests[] = MedicalTest::create([
            'medical_record_id' => $record->id,
            'uploadable_id' => $user->id,
            'uploadable_type' => $user::class,
            'file_path' => $path,
            'file_type' => $type,
            'test_type' => $request->test_type,
            'notes' => $request->notes,
        ]);
    }

    return collect($tests);
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
