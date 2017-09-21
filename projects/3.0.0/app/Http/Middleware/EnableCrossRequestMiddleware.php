<?php

namespace App\Http\Middleware;

use Log;
use Closure;

class EnableCrossRequestMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        if(is_array($response)){
	        $response = response()->json($response);
        }
		$origin = isset($_SERVER['HTTP_ORIGIN'])? $_SERVER['HTTP_ORIGIN'] : '';   
		if(in_array($origin, config('app.allow_origin'))){  
			$response->header('Access-Control-Allow-Origin',  $origin);
			$response->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Cookie, Accept, multipart/form-data, application/json');
			$response->header('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, OPTIONS');
			$response->header('Access-Control-Allow-Credentials', 'true');
		}
        return $response;
    }
}
