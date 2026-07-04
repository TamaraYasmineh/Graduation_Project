<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ArchivePatientRequest;
use App\Services\PatientArchiveService;
class PatientArchiveController extends BaseController
{
    protected $service;

    public function __construct(PatientArchiveService $service)
    {
        $this->service = $service;
    }

    public function archive(int $patientId, ArchivePatientRequest $request)
    {
        $result = $this->service->archivePatient(
            $patientId,
            $request->validated(),
            $request->user()
        );

        if (! $result['success']) {
            return $this->sendError($result['message'], [], $result['code']);
        }

        return $this->sendResponse($result['data'], $result['message'], 201);
    }

    public function unarchive(int $patientId, Request $request)
    {
        $result = $this->service->unarchivePatient($patientId, $request->user());

        if (! $result['success']) {
            return $this->sendError($result['message'], [], $result['code']);
        }

        return $this->sendResponse(null, $result['message']);
    }

    public function index(Request $request)
    {
        $filters = $request->only(['reason', 'search', 'per_page']);
        $result = $this->service->getArchivedPatients($filters);

        return $this->sendResponse(
            $result['data'], 
            'Archived patients retrieved successfully'
        );
    }


    public function statistics()
    {
        $result = $this->service->getArchiveStatistics();

        return $this->sendResponse(
            $result['data'], 
            'Archive statistics retrieved successfully'
        );
    }
}
