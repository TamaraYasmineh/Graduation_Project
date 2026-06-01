<?php

namespace App\Http\Controllers;
use App\Http\Requests\CalculateBsaRequest;
use App\Http\Resources\BsaResource;
use App\Services\SessionService;
use Illuminate\Http\Request;
use App\Models\Treatment_plan;
use App\Models\Treatment_session;
use App\Http\Requests\CalculateDoseRequest;
use App\Http\Requests\TreatmentPlanRequest;
use App\Http\Resources\TreatmentPlanResource;
use App\Http\Requests\TreatmentSessionRequest;
use App\Http\Resources\TreatmentSessionResource;
class SessionController extends BaseController
{
    private SessionService $bsaService;
    private SessionService $treatmentPlanService;
    private SessionService $treatmentPlan;
    private SessionService $treatmentSessionService;
    public function __construct(SessionService $bsaService,SessionService $treatmentPlanService,SessionService $treatmentPlan,SessionService $treatmentSessionService)
    {
        $this->bsaService = $bsaService;
        $this->treatmentPlanService = $treatmentPlanService;
        $this->treatmentPlan = $treatmentPlan;
        $this->treatmentSessionService = $treatmentSessionService;
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
    public function storeTretmentPlane(TreatmentPlanRequest $request)
    {
        $plan = $this->treatmentPlanService
            ->storeTreatmentPlan($request->validated());

            return $this->sendResponse(
                new TreatmentPlanResource($plan),
                'تم تخزين خطة العلاج بنجاح'
            );
    }
    public function storeTreatmentSession(
        TreatmentSessionRequest $request
    ) {
    
        $session = $this->treatmentSessionService
            ->storeSession($request->validated());
    
        return $this->sendResponse(
            new TreatmentSessionResource($session),
            'تم تخزين الجلسة بنجاح'
        );
    }
    public function updateTretmentPlane(Request $request, $id)
    {
        $treatmentPlan = Treatment_plan::findOrFail($id);
        $updated = $treatmentPlan->update($treatmentPlan, $request->all());
        return $this->sendResponse(
            $updated,
             'تم تعديل خطة العلاج بنجاح'
        );
    }
    public function updateTreatmentSession(Request $request, $id)
    {
        $treatmentSession = Treatment_session::findOrFail($id);
        $updated = $treatmentSession->update($treatmentSession, $request->all());
        return $this->sendResponse(
            $updated,
            'تم تعديل الجلسة بنجاح'
        );
    }
    }

