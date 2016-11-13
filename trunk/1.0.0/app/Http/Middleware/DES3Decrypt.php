<?php

namespace App\Http\Middleware;

use Log;
use Closure;
use App\Helper\DES3;

class DES3Decrypt
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
        Log::error('before------------:' . $request);
        $data = DES3::decrypt($request->data);
        if ($data !== false) {
            Log::error('before------------decrypt:' . $data);
            $request->replace((array)json_decode($data));
            $request->isDecrypt = 1;
        }
        return $next($request);
    }
}
