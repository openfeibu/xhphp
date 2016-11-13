<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use Log;
use Crypt;
use App\Version;
use App\ClientInfo;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class VerifyController extends Controller
{
    public function verify(Request $request)
    {
        #todo 参数验证
        $clientInfo = ClientInfo::where('mid', $request->mid)->first();
        if ($clientInfo) {
            $versionCode = Version::select('code')->orderBy('id', 'desc')->first();
            if ($clientInfo->version >= $versionCode) {
                $clientInfo->setConnection('write');
                $clientInfo->version = $request->version;
                $clientInfo->save();

                return [
                    'code' => 200,
                    'description' => '校验成功'
                ];
            } else {
                return [
                    'code' => 403,
                    'description' => '客户端版本过低'
                ];
            }
        } else {
            $mid = ClientInfo::createMid($request);
            $clientInfo = new ClientInfo;
            $clientInfo->setConnection('write');
            $clientInfo->version = $request->version;
            $clientInfo->platform = $request->platform;
            $clientInfo->os = $request->os;
            $clientInfo->brand = $request->brand;
            $clientInfo->mid = $mid;
            $clientInfo->save();

            return [
                'code' => 200,
                'mid' => $mid
            ];
        }
    }
}
