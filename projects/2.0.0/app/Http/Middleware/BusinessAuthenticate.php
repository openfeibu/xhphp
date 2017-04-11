<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class BusinessAuthenticate
{

    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard('business')->guest()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('未登录', 401);
            } else {
                return redirect()->guest('business/login');
            }
        }
        return $next($request);
    }
}
