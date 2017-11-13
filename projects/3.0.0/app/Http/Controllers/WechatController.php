<?php

namespace App\Http\Controllers;

use Session;
use Illuminate\Http\Request;
use App\Http\Requests;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Payment\Order;

class WechatController extends Controller
{
    public function __construct()
    {

    }
    public function getConfig(Request $request)
    {
        $options = [
            'app_id' => config('wechat.app_id'),
			'secret'             => config('wechat.secret'),
            'payment' => [
                'merchant_id'        => config('wechat.payment.merchant_id'),
                'key'                => config('wechat.payment.key'),
            ],
        ];
        $app = new Application($options);
        $js = $app->js;
		$wx_config = $js->config(array('onMenuShareQQ', 'onMenuShareWeibo'), true);
		//var_dump(json_decode($wx_config,true));
		return [
			'code' => 200,
			'data' => json_decode($wx_config,true)
 		];
    }
}
