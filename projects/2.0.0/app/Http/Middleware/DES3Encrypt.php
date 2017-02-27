<?php

namespace App\Http\Middleware;

use Log;
use Closure;
use App\Helper\DES3;

class DES3Encrypt
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
        Log::error('after------------:' . $response);
        if ($request->isDecrypt === 1) {
            $data = ['data' => DES3::encrypt($response->getContent())];
        } else {
            $data = $response->getContent();
        }
        $response->setContent($data);
        Log::error('after------------encrypt:' . $response);
        return $response;
    }
}
