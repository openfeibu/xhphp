<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class TelecomAuthenticate
{

    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard('telecom')->guest()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('未登录', 401);
            } else {
                return redirect()->guest('telecomAdmin/login');
            }
        }
        return $next($request);
    }
}
