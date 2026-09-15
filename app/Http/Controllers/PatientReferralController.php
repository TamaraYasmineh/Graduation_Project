<?php

namespace App\Http\Controllers;
use App\Http\Controllers\BaseController;
use App\Http\Requests\StoreReferralRequest;
use App\Http\Resources\PatientReferralResource;
use App\Models\Doctor;
use App\Services\PatientReferralService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PatientReferralController extends BaseController
{
    protected $service;

    public function __construct(PatientReferralService $service)
    {
        $this->service = $service;
    }

    public function refer(Request $request, int $patient)
    {
        $data = $request->only([
            'type',
            'reason',
            'notes',
            'referred_to_doctor_id',
            'external_center_name',
            'external_center_phone',
            'external_center_address',
        ]);

        $doctor = Doctor::where('user_id', auth()->id())->first();

        if (! $doctor) {
            return response()->json([
                'success' => false,
                'message' => 'Only doctors can refer patients',
            ], 403);
        }

        $result = $this->service->referPatient($patient, $data, $doctor);

         if (! $result['success']) {
        return response()->json($result, $result['code'] ?? 500);
    }

    return response()->json([
        'success' => true,
        'data' => new PatientReferralResource($result['referral']),
        'message' => $result['message'],
    ], 200);
    }
    public function updateStatus(int $referralId, Request $request)
    {
        $request->validate([
            'status' => 'required|in:pending,accepted,rejected,completed',
        ]);

        $result = $this->service->updateReferralStatus(
            $referralId,
            $request->status,
            $request->user()
        );

        if (! $result['success']) {
            return $this->sendError($result['message'], [], $result['code']);
        }

        return $this->sendResponse(
            $result['data'],
            $result['message'] ?? 'Referral status updated successfully'
        );
    }

    public function index(Request $request)
    {
        $filters = $request->only(['type', 'status', 'search', 'per_page']);
        $result = $this->service->getReferrals($filters);

        return $this->sendResponse(
            $result['data'],
            'Referrals retrieved successfully'
        );
    }

    public function statistics()
    {
        $result = $this->service->getReferralStatistics();

        return $this->sendResponse(
            $result['data'],
            'Referral statistics retrieved successfully'
        );
    }

    public function getDoctors()
    {
        $result = $this->service->getCenterDoctors();

        return $this->sendResponse(
            $result['data'],
            'Doctors retrieved successfully'
        );
    }
}
