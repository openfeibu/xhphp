<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Log;
use Input;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\HelpService;
use App\Services\UserService;
use App\Services\OrderService;
use App\Services\MessageService;
use App\Services\TradeAccountService;
use App\Services\WalletService;
use App\Services\TelecomService;
use App\Services\OrderInfoService;
use App\Services\SMSService;
use App\Services\ShopService;

class AlipayController extends Controller
{

	protected $orderService;

    protected $userService;

    protected $helpService;

	protected $walletService;

    protected $tradeAccountService;

	protected $telecomService;

    public function __construct(OrderService $orderService,
                         		UserService $userService,
                         		HelpService $helpService,
                         		SMSService $smsService,
                         		ShopService $shopService,
                         		WalletService $walletService,
                         		TradeAccountService $tradeAccountService,
                         		OrderInfoService $orderInfoService,
                         		TelecomService $telecomService){
	    parent::__construct();
		$this->orderService = $orderService;
        $this->userService = $userService;
        $this->helpService = $helpService;
        $this->smsService = $smsService;
        $this->walletService = $walletService;
        $this->tradeAccountService = $tradeAccountService;
        $this->telecomService = $telecomService;
        $this->shopService = $shopService ;
        $this->orderInfoService = $orderInfoService;
	}
	 public function alipayAppNotify ()
    {
	    Log::debug("android支付宝支付回调开始");

	    $alipay = app('alipay.mobile');
	    $alipay_config = array_merge(config('alipay-mobile'),config('alipay'));
		if($alipay->getResponse(Input::get('notify_id')))
		{
			if($alipay->getSignVeryfy(Input::get(), Input::get('sign'))) {
				$out_trade_no = Input::get('out_trade_no');
				$trade_no = Input::get('trade_no');
				$trade_status = Input::get('trade_status');
				Log::debug('Alipay notify get data verification success.', [
					'out_trade_no' => Input::get('out_trade_no'),
					'trade_no' => Input::get('trade_no')
				]);
	    		if(Input::get('trade_status') == 'TRADE_FINISHED') {

	    		}
	    		else if (Input::get('trade_status') == 'TRADE_SUCCESS') {
					$type = substr($out_trade_no,0,2);
					if($type == 'RT'){
						Log::debug("android支付宝支付回调 RT");

						$this->orderService->updateOrderStatusNew($out_trade_no);
						$order = $this->orderService->getOrderBysn($out_trade_no);
			    		$trade = array(
				    		'uid' => $order->owner_id,
							'out_trade_no' => $out_trade_no,
							'trade_no' => $trade_no,
							'trade_status' => 'success',
							'from' => 'order',
							'trade_type' => 'ReleaseTask',
							'wallet_type' => -1,
							'fee' => $order->total_fee,
							'service_fee'=> 0,
							'pay_id' => 1,
							'description' => '发布任务' ,
			    		);
		    		}
					$this->tradeAccountService->addThradeAccount($trade);
	    		}

				Log::debug("android支付宝支付回调 success");
				echo "success";
			}
			else
			{
				Log::debug("android支付宝支付回调 sign fail");
				echo "sign fail";
			}
		}
		else
		{
			Log::debug("android支付宝支付回调 response fail");
			echo "response fail";
		}
    }
    public function alipayAppReturn (Request $request)
    {
	    Log::debug("android支付宝支付同步回调开始");
        $alipay = app('alipay.mobile');
        $alipay_config = array_merge(config('alipay-mobile'),config('alipay'));
    	if($request->success=="true")//判断success是否为true.
		{
			$sign=$request->sign;
			$data=$alipay->createLinkstring($alipay->paraFilter(Input::get()));
			$isSgin=false;
			$isSgin=$alipay->rsaVerify($data,$alipay_config['alipay_public_key'],$sign);
			if ($isSgin) {
				Log::debug("android支付宝支付同步回调 success");
				echo "return success";
			}
			else {
				Log::debug("android支付宝支付同步回调 fail");
				echo "return fail";
			}
		}
    }
    public function alipayWapNotify ()
    {
		$alipay = app('alipay.wap');
	    $alipay_config = array_merge(config('alipay-wap'),config('alipay'));
		$verify_result = $alipay->verifyNotify();
		Log::debug("手机网站支付宝回调开始");
		if($verify_result) {
			$out_trade_no = Input::get('out_trade_no');
			$trade_no = Input::get('trade_no');
			$trade_status = Input::get('trade_status');
    		if(Input::get('trade_status') == 'TRADE_FINISHED') {

    		}
    		else if (Input::get('trade_status') == 'TRADE_SUCCESS') {
	    		$type = substr($out_trade_no,0,2);
				if($type == 'RT'){
		    		$this->orderService->updateOrderStatusNew($out_trade_no);
					$order = $this->orderService->getOrderBysn($out_trade_no);
		    		$trade = array(
			    		'uid' => $order->owner_id,
						'out_trade_no' => $out_trade_no,
						'trade_no' => $trade_no,
						'from' => 'order',
						'fee' => $order->total_fee,
						'service_fee'=> 0,
						'pay_id' => 1,
						'trade_type' => 'ReleaseTask',
						'wallet_type' => -1,
						'trade_status' => 'success',
						'description' => '发布任务' ,
		    		);
	    		}else if($type == 'TP'){
		    		$this->telecomService->updateTelecomTemOrder($out_trade_no,array('pay_status'=>1,'trade_no'=>$trade_no));
		    		$telecomOrder = $this->telecomService->getTelecomOrderByNo($out_trade_no);
					$trade = array(
				        	'uid' => $telecomOrder->uid,
							'out_trade_no' => $out_trade_no,
							'trade_no' => $trade_no,
							'trade_status' => 'success',
							'wallet_type' => -1,
							'from' => 'telecom_order',
							'trade_type' => 'TelecomOrder',
							'fee' => $telecomOrder->fee,
							'service_fee' => 0,
							'wallet_type' => -1,
							'pay_id' => 1,
							'description' => '电信套餐',
			    	);
	    		}else if($type == 'SP'){
		    		$this->orderInfoService->updateOrderInfo($out_trade_no,['pay_status' => 1,'order_status' => 1,'pay_time' => dtime()]);
		    		$order_info = $this->orderInfoService->isExistsOrderInfo(['order_sn' => $out_trade_no]);
		    		$shop = $this->shopService->getShop(['shop_id' =>$order_info->shop_id],['uid']) ;
		    		$user = $this->userService->getUserByUserID($shop->uid);
		    		$this->orderInfoService->deGoodsNumber($order_info->order_id);
					$this->smsService->sendSMS($user->mobile_no,'order_info',['sms_template_code' => config('sms.order_info'),'uid' => $shop->uid]);
			    	$trade = array(
			        	'uid' => $order_info->uid,
						'out_trade_no' => $out_trade_no,
						'trade_no' => $trade_no,
						'trade_status' => 'success',
						'wallet_type' => -1,
						'from' => 'shop',
						'trade_type' => 'Shop',
						'fee' => $order_info->total_fee,
						'service_fee' => 0,
						'pay_id' => 1,
						'description' => '校汇商店订单',
		    		);

	    		}
		    	$this->tradeAccountService->addThradeAccount($trade);
    		}
		    Log::debug('Alipay notify get data verification success.', [
            	'out_trade_no' => Input::get('out_trade_no'),
            	'trade_no' => Input::get('trade_no')
            ]);
			Log::debug("手机网站支付宝回调 success");
			echo "success";
		}
		else {
		    //验证失败
		    Log::debug("手机网站支付宝回调 fail");
		    echo "fail";
		}
    }
	 public function alipayTelecomWapNotify ()
    {
		/*$alipay_config = config('alipay-telecom-wap');
		$alipay = app('alipay-telecom.wap');*/
		$alipay_config = array_merge(config('alipay-wap'),config('alipay'));
		$alipay = app('alipay.wap');
		$verify_result = $alipay->verifyNotify();
		Log::debug("手机网站支付宝回调开始");
		if($verify_result) {
			$out_trade_no = Input::get('out_trade_no');
			$trade_no = Input::get('trade_no');
			$trade_status = Input::get('trade_status');
    		if(Input::get('trade_status') == 'TRADE_FINISHED') {

    		}
    		else if (Input::get('trade_status') == 'TRADE_SUCCESS') {
	    		$type = substr($out_trade_no,0,2);
				if($type == 'RT'){
		    		$this->orderService->updateOrderStatusNew($out_trade_no);
					$order = $this->orderService->getOrderBysn($out_trade_no);
		    		$trade = array(
			    		'uid' => $order->owner_id,
						'out_trade_no' => $out_trade_no,
						'trade_no' => $trade_no,
						'from' => 'order',
						'fee' => $order->total_fee,
						'service_fee'=> 0,
						'pay_id' => 1,
						'trade_type' => 'ReleaseTask',
						'wallet_type' => -1,
						'trade_status' => 'success',
						'description' => '发布任务' ,
		    		);
	    		}else if($type == 'TP'){
		    		$this->telecomService->updateTelecomTemOrder($out_trade_no,array('pay_status'=>1,'trade_no'=>$trade_no));
		    		$telecomOrder = $this->telecomService->getTelecomOrderByNo($out_trade_no);
					$trade = array(
				        	'uid' => $telecomOrder->uid,
							'out_trade_no' => $out_trade_no,
							'trade_no' => $trade_no,
							'trade_status' => 'success',
							'wallet_type' => -1,
							'from' => 'telecom_order',
							'trade_type' => 'TelecomOrder',
							'fee' => $telecomOrder->fee,
							'service_fee' => 0,
							'wallet_type' => -1,
							'pay_id' => 1,
							'description' => '电信套餐',
			    	);
	    		}
		    	$this->tradeAccountService->addThradeAccount($trade);


    		}
		    Log::debug('Alipay notify get data verification success.', [
            	'out_trade_no' => Input::get('out_trade_no'),
            	'trade_no' => Input::get('trade_no')
            ]);
			Log::debug("手机网站支付宝回调 success");
			echo "success";
		}
		else {
		    //验证失败
		    Log::debug("手机网站支付宝回调 fail");
		    echo "fail";
		}
    }
}
