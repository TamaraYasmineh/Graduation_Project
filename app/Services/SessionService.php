<?php

namespace App\Services;

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
                'unit' => 'g'
            ],
            'mcg' => [
                'dose' => round($dose * 1000, 2),
                'unit' => 'mcg'
            ],
            default => [
                'dose' => round($dose, 2),
                'unit' => 'mg'
            ],
        };
    }
}