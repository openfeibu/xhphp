<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class MobileBusinessAuthenticate
{

    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard('business')->guest()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('未登录', 401);
            } else {
                return redirect()->guest('mbusiness/login');
            }
        }
        return $next($request);
    }
}
