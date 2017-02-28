<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Input;
use Log;
use Event;
use App\TradeAccount;
use App\Http\Requests;
use App\Services\HelpService;
use App\Services\UserService;
use App\Services\OrderService;
use App\Services\MessageService;
use App\Services\TradeAccountService;
use App\Services\WalletService;
use App\Services\GameService;
use App\Services\PushService;
use App\Services\ShopService;
use App\Services\OrderInfoService;
use App\Events\Integral\Integrals;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    protected $orderService;

    protected $userService;

    protected $helpService;

    protected $messageService;

	protected $walletService;

    protected $tradeAccountService;

    protected $pushService;

    function __construct(OrderService $orderService,
                         UserService $userService,
                         HelpService $helpService,
                         MessageService $messageService,
                         GameService $gameService,
                         WalletService $walletService,
                         TradeAccountService $tradeAccountService,
                         PushService $pushService,
                         ShopService $shopService,
                         OrderInfoService $orderInfoService)
    {
	    parent::__construct();
        $this->middleware('auth', ['except' => ['getOrderList', 'orderAgreement', 'getOrder','alipayAppReturn','alipayWapNotify','alipayAppNotify','getRecommendOrders','getOrderDetail']]);

        $this->orderService = $orderService;
        $this->userService = $userService;
        $this->helpService = $helpService;
        $this->gameService = $gameService;
        $this->walletService = $walletService;
        $this->tradeAccountService = $tradeAccountService;
        $this->messageService = $messageService;
        $this->pushService = $pushService;
        $this->shopService = $shopService ;
        $this->orderInfoService = $orderInfoService;
    }


    /**
     * 获取任务列表
     */
    public function getOrderList(Request $request)
    {
        //检验请求参数
        $rule = [
            'page' => 'required',
            'type' => 'sometimes|required|in:all,personal,business'
        ];
        $this->helpService->validateParameter($rule);

		$type = isset($request->type) ? $request->type : 'all';

        //获取任务列表
        $orders = $this->orderService->getOrderList($request->page,$type);

        return [
            'code' => 200,
            'type' => $type,
            'detail' => '请求成功',
            'data' => $orders,
        ];
    }

    /**
     * 获取指定任务ID可公开信息
     */
    public function getOrder(Request $request)
    {
        //检验请求参数
        $rule = [
            'order_id' => 'required',
        ];
        $this->helpService->validateParameter($rule);

        //获取单个任务信息
        $order = $this->orderService->getSingleOrder($request->order_id);

        return [
            'code' => 200,
            'detail' => '请求成功',
            'data' => $order,
        ];
    }
	public function getOrderDetail(Request $request)
	{
		//检验请求参数
        $rule = [
            'order_id' => 'required',
        ];
        $this->helpService->validateParameter($rule);
		$order = $this->orderService->getOrderDetail($request->order_id);
		return [
            'code' => 200,
            'detail' => '请求成功',
            'data' => $order,
        ];
	}
	/**
     * 获取指定任务ID可公开信息
     */
    public function getOrderByToken(Request $request)
    {
        //检验请求参数
        $rule = [
			'token' => 'required',
            'order_id' => 'required',
        ];
        $this->helpService->validateParameter($rule);

        //获取单个任务信息
        $order = $this->orderService->getSingleOrderByToken($request->order_id);

        return [
            'code' => 200,
            'detail' => '请求成功',
            'data' => $order,
        ];
    }

    /**
     * 创建新任务
     */
    public function createOrder(Request $request)
    {
	    //检验是否已实名
        /*$this->userService->isRealnameAuth();*/

        //检验请求参数
        $rule = [
            'phone' => 'sometimes|required|regex:/^1[34578][0-9]{9}/',
            'destination' => 'required',
            'description' => 'required',
            'fee' => 'required|numeric|min:2',
            'goods_fee' => 'sometimes|required|numeric|min:0',
            'pay_id' => "required|integer|between:1,3",
            'pay_password' => 'sometimes|required|string',
        ];
        $this->helpService->validateParameter($rule);

        $this->user = $this->userService->getUser();

       	$this->user = $this->userService->getUserByUserID($this->user->uid);

		$alipayInfo = $this->userService->getAlipayInfo($this->user->uid);

		if($request->pay_id == 3&&!$alipayInfo->is_paypassword){
			return [
				'code' => 3001,
				'detail' => '未设置支付密码',
			];
		}

        //频率限制

        //次数限制
       // $this->orderService->checkOrderLimit();

		$goods_fee = $request->goods_fee ? $request->goods_fee : 0;

		$total_fee = $request->fee;
		//$total_fee = 0.02;

		$service_fee = $this->helpService->serviceFee($total_fee) ;

        $order_sn = $this->helpService->buildOrderSn('RT');

        if($request->pay_id == 3){
	       	if (!password_verify($request->pay_password, $this->user->pay_password)) {
			 	throw new \App\Exceptions\Custom\OutputServerMessageException('支付密码错误');
			}
	        $wallet = $this->user->wallet;
	        if($total_fee > $wallet){
		        return [
					'code' => '110',
					'detail' => '余额（'.$this->user->wallet.'）不足,请选择其他支付方式',
		        ];
	        }
        }
        //创建新任务
        $order = $this->orderService->createOrder(['destination' => $request->destination,
                                             'description' => $request->description,
                                             'fee' => $request->fee,
                                             'goods_fee' => 0 ,
                                             'total_fee' => $total_fee,
                                             'service_fee' => $service_fee,
                                             'phone' => $request->phone,
                                             'order_sn' => $order_sn,
                                             'status' => 'waitpay',
                                             'pay_id' => $request->pay_id,
                                            ]);
		if($request->pay_id ==1){
			if($request->wap){
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
						'return_url'	=> config('common.order_return_url'),
						"out_trade_no"	=> $order_sn,
						"subject"		=> $this->user->nickname." 发布任务 ",
						"body"			=> $request->description,
						"total_fee"		=> $total_fee,
						"show_url"		=> config("app.url"),
						"app_pay"	=> "Y",//启用此参数能唤起钱包APP支付宝
				);
				$html_text = $alipay->buildRequestForm($parameter,"get", "确认");
				return [
		            'code' => 200,
		            'data' => $html_text,
		        ];
			}else{
				// throw new \App\Exceptions\Custom\RequestSuccessException();
		        $alipay = app('alipay.mobile');
				date_default_timezone_set("PRC");
				$alipay_config = array_merge(config('alipay-mobile'),config('alipay'));
				$parameter = array(
					'partner' => "\"".$alipay_config['partner']."\"",
					'service' => "\"".$alipay_config['service']."\"",
					'seller_id' =>  "\"".$alipay_config['seller']."\"",
					'payment_type' =>   "\"".$alipay_config['payment_type']."\"",
					'_input_charset' =>  "\"".$alipay_config['input_charset']."\"",
					'out_trade_no' => "\"".$order_sn."\"",
					'notify_url' =>  "\"".config("app.url")."/alipay/alipayAppNotify"."\"",
					'return_url' =>  "\"".config("app.url")."/alipay/alipayAppReturn"."\"",
					'subject' => "\"".$this->user->nickname." 发布任务 \"",
					'body' =>  "\"".$request->description."\"",
					'total_fee' =>  "\"".$total_fee."\"",

				);
				$data = $alipay->createLinkstring($parameter);
				$rsa_sign=urlencode($alipay->rsaSign($data, $alipay_config['private_key']));
				$data = $data.'&sign='.'"'.$rsa_sign.'"'.'&sign_type='.'"'.$alipay_config['sign_type'].'"';
		        return [
		            'code' => 200,
		            'data' => $data,
		        ];

			}
        }
        else if($request->pay_id==3){
	        $fee = 	$this->user->wallet - $total_fee;
	        $updateWallet = $this->walletService->updateWallet($this->user->uid,$fee);
	        if($updateWallet){
		       	$walletData = array(
					'uid' => $this->user->uid,
					'wallet' => $this->user->wallet - $total_fee,
					'fee'	=> $total_fee,
					'service_fee' => 0,
					'out_trade_no' => $order_sn,
					'pay_id' => 3,
					'wallet_type' => -1,
					'trade_type' => 'ReleaseTask',
					'description' => '发布任务',
		        );
		        $this->walletService->store($walletData);
				$trade_no = 'wallet'.$order_sn;
		        $trade = array(
		        	'uid' => $this->user->uid,
					'out_trade_no' => $order_sn,
					'trade_no' => $trade_no,
					'trade_status' => 'success',
					'wallet_type' => -1,
					'from' => 'order',
					'trade_type' => 'ReleaseTask',
					'fee' => $total_fee,
					'service_fee' => 0,
					'pay_id' => 3,
					'description' => '发布任务',
	    		);
				$this->tradeAccountService->addThradeAccount($trade);
				$this->orderService->updateOrderStatusNew($order_sn);
				return [
		            'code' => 200,
		            'detail' => "支付成功",
		            'data' => ''
		        ];
	        }else{
	        	throw new \App\Exceptions\Custom\OutputServerMessageException('支付失败');
	        }
        }
        return [
            'code' => 110,
            'detail' => "未找到支付方式",
        ];
    }

    /**
     * 接受任务
     */
    public function claimOrder(Request $request)
    {
        //检验请求参数
        $rule = [
			'token' => 'required',
            'order_id' => 'required|integer',
        ];
        $this->helpService->validateParameter($rule);

        //检验是否已实名
        $this->userService->isRealnameAuth();

        //次数限制
        $this->orderService->checkScalping($request->order_id);

        //接受任务
        $order = $this->orderService->claimOrder(['order_id' => $request->order_id]);

        $order_owner_id = $this->orderService->getSingleOrderAllInfo($request->order_id)->owner_id;
        //发送纸条给发单者
        $this->messageService->SystemMessage2SingleOne($order_owner_id, '您好，您发布的任务已被使者接入囊中，赶紧与ta取得联系。');

        //推送给发单者
        $custom = [
			'open' => 'task',
			'data' => [
				'id' => $request->order_id,
			],
		];
        $this->pushService->PushUserTokenDevice('校汇任务', '您好，您发布的任务已被使者接入囊中，赶紧与ta取得联系。', $order_owner_id,1,$custom);

        throw new \App\Exceptions\Custom\RequestSuccessException();
    }

    /**
     * 获取我的任务列表
     */
    public function getMyOrder(Request $request)
    {
        //检验请求参数
        $rule = [
			'token' => 'required',
            'page' => 'required|integer',
        ];
        $this->helpService->validateParameter($rule);

        //获取我的任务列表
        $myOrder = $this->orderService->getMyOrder($request->page);

        return [
            'code' => 200,
            'detail' => '请求成功',
            'data' => $myOrder
        ];
    }

    /**
     * 获取我的工作列表
     */
    public function getMyWork(Request $request)
    {
        //检验请求参数
        $rule = [
			'token' => 'required',
            'page' => 'required|integer',
        ];
        $this->helpService->validateParameter($rule);

        //获取我的任务列表
        $myOrder = $this->orderService->getMyWork($request->page);

        return [
            'code' => 200,
            'detail' => '请求成功',
            'data' => $myOrder
        ];
    }


    /**
     * 一方请求取消订单
     */
    public function askCancel(Request $request)
    {
        //检验请求参数
        $rule = [
            'order_id' => 'required|integer',
        ];
        $this->helpService->validateParameter($rule);

		$this->user = $this->userService->getUser();

        //检验任务是否已被接
        $order = $this->orderService->getSingleOrder($request->order_id);

		if ($order->courier_id == $this->user->uid) {

			if ($order->status != 'accepted') {
	            throw new \App\Exceptions\Custom\OutputServerMessageException('当前任务状态不允许取消');
	        }
	        $param = [
	            'order_id' => $request->order_id,
	            'only_in_status' => ['accepted'],
	            'status' => 'new',
	            'courier_cancel' => true
	        ];
	        $this->orderService->updateOrderStatus($param);
	        throw new \App\Exceptions\Custom\RequestSuccessException();
    	}

        if($order->owner_id == $this->user->uid){
	        if ($order->status != 'new') {
	            throw new \App\Exceptions\Custom\OutputServerMessageException('当前任务状态不允许取消');
	        }

	        $param = [
	            'order_id' => $request->order_id,
	            'only_in_status' => ['new'],
	        ];


			if($order->pay_id == 3){
				$walletData = array(
					'uid' => $this->user->uid,
					'wallet' => $this->user->wallet + $order->fee,
					'fee'	=> $order->fee,
					'service_fee' => 0,
					'out_trade_no' => $order->order_sn,
					'pay_id' => $order->pay_id,
					'wallet_type' => 1,
					'trade_type' => 'CancelTask',
					'description' => '取消任务',
		        );
		        $this->walletService->store($walletData);
				$tradeData = array(
					'wallet_type' => 1,
					'trade_type' => 'CancelTask',
					'description' => '取消任务',
					'trade_status' => 'refunded',
				);
				$param['status'] = 'cancelled';
				$this->walletService->updateWallet($order->owner_id,$this->user->wallet + $order->fee);
				$this->tradeAccountService->updateTradeAccount($order->order_sn,$tradeData);
				//取消任务
	        	$this->orderService->updateOrderStatus($param);
	        	return [
					'code' => 200,
					'detail' => '取消任务成功，任务费用已返回您的钱包，请查收',
	        	];
			}
	   		else{
		   		$tradeData = array(
					'wallet_type' => 1,
					'trade_type' => 'CancelTask',
					'trade_status' => 'refunding',
					'description' => '取消任务',
				);
				$param['status'] = 'cancelling';
				$this->tradeAccountService->updateTradeAccount($order->order_sn,$tradeData);
				//取消任务
	        	$this->orderService->updateOrderStatus($param);
	        	return [
					'code' => 200,
					'detail' => '取消任务成功，等待管理员审核',
	        	];
	   		}
		}
		throw new \App\Exceptions\Custom\OutputServerMessageException('没有取消该任务的权限');
        //如果需要另一方同意，则这里需要发送纸条给另一方
        // $this->messageService->SystemMessage2OtherOfOrder();

      //  throw new \App\Exceptions\Custom\RequestSuccessException();
    }
    /**
     * (搁置)另一方同意取消订单
     */
    public function agreeCancel(Request $request)
    {
        # code...
    }

    /**
     * (搁置)发单人获取接单人列表
     */
    public function getCourierList(Request $request)
    {
        # code...
    }

    /**
     * (搁置)接单人选定接单人
     */
    public function chooseCourier(Request $request)
    {
        # code...
    }

    /**
     * 接单人完成任务
     */
    public function finishWork(Request $request)
    {
        //检验请求参数
        $rule = [
            'order_id' => 'required|integer',
        ];
        $this->helpService->validateParameter($rule);

		$this->user = $this->userService->getUser();

        $order = $this->orderService->getSingleOrder($request->order_id);
        //检验任务是否已接
        if ($order->status != 'accepted') {
            throw new \App\Exceptions\Custom\OutputServerMessageException('当前任务状态不允许完成任务');
        }

        $param = [
            'order_id' => $request->order_id,
            'status' => 'finish',
        ];
        $this->orderService->updateOrderStatus($param);

        //纸条通知发单人
        $owner_id = $this->orderService->getSingleOrderAllInfo($request->order_id)->owner_id;
        $this->messageService->SystemMessage2SingleOne($owner_id, '您好，接单人已完成你交付的任务，赶紧去看看。如果满意，请给任务结算吧。');

        //推送给发单人
        $custom = [
			'open' => 'task',
			'data' => [
				'id' => $request->order_id,
			],
		];
        $this->pushService->PushUserTokenDevice('校汇任务', '您好，接单人已完成你交付的任务，赶紧去看看。如果满意，请给任务结算吧。', $owner_id,1,$custom);


        throw new \App\Exceptions\Custom\RequestSuccessException();
    }

    /**
     * 发单人结算任务
     */
    public function confirmFinishWork(Request $request)
    {
        //检验请求参数
        $rule = [
            'order_id' => 'required|integer',
            'pay_password' => 'required|string',
        ];
        $this->helpService->validateParameter($rule);

		$this->user = $this->userService->getUser();

		if (!password_verify($request->pay_password, $this->user->pay_password)) {
		 	throw new \App\Exceptions\Custom\OutputServerMessageException('支付密码错误');
		}
        $order = $this->orderService->getSingleOrder($request->order_id);
        //检验任务是否已完成
		if($order->type == 'business'){
	        $order_info = $this->orderInfoService->getOrderInfo($order->order_id);
	        if($order_info){
		        $shop = $this->shopService->isExistsShop(['shop_id' => $order_info->shop_id]);
		        $this->orderInfoService->confirm($order_info,$shop,$this->walletService,$this->tradeAccountService);
	        }
        }else{
	        $this->gameService->freeOrder($this->user,$order);
        }
        $this->orderService->confirmFinishWork($order,$this->walletService,$this->tradeAccountService);

        throw new \App\Exceptions\Custom\RequestSuccessException();
    }

    /**
     * 任务声明
     */
    public function orderAgreement(Request $request)
    {
        // require_once 'XingeApp.php';

        // $push = new XingeApp(2100214575, "-1");
        // $mess = new Message();
        // $mess->setTitle('title');
        // $mess->setContent('content');
        // $mess->setType(Message::TYPE_MESSAGE);
        // $ret = $push->PushSingleDevice('f2b79a9f938d7a4c440e8048338e14905ed7e759', $mess);
        // return $ret;

        // var_dump(XingeApp::PushAccountAndroid(2100214575, "-1", "title", "content", "1231231312312"));

        // $push = new XingeApp(2100214575, '-1');
        // $mess = new Message();
        // $mess->setTitle('123');
        // $mess->setContent('qwe');
        // $custom = array('key1'=>'value1', 'key2'=>'value2');
        // $mess->setCustom($custom);
        // $mess->setType(2);
        // return $push->PushSingleDevice('f2b79a9f938d7a4c440e8048338e14905ed7e759', $mess);
        
		/*$data = [
			'refresh' => 1,
			'target' => 'message',
			'data' => "123"
		];
$ret = $this->pushService->PushUserTokenDevice('系统通知', json_encode($data), 77,2);
	var_dump($ret);exit;*/
		 return $custom = [
			'open' => 'task',
			'data' => [
				'id' => '1094',
			],
		];
        $ret = $this->pushService->PushUserTokenDevice('标题', '内容', '958',1,$custom);
		//$this->messageService->SystemMessage2SingleOne('77', '哈哈。');
		var_dump($ret);exit;
        return [
			'code' => 200
        ];
    }
/*
	public function mobileAlipay (Request $request)
	{
		$rule = [
            'order_sn' 	=> 'required',
        ];

        $this->helpService->validateParameter($rule);

        //获取单个任务信息
        $order = $this->orderRepository->getSingleOrderBySn($request->order_sn);

        if(!$order){
	        return [
	        	'code' => 400,
	        	'description' => '任务不存在',
	        ];
        }

		$alipay = app('alipay.mobile');

		date_default_timezone_set("PRC");

		$alipay_config = array_merge(config('alipay-mobile'),config('alipay'));

		$parameter['partner'] = "\"".$alipay_config['partner']."\"";
		$parameter['service'] = "\"".$alipay_config['service']."\"";
		$parameter['out_trade_no'] = "\"".$order->order_sn."\"";
		$parameter['notify_url'] =  "\""."http://xh.feibu.info/order/alipayNotify"."\"";
		$parameter['_input_charset'] =  "\"".$alipay_config['input_charset']."\"";
		$parameter['subject'] = "\"".$order->description."\"";
		$parameter['body'] =  "\"".$order->description."\"";
		$parameter['payment_type'] =   "\""."1"."\"";
		$parameter['seller_id'] =  "\"".$alipay_config['seller']."\"";
		$parameter['total_fee'] =  "\"".$order->fee."\"";

		$data = $alipay->createLinkstring($parameter);

		Log::debug($data);
		$rsa_sign=urlencode($alipay->rsaSign($data, $alipay_config['private_key']));

		$data = $data.'&sign='.'"'.$rsa_sign.'"'.'&sign_type='.'"'.$alipay_config['sign_type'].'"';

		return $data ;

	}*/

	public function remindOrder(Request $request){
		//检验请求参数
        $rule = [
            'token' => 'required',
        ];
        $this->helpService->validateParameter($rule);

		$this->user = $this->userService->getUser();

		$remindOrder = $this->orderService->remindOrder();
        if($remindOrder == 200){
			return [
				'code' => 200,
				'detail' => '请求成功',
        	];
		}else{
			return [
				'code' => 304,
				'detail' => '无更新数据',
        	];
		}

	}

	public function getRecommendOrders (Request $request)
	{
		$rule = [
            'number' => 'sometimes|numeric',
        ];

        $this->helpService->validateParameter($rule);

        $number = isset($request->number) && intval($request->number) > 0 ?  intval($request->number) : 10;

        $orders = $this->orderService->getRecommendOrders($number);

        return [
			'code' => 200,
			'data' => $orders,
        ];
	}

}

