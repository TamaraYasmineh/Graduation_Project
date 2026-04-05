<?php

namespace App\Http\Controllers\SuperDoctor;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;

class ApproveAndRejectController extends Controller
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
        return response()->json($result, $result['code']);
    }

    return response()->json([
        'success' => true,
        'data' => $result['data']
    ]);
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
