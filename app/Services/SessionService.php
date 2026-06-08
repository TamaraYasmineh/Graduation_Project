<?php

namespace App\Services;

use App\Models\Treatment_plan;
use App\Models\Treatment_session;

class SessionService
{
    /* -----------------------------
     * WEIGHT NORMALIZATION
     * -----------------------------*/
    public function normalizeWeight(float $weight, string $unit): float
    {
        return match ($unit) {
            'lbs' => $weight * 0.453592, // lbs → kg
            default => $weight, // kg
        };
    }

    /* -----------------------------
     * HEIGHT NORMALIZATION
     * -----------------------------*/
    public function normalizeHeight(float $height, string $unit): float
    {
        return match ($unit) {
            'm' => $height * 100,       // m → cm
            'in' => $height * 2.54,     // inches → cm
            default => $height,         // cm
        };
    }

    /* -----------------------------
     * DOSE NORMALIZATION → mg/m²
     * -----------------------------*/
    public function normalizeDose(float $dose, string $unit): float
    {
        return match ($unit) {
            'g/m2' => $dose * 1000,       // g → mg
            'mcg/m2' => $dose / 1000,     // mcg → mg
            default => $dose,             // mg/m2
        };
    }

    /* -----------------------------
     * BSA CALCULATION
     * -----------------------------*/
    public function calculateBsa(float $weight, float $height, string $formula): float
    {
        return match ($formula) {
            'dubois' => $this->dubois($weight, $height),
            default => $this->mosteller($weight, $height),
        };
    }

    private function mosteller($w, $h): float
    {
        return sqrt(($w * $h) / 3600);
    }

    private function dubois($w, $h): float
    {
        return 0.007184 * pow($h, 0.725) * pow($w, 0.425);
    }

    /* -----------------------------
     * FINAL DOSE
     * -----------------------------*/
    public function calculateDose(float $bsa, float $dosePerM2, string $unit): array
    {
        $dose = $bsa * $dosePerM2;

        return match ($unit) {
            'g' => [
                'dose' => round($dose / 1000, 4),
                'unit' => 'g',
            ],
            'mcg' => [
                'dose' => round($dose * 1000, 2),
                'unit' => 'mcg',
            ],
            default => [
                'dose' => round($dose, 2),
                'unit' => 'mg',
            ],
        };
    }

    public function storeTreatmentPlan($data)
    {
        return Treatment_plan::create([
            'medical_record_id' => $data['medical_record_id'],
            'diagnosis' => $data['diagnosis'],
            'session_date' => $data['session_date'],
            'protocol_id' => $data['protocol_id'] ?? null,
            'medication' => $data['medication'] ?? null,
            'duration' => $data['duration'] ?? null,
        ]);
    }

    public function storeSession($data)
    {
        return Treatment_session::create([

            'treatment_plan_id' => $data['treatment_plan_id'],

            'session_date' => $data['session_date'],

            'height' => $data['height'] ?? null,

            'weight' => $data['weight'] ?? null,

            'bsa' => $data['bsa'] ?? null,

            'dosage' => $data['dosage'] ?? null,

            'lab_requested' => $data['lab_requested'] ?? false,

            'lab_tests_requested' => $data['lab_tests_requested'] ?? null,

            'lab_results' => $data['lab_results'] ?? null,

            'notes' => $data['notes'] ?? null,
        ]);
    }

    public function update(Treatment_plan $TreatmentPlan, array $data)
    {
        $TreatmentPlan->update([
            'medical_record_id' => $data['medical_record_id'] ?? $TreatmentPlan->medical_record_id,

            'diagnosis' => $data['diagnosis'] ?? $TreatmentPlan->diagnosis,
            'session_date' => $data['session_date'] ?? $TreatmentPlan->session_date,

            'protocol_id' => $data['protocol_id'] ?? $TreatmentPlan->protocol_id,
            'medication' => $data['medication'] ?? $TreatmentPlan->medication,
            'duration' => $data['duration'] ?? $TreatmentPlan->duration,
        ]);

        return $TreatmentPlan;
    }

    public function updatetreatmentSession(Treatment_session $Treatment_session, array $data)
    {
        $Treatment_session->update([
            'treatment_plan_id' => $data['treatment_plan_id'] ?? $Treatment_session->treatment_plan_id,
            'session_date' => $data['session_date'] ?? $Treatment_session->session_date,
            'height' => $data['height'] ?? $Treatment_session->height,
            'weight' => $data['weight'] ?? $Treatment_session->weight,
            'bsa' => $data['bsa'] ?? $Treatment_session->bsa,
            'dosage' => $data['dosage'] ?? $Treatment_session->dosage,
            'lab_requested' => $data['lab_requested'] ?? $Treatment_session->lab_requested,
            'lab_tests_requested' => $data['lab_tests_requested'] ?? $Treatment_session->lab_tests_requested,
            'lab_results' => $data['lab_results'] ?? $Treatment_session->lab_results,
            'notes' => $data['notes'] ?? $Treatment_session->notes,
        ]);

        return $Treatment_session;
    }

    public function getAllTreatmentPlans()
    {
        return Treatment_plan::all();
    }

    public function getTreatmentPlanById($id)
    {
        return Treatment_plan::findOrFail($id);
    }

    public function deleteTreatmentPlan(Treatment_plan $treatmentPlan)
    {
        $treatmentPlan->delete();

        return true;
    }

    public function getAllTreatmentSessions()
    {
        return Treatment_session::all();
    }

    public function getTreatmentSessionById($id)
    {
        return Treatment_session::findOrFail($id);
    }

    public function deleteTreatmentSession(Treatment_session $treatmentSession)
    {
        $treatmentSession->delete();

        return true;
    }
}
