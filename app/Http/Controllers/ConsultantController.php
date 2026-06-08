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
    // public function addInternalDoctor(Request $request)
    // {
    //     $request->validate([
    //         'doctor_id' => 'required|exists:doctors,id',
    //         'consultation_fee' => 'required|numeric|min:1',
    //         'whatsapp_number' => 'required',
    //     ]);

    //     $doctor = Doctor::findOrFail(
    //         $request->doctor_id
    //     );

    //     if ($doctor->consultant) {
    //         return response()->json([
    //             'message' => 'هذا الطبيب استشاري مسبقاً'
    //         ], 422);
    //     }

    //     $consultant = $doctor->consultant()->create([
    //         'consultation_fee' => $request->consultation_fee,
    //         'whatsapp_number' => $request->whatsapp_number,
    //         'is_active' => true,
    //     ]);

    //     return response()->json([
    //         'message' => 'تم إضافة الاستشاري',
    //         'data' => $consultant
    //     ]);
    // }
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
    // public function addExternalDoctor(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required',
    //         'phone' => 'required',
    //         'specialization' => 'required',
    //         'consultation_fee' => 'required|numeric|min:1',
    //         'whatsapp_number' => 'required',
    //         'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'

    //     ]);
    //     $imagePath = null;

    //     if ($request->hasFile('profile_image')) {

    //         $imagePath = $request
    //             ->file('profile_image')
    //             ->store('consultants', 'public');
    //     }
    //     $externalDoctor = ExternalDoctor::create([
    //         'name' => $request->name,
    //         'phone' => $request->phone,
    //         'specialization' => $request->specialization,
    //         'years_of_experience' => $request->years_of_experience,
    //         'license_number' => $request->license_number,
    //         'bio' => $request->bio,
    //         'profile_image' => $imagePath,

    //     ]);

    //     $consultant = $externalDoctor
    //         ->consultant()
    //         ->create([
    //             'consultation_fee' => $request->consultation_fee,
    //             'whatsapp_number' => $request->whatsapp_number,
    //             'is_active' => true
    //         ]);

    //     return response()->json([
    //         'message' => 'تم إضافة الاستشاري الخارجي',
    //         'data' => $consultant
    //     ]);
    // }
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
        $consultants = Consultant::with(
            'consultable'
        )
            ->where('is_active', true)
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
}
