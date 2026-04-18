<?php

namespace App\Http\Controllers\SuperDoctor;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FirebaseService;
use App\Services\UserService;
use Illuminate\Http\Request;

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

// public function approveUser($id, Request $request,FirebaseService $firebase)
// {
//     $result = $this->userService->approveUser($id, $request->user());

//     if (!$result['success']) {
//         return response()->json($result, $result['code']);
//     }
//  $user = User::find($id);
//   if ($user) {
//         $tokens = $user->deviceTokens->pluck('token');

//         foreach ($tokens as $token) {
//             $firebase->sendNotification(
//                 $token,
//                 'تم تفعيل حسابك',
//                 'يمكنك الآن تسجيل الدخول'
//             );
//         }
//     }

//     return response()->json([
//         'success' => true,
//         'message' => $result['message']
//     ]);
// }
public function approveUser($id, Request $request, FirebaseService $firebase)
{
    $user = User::find($id);

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found'
        ], 404);
    }

    // 🔥 تحقق إذا هو approved مسبقاً
    if ($user->status === 'approved') {
        return response()->json([
            'success' => false,
            'message' => 'الحساب مقبول مسبقاً'
        ], 400);
    }

    // تنفيذ الموافقة
    $result = $this->userService->approveUser($id, $request->user());

    if (!$result['success']) {
        return response()->json($result, $result['code']);
    }

    // 🔔 إرسال إشعار
    $tokens = $user->deviceTokens()->pluck('token');

    if ($tokens->isNotEmpty()) {
        foreach ($tokens as $token) {
            $firebase->sendNotification(
                $token,
                'تم تفعيل حسابك ✅',
                'يمكنك الآن تسجيل الدخول'
            );
        }
    }

    return response()->json([
        'success' => true,
        'message' => $result['message']
    ]);
}
// public function rejectUser($id, Request $request,FirebaseService $firebase)
// {
//     $result = $this->userService->rejectUser($id, $request->user());

//     if (!$result['success']) {
//         return response()->json($result, $result['code']);
//     }
//      $user = User::find($id);

//     if ($user) {
//         $tokens = $user->deviceTokens->pluck('token');

//         foreach ($tokens as $token) {
//             $firebase->sendNotification(
//                 $token,
//                 'تم رفض حسابك ',
//                 'يرجى التواصل مع الإدارة'
//             );
//         }
//     }


//     return response()->json([
//         'success' => true,
//         'message' => $result['message']
//     ]);
// }

public function rejectUser($id, Request $request, FirebaseService $firebase)
{
    $user = User::find($id);

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found'
        ], 404);
    }

    // 🔥 إذا مرفوض مسبقاً
    if ($user->status === 'rejected') {
        return response()->json([
            'success' => false,
            'message' => 'الحساب مرفوض مسبقاً'
        ], 400);
    }

    $result = $this->userService->rejectUser($id, $request->user());

    if (!$result['success']) {
        return response()->json($result, $result['code']);
    }

    $tokens = $user->deviceTokens()->pluck('token');

    if ($tokens->isNotEmpty()) {
        foreach ($tokens as $token) {
            $firebase->sendNotification(
                $token,
                'تم رفض حسابك ❌',
                'يرجى التواصل مع الإدارة'
            );
        }
    }

    return response()->json([
        'success' => true,
        'message' => $result['message']
    ]);
}
}
