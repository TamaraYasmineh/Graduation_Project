<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class BaseController extends Controller
{
    public function sendResponse($result, $message , $status = 200): JsonResponse
    {
         $data = [
            'success' => true,
            'message' => $message,
            'data' => $result,
            'errors' => null,
            'status' => $status,

        ];
        return response()->json($data);

    }

    public function sendError($error, $message = [], $status = 401): JsonResponse
    {
       $data = [
            'success' => false,
            'message' => $message,
            'data' => null,
            'error' => $error,
            'status' => $status,
        ];
        return response()->json($data);

    }
}
