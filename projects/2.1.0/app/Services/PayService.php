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
								OrderInfoService $orderInfoService)
	{
		$this->userService = $userService;
	 	$this->helpService = $helpService;
	 	$this->walletService = $walletService;
        $this->tradeAccountService = $tradeAccountService;
	 	$this->orderInfoService = $orderInfoService;
	 	$this->smsService = $smsService;
	 	$this->user = $this->userService->getUser();
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
	* @param mixed $pay_form 使用场合 : shop ，task
	* @since 1.0
	* @return array
	*/
	public function payHandle($pay_id,$pay_platform,$pay_form,$data)
	{
		$this->user = $this->userService->getUserByUserID($this->user->uid);

		switch($pay_id)
		{
			case 1:
				switch($pay_platform)
				{
					case 'web':
						$alipay_config = array_merge(config('alipay-wap'),config('alipay'));
						$alipay = app('alipay.wap');
						//构造要请求的参数数组，无需改动
						$parameter = array(
								"service"       => $alipay_config['service'],
								"partner"       => $alipay_config['partner'],
								"seller_id"  	=> $alipay_config['seller'],
								"payment_type"	=> $alipay_config['payment_type'],
								"_input_charset"=> $alipay_config['input_charset'],
								'notify_url' 	=> config("app.url")."/alipay/alipayWapNotify",
								'return_url'	=> $data['return_url'],
								"out_trade_no"	=> $data['order_sn'],
								"subject"		=> $data['subject'],
								"body"			=> $data['body'],
								"total_fee"		=> $data['total_fee'],
								"show_url"		=> config("app.url"),
								"app_pay"	=> "Y",//启用此参数能唤起钱包APP支付宝
						);
						$html_text = $alipay->buildRequestForm($parameter,"get", "确认");
						return $html_text;
					default:
					 	$alipay = app('alipay.mobile');
						date_default_timezone_set("PRC");
						$alipay_config = array_merge(config('alipay-mobile'),config('alipay'));
						$parameter = array(
							'partner' => "\"".$alipay_config['partner']."\"",
							'service' => "\"".$alipay_config['service']."\"",
							'seller_id' =>  "\"".$alipay_config['seller']."\"",
							'payment_type' =>   "\"".$alipay_config['payment_type']."\"",
							'_input_charset' =>  "\"".$alipay_config['input_charset']."\"",
							'out_trade_no' => "\"".$data['order_sn']."\"",
							'notify_url' =>  "\"".config("app.url")."/alipay/alipayAppNotify"."\"",
							'return_url' =>  "\"".config("app.url")."/alipay/alipayAppReturn"."\"",
							'subject' => "\"".$data['subject']."\"",
							'body' =>  "\"".$data['body']."\"",
							'total_fee' =>  "\"".$data['total_fee']."\"",

						);
						$data = $alipay->createLinkstring($parameter);
						$rsa_sign=urlencode($alipay->rsaSign($data, $alipay_config['private_key']));
						$data = $data.'&sign='.'"'.$rsa_sign.'"'.'&sign_type='.'"'.$alipay_config['sign_type'].'"';
				        return $data;
				}
				break;
			case 3:
				$fee = 	$this->user->wallet - $data['total_fee'];
		        $update = $this->walletService->updateWallet($this->user->uid,$fee);
		        if($update){
			       	$walletData = array(
						'uid' => $this->user->uid,
						'wallet' => $this->user->wallet - $data['total_fee'],
						'fee'	=> $data['total_fee'],
						'service_fee' => 0,
						'out_trade_no' => $data['order_sn'],
						'pay_id' => 3,
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
						'from' => $pay_form,
						'trade_type' => $data['trade_type'],
						'fee' => $data['total_fee'],
						'service_fee' => 0,
						'pay_id' => 3,
						'description' => $data['body'],
		    		);
					$this->tradeAccountService->addThradeAccount($trade);
				}else{
	        		throw new \App\Exceptions\Custom\OutputServerMessageException('支付失败');
	        	}
				switch($pay_form)
				{
					case 'shop':
						$this->orderInfoService->updateOrderInfo($data['order_sn'],['pay_status' => 1,'order_status' => 1,'pay_time' => dtime()]);
						$this->orderInfoService->deGoodsNumber($data['order_id']);
						$this->smsService->sendCommonSMS($data['mobile_no'], config('sms.order'));
						break;
					default:
						break;
				}

				return '支付成功';
				break;
			default:
				throw new \App\Exceptions\Custom\OutputServerMessageException('未存在该支付方式');
				break;
		}
	}
}
