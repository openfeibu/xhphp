<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Input;
use Log;
use Event;
use App\TradeAccount;
use App\Http\Requests;
use App\OrderBonusSetting;
use App\Services\SMSService;
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
use App\Services\PayService;

class OrderController extends Controller
{
    protected $orderService;

    protected $userService;

    protected $helpService;

    protected $messageService;

	protected $walletService;

    protected $tradeAccountService;

    protected $pushService;

    protected $smsService;

    function __construct(OrderService $orderService,
                         UserService $userService,
                         HelpService $helpService,
                         MessageService $messageService,
                         GameService $gameService,
                         WalletService $walletService,
                         TradeAccountService $tradeAccountService,
                         PushService $pushService,
                         ShopService $shopService,
                         SMSService $smsService,
                         OrderInfoService $orderInfoService,
                         PayService $payService)
    {
	    parent::__construct();
        $this->middleware('auth', ['except' => ['getOrderList', 'orderAgreement', 'getOrder','alipayAppReturn','alipayWapNotify','alipayAppNotify','getRecommendOrders','getOrderDetail']]);

        $this->orderService = $orderService;
        $this->smsService = $smsService;
        $this->userService = $userService;
        $this->helpService = $helpService;
        $this->gameService = $gameService;
        $this->walletService = $walletService;
        $this->tradeAccountService = $tradeAccountService;
        $this->messageService = $messageService;
        $this->pushService = $pushService;
        $this->shopService = $shopService ;
        $this->orderInfoService = $orderInfoService;
        $this->payService = $payService;
    }


    /**
     * ??????????????????
     */
    public function getOrderList(Request $request)
    {
        //??????????????????
        $rule = [
            'page' => 'required',
            //'type' => 'sometimes|required|in:all,personal,business,canteen'
        ];
        $this->helpService->validateParameter($rule);

		$type = isset($request->type) ? $request->type : 'all';

        //??????????????????
        $orders = $this->orderService->getOrderList($request->page,$type);

        return [
            'code' => 200,
            'type' => $type,
            'detail' => '????????????',
            'data' => $orders,
        ];
    }

    /**
     * ??????????????????ID???????????????
     */
    public function getOrder(Request $request)
    {
        //??????????????????
        $rule = [
            'order_id' => 'required',
        ];
        $this->helpService->validateParameter($rule);

        //????????????????????????
        $order = $this->orderService->getSingleOrder($request->order_id);

        return [
            'code' => 200,
            'detail' => '????????????',
            'data' => $order,
        ];
    }
	public function getOrderDetail(Request $request)
	{
		//??????????????????
        $rule = [
            'order_id' => 'required',
        ];
        $this->helpService->validateParameter($rule);
		$order = $this->orderService->getOrderDetail($request->order_id);
		return [
            'code' => 200,
            'detail' => '????????????',
            'data' => $order,
        ];
	}
	/**
     * ??????????????????ID???????????????
     */
    public function getOrderByToken(Request $request)
    {
        //??????????????????
        $rule = [
            'order_id' => 'required',
        ];
        $this->helpService->validateParameter($rule);

        //????????????????????????
        $order = $this->orderService->getSingleOrderByToken($request->order_id);

        return [
            'code' => 200,
            'detail' => '????????????',
            'data' => $order,
        ];
    }

    /**
     * ???????????????
     */
    public function createOrder(Request $request)
    {
	    //?????????????????????
        /*$this->userService->isRealnameAuth();*/

        //??????????????????
        $rule = [
            'phone' => 'sometimes|required|regex:/^1[34578][0-9]{9}/',
            'destination' => 'required',
            'description' => 'required',
            'fee' => 'required|numeric|min:2',
            'goods_fee' => 'sometimes|required|numeric|min:0',
            'pay_id' => "required|integer|in:1,2,3",
            'pay_password' => 'sometimes|required|string',
            'platform' => 'sometimes|in:and,ios,wap,wechat',
        ];
        $this->helpService->validateParameter($rule);

        $this->user = $this->userService->getUserTokenAuth();

		$alipayInfo = $this->userService->getAlipayInfo($this->user->uid);

		if($request->pay_id == 3&&!$alipayInfo->is_paypassword){
			return [
				'code' => 3001,
				'detail' => '?????????????????????',
			];
		}

        //????????????

        //????????????
       // $this->orderService->checkOrderLimit();

		$goods_fee = $request->goods_fee ? $request->goods_fee : 0;

		$total_fee = $request->fee;
		//$total_fee = 0.02;

		$service_fee = $this->helpService->serviceFee($total_fee) ;

        $order_sn = $this->helpService->buildOrderSn('RT');

        if($request->pay_id == 3){
	       	if (!password_verify($request->pay_password, $this->user->pay_password)) {
			 	throw new \App\Exceptions\Custom\OutputServerMessageException('??????????????????');
			}
	        $wallet = $this->user->wallet;
	        if($total_fee > $wallet){
		        return [
					'code' => '110',
					'detail' => '?????????'.$this->user->wallet.'?????????,???????????????????????????',
		        ];
	        }
        }
        //???????????????
        $order = $this->orderService->createOrder(['destination' => $request->destination,
                                             'description' => $request->description,
                                             'fee' => $request->fee,
                                             'goods_fee' => 0 ,
                                             'total_fee' => $total_fee,
                                             'service_fee' => $service_fee,
                                             'phone' => $request->phone ? $request->phone : $this->user->mobile_no,
                                             'order_sn' => $order_sn,
                                             'status' => 'waitpay',
                                             'pay_id' => $request->pay_id,
                                             'uid' => $this->user->uid
                                            ]);
        $pay_platform = isset($request->platform) ? $request->platform : 'and';
        $data = [
        	'return_url' => config('common.order_return_url'),
        	'order_sn' => $order_sn,
        	'subject' => $this->user->nickname." ???????????? ",
        	'body' => $request->description,
        	'total_fee' => $total_fee,
        	'trade_type' => 'ReleaseTask',

			'pay_id' => $request->pay_id,
            'pay_from' => 'order',
			'pay_platform' => $pay_platform,
        ];
        $data = $this->payService->payHandle($data);
        return [
			'code' => 200,
			'data' => $data
        ];
    }

    /**
     * ????????????
     */
    public function claimOrder(Request $request)
    {
    //    $this->messageService->SystemMessage2SingleOne(77, '????????????1233');exit;
        //??????????????????
        $fp = fopen("lock.txt", "w+");
		if (flock($fp, LOCK_NB | LOCK_EX)) {
            $rule = [
    			'token' => 'required',
                'order_id' => 'required|integer',
            ];
            $this->helpService->validateParameter($rule);

            $user = $this->userService->getUser();
            //?????????????????????
            $this->userService->isRealnameAuth($user);

            //????????????
            //$this->orderService->checkScalping($request->order_id);

            //????????????
            $this->orderService->claimOrder(['order_id' => $request->order_id]);

            $order = $this->orderService->getSingleOrderAllInfo($request->order_id);

            if($order->order_id)
            {
                if($order->type == 'business')
                {
                    $order_info = $this->orderInfoService->getOrderInfoCustom(['order_id' => $order->order_id],['pick_code']);
                    $rst = $this->smsService->sendSMS($order->courier_mobile_no,$type = 'pick_code',$data = ['sms_template_code' => config('sms.pick_code'),'code' => $order_info->pick_code,'uid' => $order->courier_id]);
                }else if($order->type == 'canteen')
                {
                    $this->orderInfoService->updateOrderInfoById($order->order_id,['shipping_status' => 1,'shipping_time' => dtime()]);
                }
            }
            //????????????????????????
            $this->messageService->SystemMessage2SingleOne($order->owner_id, trans('task.task_be_accepted'));

            //??????????????????

    		$data = [
    			'refresh' => 1,
    			'target' => '',
    			'open' => 'task',
    			'data' => [
    				'id' => $request->order_id,
    				'title' => '????????????',
    				'content' => trans('task.task_be_accepted'),
    			],
    		];
            $this->pushService->PushUserTokenDevice('????????????', trans('task.task_be_accepted'), $order->owner_id,2,$data);
            throw new \App\Exceptions\Custom\RequestSuccessException("????????????????????????");
        }
        else {
            throw new \App\Exceptions\Custom\OutputServerMessageException('??????????????????????????????');
        }
        @fclose($fp);
    }

    /**
     * ????????????????????????
     */
    public function getMyOrder(Request $request)
    {
        //??????????????????
        $rule = [
			'token' => 'required',
            'page' => 'required|integer',
        ];
        $this->helpService->validateParameter($rule);

        //????????????????????????
        $myOrder = $this->orderService->getMyOrder($request->page);

        return [
            'code' => 200,
            'detail' => '????????????',
            'data' => $myOrder
        ];
    }

    /**
     * ????????????????????????
     */
    public function getMyWork(Request $request)
    {
        //??????????????????
        $rule = [
			'token' => 'required',
            'page' => 'required|integer',
        ];
        $this->helpService->validateParameter($rule);

        //????????????????????????
        $myOrder = $this->orderService->getMyWork($request->page);

        return [
            'code' => 200,
            'detail' => '????????????',
            'data' => $myOrder
        ];
    }


    /**
     * ????????????????????????
     */
    public function askCancel(Request $request)
    {
        //??????????????????
        $rule = [
            'order_id' => 'required|integer',
        ];
        $this->helpService->validateParameter($rule);

		$this->user = $this->userService->getUser();

        //???????????????????????????
        $order = $this->orderService->getSingleOrder($request->order_id);

		if ($order->courier_id == $this->user->uid) {

			if ($order->status != 'accepted') {
	            throw new \App\Exceptions\Custom\OutputServerMessageException('?????????????????????????????????');
	        }
	        $param = [
	            'order_id' => $request->order_id,
	            'only_in_status' => ['accepted'],
	            'status' => 'new',
	            'courier_cancel' => true
	        ];
            //?????????????????????
            if($order->type == 'business')
            {
                $this->orderInfoService->uploadPickCode(['order_id' => $order->order_id]);
            }
            else if($order->type == 'canteen')
            {
                $this->orderInfoService->updateOrderInfoById($order->order_id,['shipping_status' => 0]);
            }

	        $this->orderService->updateOrderStatus($param);

	        throw new \App\Exceptions\Custom\RequestSuccessException();
    	}

        if($order->owner_id == $this->user->uid){
	        if ($order->status != 'new') {
	            throw new \App\Exceptions\Custom\OutputServerMessageException('?????????????????????????????????');
	        }

	        $param = [
	            'order_id' => $request->order_id,
	            'only_in_status' => ['new'],
	        ];

            if($order->type == 'business')
            {
                //??????????????????
                $param['status'] = 'cancelled';
                $this->orderService->delete(['oid' => $request->order_id ]);
                //??????????????????
                $this->orderInfoService->updateOrderInfoById($order->order_id,['shipping_status' => 0]);
                return [
                    'code' => 200,
                    'detail' => '????????????????????????????????????',
                ];
            }
            else
            {
                //??????????????????
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
    					'description' => '????????????',
    		        );
    		        $this->walletService->store($walletData);
    				$tradeData = array(
    					'wallet_type' => 1,
    					'trade_type' => 'CancelTask',
    					'description' => '????????????',
    					'trade_status' => 'refunded',
    				);
    				$param['status'] = 'cancelled';
    				$this->walletService->updateWallet($order->owner_id,$this->user->wallet + $order->fee);
    				$this->tradeAccountService->updateTradeAccount($order->order_sn,$tradeData);
    				//????????????
    	        	$this->orderService->updateOrderStatus($param);
    	        	return [
    					'code' => 200,
    					'detail' => '??????????????????????????????????????????????????????????????????',
    	        	];
    			}
    	   		else{
    		   		$tradeData = array(
    					'wallet_type' => 1,
    					'trade_type' => 'CancelTask',
    					'trade_status' => 'refunding',
    					'description' => '????????????',
    				);
    				$param['status'] = 'cancelling';
    				$this->tradeAccountService->updateTradeAccount($order->order_sn,$tradeData);
    				//????????????
    	        	$this->orderService->updateOrderStatus($param);
    	        	return [
    					'code' => 200,
    					'detail' => '??????????????????????????????????????????',
    	        	];
    	   		}
            }
		}
		throw new \App\Exceptions\Custom\OutputServerMessageException('??????????????????????????????');
        //?????????????????????????????????????????????????????????????????????
        // $this->messageService->SystemMessage2OtherOfOrder();

      //  throw new \App\Exceptions\Custom\RequestSuccessException();
    }
    /**
     * (??????)???????????????????????????
     */
    public function agreeCancel(Request $request)
    {
        # code...
    }

    /**
     * (??????)??????????????????????????????
     */
    public function getCourierList(Request $request)
    {
        # code...
    }

    /**
     * (??????)????????????????????????
     */
    public function chooseCourier(Request $request)
    {
        # code...
    }

    /**
     * ?????????????????????
     */
    public function finishWork(Request $request)
    {
        //??????????????????
        $rule = [
            'order_id' => 'required|integer',
        ];
        $this->helpService->validateParameter($rule);

		$this->user = $this->userService->getUser();

        $order = $this->orderService->getSingleOrder($request->order_id);
        //????????????????????????
        if ($order->status != 'accepted') {
            throw new \App\Exceptions\Custom\OutputServerMessageException('???????????????????????????????????????');
        }

        $param = [
            'order_id' => $request->order_id,
            'status' => 'finish',
        ];
        $this->orderService->updateOrderStatus($param);
        if($order->type == 'canteen' || $order->type == 'business')
        {
            $this->orderService->updateUserOrderBonus($this->user);
        }
        //?????????????????????
        $owner_id = $this->orderService->getSingleOrderAllInfo($request->order_id)->owner_id;
        $this->messageService->SystemMessage2SingleOne($owner_id, trans('task.task_be_finished'));


        //??????????????????
		$data = [
			'refresh' => 1,
			'target' => '',
			'open' => 'task',
			'data' => [
				'id' => $request->order_id,
				'title' => '????????????',
				'content' => trans('task.task_be_finished'),
			],
		];
        $this->pushService->PushUserTokenDevice('????????????', trans('task.task_be_finished'), $owner_id,2,$data);

        throw new \App\Exceptions\Custom\RequestSuccessException();
    }

    /**
     * ?????????????????????
     */
    public function confirmFinishWork(Request $request)
    {
        //??????????????????
        $rule = [
            'order_id' => 'required|integer',
            'pay_password' => 'required|string',
        ];
        $this->helpService->validateParameter($rule);

		$this->user = $this->userService->getUser();
        if(!$this->user->pay_password){
			return [
				'code' => 3001,
				'detail' => '?????????????????????',
			];
		}
		if (!password_verify($request->pay_password, $this->user->pay_password)) {
		 	throw new \App\Exceptions\Custom\OutputServerMessageException('??????????????????');
		}
        $order = $this->orderService->getSingleOrder($request->order_id);
        //???????????????????????????
		if($order->type == 'business' || $order->type == 'canteen'){
            $order_info = $this->orderInfoService->isExistsOrderInfo(['order_id' => $order->order_id]);
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
     * ????????????
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
		/*
        $data = [
            'refresh' => 1,
            'target' => '',
            'open' => 'task',
            'data' => [
                'id' => '1131',
                'title' => 'hajaha',
                'content' => 'asdfasdfasdf',
            ],
        ];

        $ret = $this->pushService->PushUserTokenDeviceList('????????????', '????????????', ['2jus5aQfz37BfyVGBep8lodefOBkV1UYeKxQqi5hzLY='],2,'xiaomi',$data);
        var_dump($ret);exit;
		*/
		$data = [
			'refresh' => 1,
			'target' => 'topic',
			'open' => '',
			'data' => [
				'url' => '',
				'num' => 1,
				'title' => '?????????',
				'content' => '?????????',
			],
		];

$ret = $this->pushService->PushUserTokenDevice('????????????', '????????????', 957,2,$data);
	var_dump($ret);exit;
		$custom = [
			'open' => 'task',
			'data' => [
				'id' => '1094',
			],
		];
		$custom = [
			'open' => 'task',
			'data' => [
				'id' => '1094',
			],
		];
        $ret = $this->pushService->PushUserTokenDevice('????????????', '????????????', '957',1,$custom);
		//$this->messageService->SystemMessage2SingleOne('77', '?????????');
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

        //????????????????????????
        $order = $this->orderRepository->getSingleOrderBySn($request->order_sn);

        if(!$order){
	        return [
	        	'code' => 400,
	        	'description' => '???????????????',
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
		//??????????????????
        $rule = [
            'token' => 'required',
        ];
        $this->helpService->validateParameter($rule);

		$this->user = $this->userService->getUser();

		$remindOrder = $this->orderService->remindOrder();
        if($remindOrder == 200){
			return [
				'code' => 200,
				'detail' => '????????????',
        	];
		}else{
			return [
				'code' => 304,
				'detail' => '???????????????',
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
    public function getOrderBonus(Request $request)
    {
        $rule = [
            'token' => 'required',
        ];
        $this->helpService->validateParameter($rule);

		$user = $this->userService->getUser();

        $weekOrderBonus = $this->orderService->getOrderBonus($user->uid);

        return [
            'code' => 200,
            'date' => $weekOrderBonus
        ];
    }
    public function getOrderBonusSetting()
    {
        $bonus_setting = OrderBonusSetting::orderBy('id','asc')->get();
        return [
            'code' => 200,
            'date' => $bonus_setting
        ];
    }
}
