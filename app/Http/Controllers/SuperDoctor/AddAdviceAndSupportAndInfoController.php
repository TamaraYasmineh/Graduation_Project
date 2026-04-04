<?php

namespace App\Http\Controllers\SuperDoctor;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdviceRequest;
use App\Http\Resources\AdviceResource;
use App\Services\AdviceService;
use Illuminate\Http\Request;

class AddAdviceAndSupportAndInfoController extends Controller
{
    protected $adviceService;

    public function __construct(AdviceService $adviceService)
    {
        $this->adviceService = $adviceService;
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
}
