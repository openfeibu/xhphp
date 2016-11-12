<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\UserService;

class UserAuthenticate
{

    protected $user;

    function __construct(UserService $user)
    {
        $this->user = $user;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->token !== '0') {
            $this->user->tokenAuth($request->token);
        }

        return $next($request);
    }
}
