<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\PatientArchive;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PatientArchiveService
{
    public function archivePatient(int $patientId, array $data, User $user)
    {
        $patient = Patient::find($patientId);

        if (! $patient) {
            return [
                'success' => false,
                'message' => 'Patient not found',
                'code' => 404,
            ];
        }

        // تحقق من أن المريض غير مؤرشف مسبقاً
        if ($patient->isArchived()) {
            return [
                'success' => false,
                'message' => 'Patient is already archived',
                'code' => 400,
            ];
        }

        DB::beginTransaction();

        try {
            $archive = PatientArchive::create([
                'patient_id' => $patientId,
                'archived_by' => $user->id,
                'reason' => $data['reason'],
                'note' => $data['note'] ?? null,
                'archived_at' => now(),
                'is_active' => true,
            ]);

            DB::commit();

            return [
                'success' => true,
                'data' => $archive->load(['patient', 'archivedBy']),
                'message' => 'Patient archived successfully',
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Failed to archive patient: ' . $e->getMessage(),
                'code' => 500,
            ];
        }
    }

    public function unarchivePatient(int $patientId, User $user)
    {
        $archive = PatientArchive::where('patient_id', $patientId)
            ->where('is_active', true)
            ->first();

        if (! $archive) {
            return [
                'success' => false,
                'message' => 'Patient is not archived',
                'code' => 404,
            ];
        }

        $archive->update(['is_active' => false]);

        return [
            'success' => true,
            'message' => 'Patient unarchived successfully',
        ];
    }

    public function getArchivedPatients(array $filters = [])
    {
        $query = PatientArchive::with(['patient', 'archivedBy'])
            ->active()
            ->latest('archived_at');

        // فلترة حسب السبب
        if (isset($filters['reason'])) {
            $query->byReason($filters['reason']);
        }

        // بحث بالاسم
        if (isset($filters['search'])) {
            $query->whereHas('patient', function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%');
            });
        }

        $archives = $query->paginate($filters['per_page'] ?? 15);

        return [
            'success' => true,
            'data' => $archives,
        ];
    }

    public function getArchiveStatistics()
    {
        $totalArchived = PatientArchive::active()->count();
        $recovered = PatientArchive::active()->byReason('recovered')->count();
        $death = PatientArchive::active()->byReason('death')->count();
        $followUpEnded = PatientArchive::active()->byReason('follow_up_ended')->count();

        return [
            'success' => true,
            'data' => [
                'total_archived' => $totalArchived,
                'recovered' => $recovered,
                'death' => $death,
                'follow_up_ended' => $followUpEnded,
            ],
        ];
    }
}