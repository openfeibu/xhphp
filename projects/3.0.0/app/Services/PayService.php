<?php

namespace App\Services;

use Session;
use Validator;
use Illuminate\Http\Request;
use App\Services\HelpService;
use App\Services\UserService;
use App\Services\TradeAccountService;
use App\Services\WalletService;
use App\Services\OrderInfoService;
use App\Services\SMSService;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Payment\Order;
use App\Services\OrderService;

class PayService
{
	protected $helpService;

	protected $userService;

	protected $orderInfoService;

	protected $user;

	public function __construct (UserService $userService,
								HelpService $helpService,
								SMSService $smsService,
								WalletService $walletService,
                         		TradeAccountService $tradeAccountService,
								OrderInfoService $orderInfoService,
								OrderService $orderService)
	{
		$this->userService = $userService;
	 	$this->helpService = $helpService;
	 	$this->walletService = $walletService;
        $this->tradeAccountService = $tradeAccountService;
	 	$this->orderInfoService = $orderInfoService;
	 	$this->smsService = $smsService;
		$this->orderService = $orderService;

	}
	/**
		* 支付处理
		*
		* @access public
		*
	*/
	/**
	* 支付处理
	*
	* @access public
	* @param mixed $pay_id 支付方式：1.支付宝 。2.微信支付（待接入）。3.余额支付
	* @param mixed $pay_platform 请求平台：web(网页)  其他为and ，ios
	* @param mixed $pay_from 使用场合 : shop ，task
	* @since 1.0
	* @return array
	*/
	public function payHandle($data)
	{
		$this->user = $this->userService->getUser();
		switch($data['pay_from'])
		{
			case 'shop':
				return $this->shopPayHandle($data);
				break;
			case 'order':
				return $this->taskPayHandle($data);
				break;
			default :
				throw new \App\Exceptions\Custom\OutputServerMessageException('操作失败');
				break;
		}

	}
	private function taskPayHandle($data)
	{
		switch($data['pay_id'])
		{
			case 1:
				$parameter = array(
					"out_trade_no"	=> $data['order_sn'],
					"subject"		=> $this->user->nickname." 发布任务 ",
					"body"			=> $data['body'],
					"total_fee"		=> $data['total_fee'],
				);
				return $this->alipay($data,$parameter);
				break;
			//微信
			case 2:
				$parameter = [
					'body'             => $data['body'],
					'detail'           => $data['body'],
					'out_trade_no'     => $data['order_sn'],
					'total_fee'        => $data['total_fee'] * 100, // 单位：分
					'notify_url'       => config('app.url').'/order-notify',
				];
				return $this->wechat($data,$parameter);
				break;
	        case 3:
		        $fee = 	$this->user->wallet - $data['total_fee'];
		        $updateWallet = $this->walletService->updateWallet($this->user->uid,$fee);
		        if($updateWallet){
			       	$walletData = array(
						'uid' => $this->user->uid,
						'wallet' => $fee,
						'fee'	=> $data['total_fee'],
						'service_fee' => 0,
						'out_trade_no' => $data['order_sn'],
						'pay_id' => $data['pay_id'],
						'wallet_type' => -1,
						'trade_type' => $data['trade_type'],
						'description' => '发布任务',
			        );
			        $this->walletService->store($walletData);
					$trade_no = 'wallet'.$data['order_sn'];
			        $trade = array(
			        	'uid' => $this->user->uid,
						'out_trade_no' => $data['order_sn'],
						'trade_no' => $trade_no,
						'trade_status' => 'success',
						'wallet_type' => -1,
						'from' => $data['pay_from'],
						'trade_type' => $data['trade_type'],
						'fee' => $data['total_fee'],
						'service_fee' => 0,
						'pay_id' => $data['pay_id'],
						'description' => '发布任务',
		    		);
					$this->tradeAccountService->addThradeAccount($trade);
					$this->orderService->updateOrderStatusNew($data['order_sn']);
					return '支付成功';
		        }else{
		        	throw new \App\Exceptions\Custom\OutputServerMessageException('支付失败');
		        }
				break;
        }
	}
	/*商店购买支付*/
	private function shopPayHandle($data)
	{
		switch($data['pay_id'])
		{
			//支付宝
			case 1:
				$parameter = array(
					"out_trade_no"	=> $data['order_sn'],
					"subject"		=> $data['subject'],
					"body"			=> $data['body'],
					"total_fee"		=> $data['total_fee'],
				);
				return $this->alipay($data,$parameter);
				break;
			//微信
			case 2:
				$parameter = [
					'body'             => $data['body'],
					'detail'           => $data['body'],
					'out_trade_no'     => $data['order_sn'],
					'total_fee'        => $data['total_fee'] * 100, // 单位：分
					'notify_url'       => config('app.url').'/order-notify',
				];
				return $this->wechat($data,$parameter);
				break;
			case 3:
				$fee = 	$this->user->wallet - $data['total_fee'];
				$update = $this->walletService->updateWallet($this->user->uid,$fee);
				if($update){
					$walletData = array(
						'uid' => $this->user->uid,
						'wallet' => $fee,
						'fee'	=> $data['total_fee'],
						'service_fee' => 0,
						'out_trade_no' => $data['order_sn'],
						'pay_id' => $data['pay_id'],
						'wallet_type' => -1,
						'trade_type' => $data['trade_type'],
						'description' => $data['body'],
					);
					$this->walletService->store($walletData);
					$trade_no = 'walletbuyer'.$data['order_sn'];
					$trade = array(
						'uid' => $this->user->uid,
						'out_trade_no' => $data['order_sn'],
						'trade_no' => $trade_no,
						'trade_status' => 'success',
						'wallet_type' => -1,
						'from' => $data['pay_from'],
						'trade_type' => $data['trade_type'],
						'fee' => $data['total_fee'],
						'service_fee' => 0,
						'pay_id' => $data['pay_id'],
						'description' => $data['body'],
					);
					$this->tradeAccountService->addThradeAccount($trade);
				}else{
					throw new \App\Exceptions\Custom\OutputServerMessageException('支付失败');
				}
				$this->orderInfoService->updateOrderInfo($data['order_sn'],['pay_status' => 1,'order_status' => 1,'pay_time' => dtime()]);
				$this->orderInfoService->deGoodsNumber($data['order_id']);
				$this->smsService->sendSMS($data['mobile_no'],'order_info',['sms_template_code' => config('sms.order_info'),'uid' => $data['shop']->uid ]);
				if($data['shop']->shop_type == 3)
				{
					$order_data = $this->orderInfoService->createOrder($data['order_info'],$data['shop'],$data['shop_user']);
					$order = $this->orderService->createOrder($order_data);
				}
				return '支付成功';
				break;
		}
		throw new \App\Exceptions\Custom\OutputServerMessageException('未存在该支付方式');
	}
	private function wallet($walletData)
	{
		$update = $this->walletService->updateWallet($this->user->uid,$fee);
		$this->walletService->store($walletData);

		return '支付成功';
	}
	private function wechat($data,$parameter)
	{

		switch($data['pay_platform'])
		{
			case 'wechat':
				$options = [
					'app_id' => config('wechat.app_id'),
					'payment' => [
						'merchant_id'        => config('wechat.payment.merchant_id'),
						'key'                => config('wechat.payment.key'),
					],
				];
				$app = new Application($options);
				$payment = $app->payment;
				$parameter['trade_type'] = 'JSAPI';
				$parameter['openid'] = $this->user->wxopenid;
				$order = new Order($parameter);
				$result = $payment->prepare($order);
				if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
					$prepayId = $result->prepay_id;
				}
				$pay_data = $payment->configForPayment($prepayId);
				break;
			default:
				$options = [
					'app_id' => config('wechat.app_payment.app_id'),
					'payment' => [
						'merchant_id'        => config('wechat.app_payment.merchant_id'),
						'key'                => config('wechat.app_payment.key'),
					],
				];
				$app = new Application($options);
				$payment = $app->payment;
				$parameter['trade_type'] = 'APP';
				$order = new Order($parameter);
				$result = $payment->prepare($order);
				if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
					$prepayId = $result->prepay_id;
				}
				$pay_data = $payment->configForAppPayment($prepayId);
				break;
		}
		return $pay_data;
	}
	private function alipay($data,$parameter)
	{
		switch($data['pay_platform'])
		{
			case 'wap':
				$alipay_config = array_merge(config('alipay-wap'),config('alipay'));
				$alipay = app('alipay.wap');
				//构造要请求的参数数组，无需改动
				$alipay_config_parameter = $this->alipay_config($alipay_config);
				$alipay_config_parameter['show_url'] = config("app.url");
				$alipay_config_parameter['app_pay'] = 'Y';
				$alipay_config_parameter['notify_url'] = config("app.url")."/alipay/alipayWapNotify";
				$alipay_config_parameter['return_url'] = $data['return_url'];
				$parameter = array_merge($parameter,$alipay_config_parameter);
				$pay_data = $alipay->buildRequestForm($parameter,"get", "确认");
			default:
				$alipay_config = array_merge(config('alipay-mobile'),config('alipay'));
				$alipay = app('alipay.mobile');
				date_default_timezone_set("PRC");
				$alipay_config_parameter = $this->alipay_config($alipay_config);
				$alipay_config_parameter['notify_url'] = config("app.url")."/alipay/alipayAppNotify";
				$alipay_config_parameter['return_url'] = config("app.url")."/alipay/alipayAppReturn";
				$parameter = array_merge($parameter,$alipay_config_parameter);
				$parameter = handle_alipay_parameter($parameter);
				$pay_data = $alipay->createLinkstring($parameter);
				$rsa_sign=urlencode($alipay->rsaSign($pay_data, $alipay_config['private_key']));
				$pay_data = $pay_data.'&sign='.'"'.$rsa_sign.'"'.'&sign_type='.'"'.$alipay_config['sign_type'].'"';
		}
		return $pay_data;
	}
	/*
	$type wap,mobile
	*/
	private function alipay_config($alipay_config)
	{
		$alipay_config_parameter = array(
			'service' => $alipay_config['service'],
			'partner' => $alipay_config['partner'],
			'seller_id' =>  $alipay_config['seller'],
			'payment_type' =>   $alipay_config['payment_type'],
			'_input_charset' =>  $alipay_config['input_charset'],
		);
		return $alipay_config_parameter;
	}

}
