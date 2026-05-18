<?php

namespace App\Http\Controllers;
use App\Http\Requests\CalculateBsaRequest;
use App\Http\Resources\BsaResource;
use App\Services\SessionService;
use Illuminate\Http\Request;
use App\Http\Requests\CalculateDoseRequest;

class SessionController extends BaseController
{
    private SessionService $bsaService;
    private SessionService $doseService;
    public function __construct(SessionService $bsaService)
    {
        $this->bsaService = $bsaService;
    }
    public function calculateBsa(CalculateBsaRequest $request)
{
    // normalize inputs
    $weight = $this->bsaService->normalizeWeight(
        $request->weight,
        $request->weight_unit
    );

    $height = $this->bsaService->normalizeHeight(
        $request->height,
        $request->height_unit
    );

    $dosePerM2 = $this->bsaService->normalizeDose(
        $request->bsa_based_dose,
        $request->dose_unit
    );

    $formula = $request->formula;
    $unit = $request->desired_unit;

    // Step 1: BSA
    $bsa = $this->bsaService->calculateBsa($weight, $height, $formula);

    // Step 2: Dose
    $doseResult = $this->bsaService->calculateDose($bsa, $dosePerM2, $unit);

    return $this->sendResponse([
        'weight' => $weight,
        'height' => $height,
        'formula' => $formula,
        'bsa' => round($bsa, 2),
        'bsa_based_dose_mg_m2' => $dosePerM2,
        'final_dose' => $doseResult['dose'],
        'unit' => $doseResult['unit'],
    ], 'Dose calculated successfully');
}
    }

