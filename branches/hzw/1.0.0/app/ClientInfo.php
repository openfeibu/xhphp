<?php

namespace App;

use DB;
use Crypt;
use Illuminate\Database\Eloquent\Model;

class ClientInfo extends Model
{
    protected $table = 'client_info';

    /**
     * 根据version、platform、os、brand和时间生成mid
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return string          设备编码
     */
    public static function createMid($request)
    {
    	if (!$request->has('version')) {
    		$request->version = 'null';
    	}
    	if (!$request->has('platform')) {
    		$request->platform = 'null';
    	}
    	if (!$request->has('os')) {
    		$request->os = 'null';
    	}
    	if (!$request->has('brand')) {
    		$request->brand = 'null';
    	}
    	return Crypt::encrypt($request->version . '-' . $request->platform . '-' . $request->os . '-' . $request->brand . '-' . $request->REQUEST_TIME_FLOAT);
    }
}
