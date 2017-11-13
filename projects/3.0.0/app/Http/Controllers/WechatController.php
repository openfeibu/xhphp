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
            'payment' => [
                'merchant_id'        => config('wechat.payment.merchant_id'),
                'key'                => config('wechat.payment.key'),
            ],
        ];
        $app = new Application($options);
        $js = $app->js;
        var_dump($js->config(array('onMenuShareQQ', 'onMenuShareWeibo'), true));exit;
    }
}
