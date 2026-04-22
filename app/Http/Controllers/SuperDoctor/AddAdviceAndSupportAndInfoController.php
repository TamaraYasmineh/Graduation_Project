<?php

namespace App\Http\Controllers\SuperDoctor;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdviceRequest;
use App\Http\Requests\StoreCenterInfoRequest;
use App\Http\Requests\StoreSupportRequest;
use App\Http\Requests\UpdateAdviceRequest;
use App\Http\Requests\UpdateCenterInfoRequest;
use App\Http\Requests\UpdateSupportRequest;
use App\Http\Resources\AdviceResource;
use App\Http\Resources\CenterInfoResource;
use App\Http\Resources\PsychologicalSupportResource;
use App\Services\AdviceService;
use App\Services\CenterInfoService;
use App\Services\PsychologicalSupportService;
use Illuminate\Http\Request;
use App\Services\FirebaseService;
class AddAdviceAndSupportAndInfoController extends Controller
{
    protected $adviceService;
    protected $service;
    protected $supportService;

    public function __construct(AdviceService $adviceService, CenterInfoService $service, PsychologicalSupportService $supportService)
    {
        $this->adviceService = $adviceService;
        $this->service = $service;
        $this->supportService = $supportService;
    }

    public function storeAdvices(StoreAdviceRequest $request)
    {
        $result = $this->adviceService->store(
            $request->validated(),
            $request->user()
        );

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], $result['code']);
        }

        return response()->json([
            'success' => true,
            'data' => new AdviceResource($result['data']),
            'message' => 'Advice created successfully'
        ]);
    }

    public function getAdvicesForPatientsAndSuper(Request $request)
    {
        $result = $this->adviceService->getAdvices($request->user());

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], $result['code']);
        }

        return response()->json([
            'success' => true,
            'data' => AdviceResource::collection($result['data'])
        ]);
    }
    public function destroyAdvice($id, Request $request)
    {
        $result = $this->adviceService->deleteAdvice($id, $request->user());

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], $result['code']);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message']
        ]);
    }


    //store center's info

    public function storeCenterInformation(StoreCenterInfoRequest $request)
    {
        $result = $this->service->storeCenterInfo(
            $request->validated(),
            $request->user()
        );

        if (!$result['success']) {
            return response()->json($result, $result['code']);
        }

        return response()->json([
            'success' => true,
            'data' => $result['data'],
            'message' => 'Center info created'
        ]);
    }

    //  تعديل
    public function updateCenterInformation($id, UpdateCenterInfoRequest $request)
    {
        $result = $this->service->updateCenterInfo(
            $id,
            $request->validated(),
            $request->user()
        );

        if (!$result['success']) {
            return response()->json($result, $result['code']);
        }

        return response()->json([
            'success' => true,
            'data' => $result['data'],
            'message' => 'Center info updated'
        ]);
    }
    //عرض
    public function showCenterInformation()
    {
        $result = $this->service->getAllCenters();

        return response()->json([
            'success' => true,
            'data' => CenterInfoResource::collection($result['data'])
        ]);
    }
    //psychological support
    // public function storePsychologicalSupport(StoreSupportRequest $request)
    // {
    //     $result = $this->supportService->store($request->validated(), $request->user());

    //     if (!$result['success']) {
    //         return response()->json($result, $result['code']);
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'data' => new PsychologicalSupportResource($result['data'])
    //     ]);
    // }
    public function storePsychologicalSupport(
        StoreSupportRequest $request,
        FirebaseService $firebase
    ) {
        $result = $this->supportService->store(
            $request->validated(),
            $request->user()
        );
    
        if (!$result['success']) {
            return response()->json($result, $result['code']);
        }
    
        $patients = \App\Models\User::role('patient')->get(); // 👈 كل المرضى
        $responses = [];
        foreach ($patients as $patient) {
        $tokens = $patient->deviceTokens()->pluck('token');
        foreach ($tokens as $token) {
        $responses[] = $firebase->sendNotification(
            $token,
            ' فهيا يابطل فريقنا جاهز لدعمك',
            'رسالة جديدة'
        );
    }
}
    
        return response()->json([
            'success' => true,
            'data' => new PsychologicalSupportResource($result['data']),
            'notifications' => $responses
        ]);
    }
//     public function storePsychologicalSupport(
//         StoreSupportRequest $request,
//         FirebaseService $firebase
//     ) {
//         $result = $this->supportService->store(
//             $request->validated(),
//             $request->user()
//         );
    
//         if (!$result['success']) {
//             return response()->json($result, $result['code']);
//         }
    
//         $patientId = $result['data']->user_id;

// $patient = \App\Models\User::find($patientId);

// if (!$patient) {
//     return response()->json([
//         'error' => 'Patient not found'
//     ]);
// }

// $tokens = $patient->deviceTokens()->pluck('token');

// $responses = [];

// foreach ($tokens as $token) {
//     $responses[] = $firebase->sendNotification(
//         $token,
//         'تم استلام طلب الدعم النفسي',
//         'سيتم التواصل معك قريباً'
//     );
// }
    
//         return response()->json([
//             'success' => true,
//             'data' => new PsychologicalSupportResource($result['data']),
//             'notifications' => $responses
//         ]);
//     }
//     //  تعديل
//     public function updatePsychologicalSupport($id, UpdateSupportRequest $request)
//     {
//         $result = $this->supportService->update($id, $request->validated(), $request->user());

//         if (!$result['success']) {
//             return response()->json($result, $result['code']);
//         }

//         return response()->json([
//             'success' => true,
//             'data' => new PsychologicalSupportResource($result['data'])
//         ]);
//     }

    //  حذف
    public function destroyPsychologicalSupport($id, Request $request)
    {
        $result = $this->supportService->delete($id, $request->user());

        if (!$result['success']) {
            return response()->json($result, $result['code']);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message']
        ]);
    }

    //  عرض
    public function showPsychologicalSupport()
    {
        return response()->json([
            'success' => true,
            'data' => PsychologicalSupportResource::collection(
                $this->supportService->show()
            )
        ]);
    }

    public function updateAdvice($id, UpdateAdviceRequest $request)
    {
        $result = $this->adviceService->updateAdvice(
            $id,
            $request->validated(),
            $request->user()
        );

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], $result['code']);
        }

        return response()->json([
            'success' => true,
            'data' => new AdviceResource($result['data']),
            'message' => 'Advice updated successfully'
        ]);
    }
}
