<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class NgrokBypass
{
    public function handle(Request $request, Closure $next)
    {
        // يعمل فقط في بيئة local
        if (app()->environment('local')) {
            $request->headers->set('ngrok-skip-browser-warning', 'true');
        }

        return $next($request);
    }
}
