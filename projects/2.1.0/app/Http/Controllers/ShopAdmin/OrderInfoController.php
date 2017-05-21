<?php

namespace App\Http\Controllers\ShopAdmin;

use DB;
use App\Cart;
use App\OrderGoods;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\ShopAdmin\Controller;
use App\Services\PayService;
use App\Services\UserService;
use App\Services\GoodsService;
use App\Services\ShopService;
use App\Services\HelpService;
use App\Services\OrderService;
use App\Services\OrderInfoService;
use App\Services\TradeAccountService;
use App\Services\WalletService;
use App\Services\CouponService;

class OrderInfoController extends Controller
{
	protected $helpService;

	protected $goodsService;

	protected $shopService;

	protected $userService;

	protected $cartService;

	protected $orderService;

	protected $orderInfoService;

	protected $payService;

	protected $user;

	public function __construct (UserService $userService,
								ShopService $shopService,
								GoodsService $goodsService,
								HelpService $helpService,
								WalletService $walletService,
                         		TradeAccountService $tradeAccountService,
								OrderService $orderService,
								OrderInfoService $orderInfoService,
								CouponService $couponService)
	{

		parent::__construct($shopService);
		$this->userService = $userService;
		$this->goodsService = $goodsService ;
		$this->shopService = $shopService ;
	 	$this->helpService = $helpService;
	 	$this->orderInfoService = $orderInfoService;
	 	$this->walletService = $walletService;
        $this->tradeAccountService = $tradeAccountService;
		$this->orderService = $orderService ;
		$this->couponService = $couponService;
	}
	public function orderInfos (Request $request)
	{
		$rule = [
            'page' => 'required|integer',
            'type' => 'required|in:beship,shipping,succ,cancell,all',
        ];
        $this->helpService->validateParameter($rule);
		$order_infos = $this->orderInfoService->getShopOrderInfos($this->shop->shop_id,$request->type,20);
        return [
			'code' => 200,
			'count' => count($order_infos),
			'order_infos' => $order_infos
        ];
	}
	/**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getOrderInfo(Request $request)
    {
        $rules = [
        	'order_id' => 'required|integer|exists:order_info,order_id',
        ];
        $is_exists = $this->orderInfoService->isExistsOrderInfo(['order_id' => $request->order_id],$columns = ['order_id']);
        $order_info = $this->orderInfoService->getOrderInfo($request->order_id);

        return [
        	'code' => 200,
			'order_info' => $order_info,
        ];
    }
	public function shipping (Request $request)
    {
    	$rules = [
			'order_id'  => 'required|exists:order_info,order_id',
    	];
    	$this->helpService->validateParameter($rules);

		$order_info = $this->orderInfoService->sellerCheckShipping($request->order_id,$this->shop->shop_id);

		//商家
		if($this->shop->shop_type == 2){
			//创建新任务
			$order_sn = $this->helpService->buildOrderSn('RT');
			$total_fee = $order_info->shipping_fee + $order_info->seller_shipping_fee;
			$service_fee = $this->helpService->serviceFee($total_fee) ;
			//提货码
			$pick_code = $this->orderInfoService->getPickCode();
			$this->orderInfoService->updateOrderInfo($order_info->order_sn,['pick_code' => $pick_code]);
			//生成任务

        	$order = $this->orderService->createOrder(['destination' => $order_info->address,
                                             'description' => $this->shop->shop_name.' '.$this->shop->college_name.' '.$order_info->description,
                                             'fee' => $total_fee,
                                             'goods_fee' => 0 ,
                                             'total_fee' => $total_fee,
                                             'service_fee' => $service_fee,
                                             'phone' => $order_info->mobile ? $order_info->mobile : $this->user->mobile_no,
                                             'order_sn' => $order_sn,
                                             'status' => 'new',
                                             'pay_id' => $order_info->pay_id,
                                             'type' => 'business',
                                             'order_id' => $order_info->order_id,
											 'uid' => $this->user->uid
                                            ]);
		}
		$this->orderInfoService->updateOrderInfoById($order_info->order_id,['shipping_status' => 1,'shipping_time' => dtime()]);

		throw new \App\Exceptions\Custom\RequestSuccessException('操作成功');
    }
	/*撤回发货*/
	public function revokeShipping(Request $request)
	{
		$rule = [
            'order_id' => 'required|integer',
        ];
        $this->helpService->validateParameter($rule);

		$this->user = $this->userService->getUser();

		$shop = $this->shopService->isExistsShop(['uid' => $this->user->uid]);

		$order_info = $this->orderInfoService->isExistsOrderInfo(['shop_id' => $shop->shop_id,'order_id' => $request->order_id],['order_id']);

		$order = $this->orderService->isExistsOrderColumn(['order_id' => $order_info->order_id,'type' => 'business','owner_id' => $this->user->uid]);

        if ($order->status != 'new') {
            throw new \App\Exceptions\Custom\OutputServerMessageException('当前订单状态不允许撤回发货');
        }

        $this->orderService->delete(['oid' => $order->oid]);
        //更新订单状态
        $this->orderInfoService->updateOrderInfoById($order->order_id,['shipping_status' => 0]);
        return [
            'code' => 200,
            'detail' => '取消任务成功，请重新发货',
        ];

	}
	/*
	 	检查提货码
	*/
	public function checkPickCode(Request $request)
	{
		$rules = [
			'pick_code'  => 'required',
    	];
    	$this->helpService->validateParameter($rules);
		$shop = $this->shopService->isExistsShop(['uid' => $this->user->uid]);
		$order_info = $this->orderInfoService->isExistsOrderInfo(['shop_id' => $shop->shop_id,'pick_code' => $request->pick_code],['order_id']);
		if(!$order_info)
		{
			return [
				'code' => 201,
				'detail' => '订单不存在',
				'data' => [],
			];
		}else{
			$order_info = $this->orderInfoService->getOrderInfo($order_info->order_id,'seller');
			return [
				'code' => 200,
				'detail' => '订单存在',
				'data' => $order_info,
			];
		}
	}
    public function agreeCancel(Request $request)
    {
    	$rules = [
			'order_id'  => 'required|exists:order_info,order_id',
    	];
    	$this->helpService->validateParameter($rules);


		$order_info = $this->orderInfoService->sellerCheckRefund($request->order_id,$this->shop->shop_id);

		$user = $this->userService->getUserByUserID($order_info->uid);

		$fee = 	$user->wallet + $order_info->total_fee;

        $this->walletService->updateWallet($user->uid,$fee);

       	$walletData = array(
			'uid' => $user->uid,
			'wallet' => $fee,
			'fee'	=> $order_info->total_fee,
			'service_fee' => 0,
			'out_trade_no' => $order_info->order_sn,
			'pay_id' => 3,
			'wallet_type' => 1,
			'trade_type' => 'CancelOrder',
			'description' => '取消订单',
        );
        $this->walletService->store($walletData);
        $tradeData = array(
			'wallet_type' => 1,
			'trade_type' => 'CancelOrder',
			'description' => '取消订单',
			'trade_status' => 'income',
		);

		$update = $this->orderInfoService->updateOrderInfoById($order_info->order_id,['order_status' => 4,'shipping_status' => 3,'cancelled_time' => dtime()]);

		if($update)
		{
			$this->tradeAccountService->updateTradeAccount($order_info->order_sn,$tradeData);
			$this->orderInfoService->inGoodsNumber($order_info->order_id);
			$this->couponService->updateUserCoupon(['uid' => $order_info->uid,'user_coupon_id' => $order_info->user_coupon_id],['status' => 'unused']);

			throw new \App\Exceptions\Custom\RequestSuccessException('操作成功，退款金额将返回用户钱包');
		}

		throw new \App\Exceptions\Custom\OutputServerMessageException('操作失败');

    	throw new \App\Exceptions\Custom\RequestSuccessException('操作成功，退款金额将返回用户钱包');
    }
}
