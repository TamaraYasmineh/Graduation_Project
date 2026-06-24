<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExternalConsultantRequest;
use App\Http\Requests\StoreInternalConsultantRequest;
use App\Http\Requests\UpdateConsultantRequest;
use App\Http\Resources\ConsultantResource;
use App\Models\Consultant;
use App\Models\Doctor;
use App\Models\ExternalDoctor;
use App\Services\ConsultantService;
use Illuminate\Http\Request;

class ConsultantController extends Controller
{
    public function addInternalDoctor(
        StoreInternalConsultantRequest $request,
        ConsultantService $service
    ) {
        try {

            $consultant =
                $service->createInternalConsultant(
                    $request->validated()
                );

            return response()->json([
                'message' => 'تم إضافة الاستشاري',

                'data' => new ConsultantResource(
                    $consultant
                ),
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function addExternalDoctor(
        StoreExternalConsultantRequest $request,
        ConsultantService $service
    ) {
        $consultant =
            $service->createExternalConsultant(
                $request->validated()
            );

        return response()->json([

            'message' => 'تم إضافة الاستشاري الخارجي',

            'data' => new ConsultantResource(
                $consultant
            ),
        ]);
    }


    public function index()
    {
        $consultants = Consultant::with('consultable')
            ->active()
            ->get();

        return ConsultantResource::collection($consultants);
    }

    public function getAllConsultantsForSD()
    {
        $consultants = Consultant::with('consultable')
            ->get();

        return ConsultantResource::collection($consultants);
    }

    public function update(
        UpdateConsultantRequest $request,
        int $consultantId,
        ConsultantService $service
    ) {

        try {

            $consultant = $service
                ->findConsultantOrFail($consultantId);

            $consultant = $service->update(
                $consultant,
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'message' => 'تم التعديل بنجاح',
                'data' => new ConsultantResource($consultant),
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }
    public function toggleStatus(int $consultantId)
    {
        $consultant = Consultant::findOrFail($consultantId);

        $consultant->update([
            'is_active' => !$consultant->is_active
        ]);

        return response()->json([
            'message' => $consultant->is_active
                ? 'Consultant activated'
                : 'Consultant deactivated',
            'is_active' => $consultant->is_active
        ]);
    }
    public function filter(Request $request)
{
    $query = Consultant::with('consultable');

    if ($request->has('is_active')) {

        $query->where(
            'is_active',
            filter_var(
                $request->is_active,
                FILTER_VALIDATE_BOOLEAN
            )
        );
    }

    return ConsultantResource::collection(
        $query->get()
    );
}
}
