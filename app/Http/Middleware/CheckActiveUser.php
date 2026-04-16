<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
class CheckActiveUser
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        if ($user->status !== 'approved') {

            return response()->json([
                'message' => match($user->status) {
                    'pending' => 'حسابك قيد المراجعة من قبل الإدارة',
                    'rejected' => 'تم رفض حسابك، يرجى التواصل مع الإدارة',
                    default => 'غير مصرح'
                }
            ], 403);
        }

        return $next($request);
    }
}
