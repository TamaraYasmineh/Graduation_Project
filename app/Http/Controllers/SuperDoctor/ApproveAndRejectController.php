<?php

namespace App\Http\Controllers\SuperDoctor;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
class ApproveAndRejectController extends BaseController
{
  protected $userService;


    public function __construct(UserService $userService)
    {
        $this->userService = $userService;

    }


    public function getPendingUsers(Request $request)
    {
        $result = $this->userService->getPendingUsers($request->user());
    
        if (!$result['success']) {
            return $this->sendError(
                $result['message'],
                [],
                $result['code']
            );
        }
    
        return $this->sendResponse(
            $result['data'],
            $result['message'] ?? 'Pending users fetched successfully'
        );
    }
    public function getRejectedUsers(Request $request)
    {
        $result = $this->userService->getRejectedUsers($request->user());
    
        if (!$result['success']) {
            return $this->sendError(
                $result['message'],
                [],
                $result['code']
            );
        }
    
        return $this->sendResponse(
            $result['data'],
            $result['message']
        );
    }

    public function getApprovedUsers(Request $request)
{
    $result = $this->userService->getApprovedUsers($request->user());

    if (!$result['success']) {
        return $this->sendError(
            $result['message'],
            [],
            $result['code']
        );
    }

    return $this->sendResponse(
        $result['data'],
        $result['message']
    );
}
public function getSuperDoctors(Request $request)
{
    $result = $this->userService->getSuperDoctors($request->user());

    if (!$result['success']) {
        return $this->sendError($result['message'], [], $result['code']);
    }

    return $this->sendResponse($result['data'], $result['message']);
}
    
public function approveUser($id, Request $request)
{
    $result = $this->userService->approveUser($id, $request->user());

    if (!$result['success']) {
        return response()->json($result, $result['code']);
    }

    return response()->json([
        'success' => true,
        'message' => $result['message']
    ]);
}

public function rejectUser($id, Request $request)
{
    $result = $this->userService->rejectUser($id, $request->user());

    if (!$result['success']) {
        return response()->json($result, $result['code']);
    }

    return response()->json([
        'success' => true,
        'message' => $result['message']
    ]);
}
}
