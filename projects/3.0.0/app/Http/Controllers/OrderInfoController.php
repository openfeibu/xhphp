<?php

namespace App\Http\Controllers;

use DB;
use App\Cart;
use App\OrderGoods;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\PayService;
use App\Services\UserService;
use App\Services\GoodsService;
use App\Services\ShopService;
use App\Services\CartService;
use App\Services\HelpService;
use App\Services\OrderService;
use App\Services\OrderInfoService;
use App\Services\UserAddressService;
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

	protected $orderInfoService;

	protected $payService;

	protected $user;

	public function __construct (UserService $userService,
								ShopService $shopService,
								GoodsService $goodsService,
								CartService $cartService,
								HelpService $helpService,
								OrderService $orderService,
								UserAddressService $userAddressService,
								WalletService $walletService,
                         		TradeAccountService $tradeAccountService,
								PayService $payService,
								OrderInfoService $orderInfoService,
								CouponService $couponService)
	{
		parent::__construct();
		$this->middleware('auth');
		$this->payService = $payService;
		$this->userService = $userService;
		$this->goodsService = $goodsService ;
		$this->shopService = $shopService ;
		$this->cartService = $cartService;
	 	$this->helpService = $helpService;
	 	$this->orderService = $orderService;
	 	$this->orderInfoService = $orderInfoService;
	 	$this->userAddressService = $userAddressService;
	 	$this->walletService = $walletService;
        $this->tradeAccountService = $tradeAccountService;
		$this->couponService = $couponService;
	 	$this->user = $this->userService->getUser();
	}

	public function index(Request $request)
	{
		$rule = [
            'page' => 'required|integer',
            'type' => 'required|in:waitpay,beship,shipping,succ,cancell',
        ];
        $this->helpService->validateParameter($rule);

        $order_infos = $this->orderInfoService->getOrderInfos($this->user->uid,$request->type);
        return [
			'code' => 200,
			'order_infos' => $order_infos
        ];
	}
	public function sellerOrderInfos (Request $request)
	{
		$rule = [
            'page' => 'required|integer',
            'type' => 'required|in:beship,shipping,succ,cancell',
        ];
        $this->helpService->validateParameter($rule);
		$shop = $this->shopService->isExistsShop(['uid' => $this->user->uid]);
		$order_infos = $this->orderInfoService->getShopOrderInfos($shop->shop_id,$request->type);
        return [
			'code' => 200,
			'order_infos' => $order_infos
        ];
	}
	public function create(Request $request)
	{
		$rules = [
        	'shop_id'  => 'required|integer',
    	];
    	$this->helpService->validateParameter($rules);
    	$user_address = $this->userAddressService->getUserAddress(['uid' => $this->user->uid]);
		$pay = config('pay');
		$carts = $this->cartService->getShopCarts($request->shop_id,$this->user->uid);
    	$shop = $this->shopService->getShop(['shop_id' => $request->shop_id],['min_goods_amount','shipping_fee','shop_name','shop_img','shop_type']);
		$total_fee = $goods_amount = $carts['shop_total'];
		$shipping_fee = 0;
		if($shop->shop_type == 1)
		{
			if($goods_amount < $shop->min_goods_amount){
				$shipping_fee = $shop->shipping_fee;
			}
		}elseif($shop->shop_type ==2)
		{
			$shipping_fee = $this->helpService->getBuyerShippingFee($carts['weight'],$total_fee);
		}else if($shop->shop_type == 3){
			$shipping_fee = $this->helpService->getCanteenShippingFee($carts['weight'],$goods_amount);
			$seller_shipping_fee = $this->helpService->getCanteenShippingFee($carts['weight'],$goods_amount,'seller');
		}

		$shipping_adjust_fee = $this->helpService->getShippingAdjustFee();
		//提交订单界面的配送费跟提交订单入库的配送费不一样，切记
		$total_fee += $shipping_adjust_fee;
		$total_fee += $shipping_fee;

		$adjust_content = $this->helpService->getShippingAdjustContent();

		$coupons = $this->couponService->getOrderInfoCoupons(['user_coupon.uid' => $this->user->uid],$goods_amount);

		$count = $this->cartService->getCount(['uid' => $this->user->uid,'shop_id' => $request->shop_id]);
		return [
            'code' => 200,
            'shop_name' =>  $shop->shop_name,
			'shop_img' => $shop->shop_img,
            'user_address' => $user_address,
            'pay' => $pay,
        	'carts' => $carts['carts'],
        	'total_fee' => $total_fee,
        	'shipping_fee' => $shipping_fee,
			'weight' => $carts['weight'],
        	'goods_count' => $count,
			'coupons' => $coupons,
			'adjust_content' => $adjust_content,
			'adjust_fee' => $shipping_adjust_fee,
        ];
	}
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
        	'token' 	=> 'required',
        	'shop_id'  => 'required|integer',
        	'pay_id' 	=> "required|integer|in:1,2,3",
        	'pay_password' => 'sometimes|required|string',
			'user_coupon_id' => 'sometimes|integer|string',
        	'address_id' => 'required|integer',
			'platform' => 'required|in:and,ios,wap,wechat',
			'raise_fee' => 'sometimes',
    	];

    	$this->helpService->validateParameter($rules);
		$this->user = $this->userService->getUser();
		$user_address = $this->userAddressService->getUserAddress(['address_id' => $request->address_id,'uid' => $this->user->uid]);
		if(!$user_address){
			throw new \App\Exceptions\Custom\OutputServerMessageException('收货地址不存在');
		}
    	$alipayInfo = $this->userService->getAlipayInfo($this->user->uid);

		if($request->pay_id == 3 && !$alipayInfo->is_paypassword){
			return [
				'code' => 3001,
				'detail' => '未设置支付密码',
			];
		}

		$order_sn = $this->helpService->buildOrderSn('SP');
		$count = $this->cartService->getCount(['uid' => $this->user->uid,'shop_id' => $request->shop_id]);
		if(!$count){
			throw new \App\Exceptions\Custom\OutputServerMessageException('未存在该购物车');
		}
		//$carts = $this->cartService->getShopCarts($request->shop_id,$this->user->uid);
		$carts = $this->cartService->checkGoodsNumber($request->shop_id,$this->user->uid);
		$shop = $this->shopService->getShop(['shop_id' => $request->shop_id]);
		if($shop->shop_type == 3){
			//描述
			$description = sprintf(trans('task.canteen_task_decription'), $carts['goods_count'] ,$carts['original_shop_total']);
		}else{
			//描述
			$description = sprintf(trans('task.task_decription'), $carts['goods_count'] ,$carts['weight']);
		}


		$total_fee = $goods_amount = $carts['shop_total'];

		$shop_user = $this->userService->getUserByUserID($shop->uid);
		buyerHandle($shop);
		$shipping_adjust_fee = $this->helpService->getShippingAdjustFee();
		//额外费 + 自愿加价
		$raise_fee = isset($request->raise_fee) ? $request->raise_fee : $shipping_adjust_fee;
		if($raise_fee < $shipping_adjust_fee){
			throw new \App\Exceptions\Custom\OutputServerMessageException('额外费用错误');
		}
		//使用优惠券
		$user_coupon_id = isset($request->user_coupon_id) ? intval($request->user_coupon_id): 0;
		$coupon = [];
		if($user_coupon_id)
		{
			$coupon = $this->couponService->getOrderInfoCoupon(['user_coupon.uid' => $this->user->uid,'user_coupon_id' => $user_coupon_id],$goods_amount);
			$total_fee = $coupon ? $total_fee - $coupon->price : $total_fee;
		}

		$shipping_fee = 0;
		if($shop->shop_type == 1)
		{
			//学生店铺
			if($goods_amount < $shop->min_goods_amount){
				$shipping_fee = $shop->shipping_fee;
			}
			$seller_shipping_fee = $this->helpService->getSellerShippingFee($carts['weight'],$goods_amount);
		}else if($shop->shop_type == 2)
		{
			//外面商家
			$shipping_fee = $this->helpService->getBuyerShippingFee($carts['weight'],$goods_amount);
			$seller_shipping_fee = $this->helpService->getSellerShippingFee($carts['weight'],$goods_amount);
		}else if($shop->shop_type == 3){
			$shipping_fee = $this->helpService->getCanteenShippingFee($carts['weight'],$goods_amount);
			$seller_shipping_fee = $this->helpService->getCanteenShippingFee($carts['weight'],$goods_amount,'seller');
		}
		$shipping_fee += $request->raise_fee;
		$raise_fee = $raise_fee - $shipping_adjust_fee;

		$total_fee += $shipping_fee;

		$adjust_content = $this->helpService->getShippingAdjustContent();

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

		//$is_show = $shop->shop_type == 'canteen' ? 0 : 1;
        $order_info = $this->orderInfoService->create([
        											'order_sn' => $order_sn,
        											'uid' => $this->user->uid,
        											'shop_id' => $request->shop_id,
        											'consignee' => $user_address->consignee,
        											'address' => $user_address->address,
        											'mobile' => $user_address->mobile,
        											'postscript' => $request->postscript,
        											'pay_id' => $request->pay_id,
        											'pay_name' => trans('common.pay_name.'.$request->pay_id),
        											'goods_amount' => $goods_amount,
        											'total_fee' => $total_fee,
        											'shipping_fee' => $shipping_fee,
													'seller_shipping_fee' => $seller_shipping_fee,
													'user_coupon_id' => $user_coupon_id,
													'description' => $description,
													'raise_fee' => $raise_fee,
													'shipping_adjust_fee' => $shipping_adjust_fee
													//'is_show' => $is_show,
        										]);
		$order_info->original_goods_amount = $carts['original_shop_total'];
		$this->couponService->updateUserCoupon(['uid' => $this->user->uid,'user_coupon_id' => $user_coupon_id],['status' => 'used']);
        $pay_platform = isset($request->platform) ? $request->platform : 'wap';
        $data = [
			'order_info' => $order_info,
			'shop_user' => $shop_user,
        	'return_url' => config('common.order_info_return_url').'?order_id='. $order_info->order_id,
        	'order_sn' => $order_sn,
        	'order_id' => $order_info->order_id,
        	'subject' => '校汇商店订单',
        	'body' => '校汇商店订单',
        	'total_fee' => $total_fee,
        	'trade_type' => 'Shopping',
        	'mobile_no' => $shop_user->mobile_no,
			'pay_id' => $request->pay_id,
			'shop' => $shop,
			'pay_from' => 'shop',
			'pay_platform' => $pay_platform,
			'adjust_content' => $adjust_content,
			'adjust_fee' => $shipping_adjust_fee,
        ];

        $data = $this->payService->payHandle($data);
        return [
			'code' => 200,
			'order_id' => $order_info->order_id,
			'data' => $data
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $rules = [
        	'order_id' => 'required|integer|exists:order_info,order_id',
        	'type'	   => 'sometimes|in:buyer,seller'
        ];
        $is_exists = $this->orderInfoService->isExistsOrderInfo(['order_id' => $request->order_id],$columns = ['order_id']);
        $type = isset($request->type) ?  $request->type : 'buyer';
        $order_info = $this->orderInfoService->getOrderInfo($request->order_id,$type);
        return [
        	'code' => 200,
			'order_info' => $order_info,
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $rules = [
        	'token' 	=> 'required',
			'order_id'  => 'required|exists:order_info,order_id',
    	];
    	$this->helpService->validateParameter($rules);

		$this->orderInfoService->checkCancel($request->order_id,$this->user->uid);

    	$this->orderInfoService->destroy(['order_id' => $request->order_id ,'uid' => $this->user->uid]);

    	throw new \App\Exceptions\Custom\RequestSuccessException('成功取消订单');
    }
    public function pay (Request $request)
    {
    	$rules = [
        	'token' 	=> 'required',
			'order_id'  => 'required|exists:order_info,order_id',
			'pay_id' 	=> "required|integer|in:1,2,3",
        	'pay_password' => 'sometimes|required|string',
			'platform' => 'required|in:and,ios,wap,wechat',
    	];
		$this->helpService->validateParameter($rules);
		$this->user = $this->userService->getUser();
		$order_info = $this->orderInfoService->checkPay($request->order_id,$this->user->uid);
		$alipayInfo = $this->userService->getAlipayInfo($this->user->uid);
		if($request->pay_id == 3 ){
			if(!$alipayInfo->is_paypassword)
			{
				return [
					'code' => 3001,
					'detail' => '未设置支付密码',
				];
			}
			if (!password_verify($request->pay_password, $this->user->pay_password)) {
				throw new \App\Exceptions\Custom\OutputServerMessageException('支付密码错误');
			}
			$wallet = $this->user->wallet;
			if($order_info->total_fee > $wallet){
				return [
					'code' => '110',
					'detail' => '余额（'.$this->user->wallet.'）不足,请选择其他支付方式',
				];
			}
		}
    	$pay_platform = isset($request->platform) ? $request->platform : 'wap';
		$shop = $this->shopService->getShop(['shop_id' => $order_info->shop_id]);
		$shop_user = $this->userService->getUserByUserID($shop->uid);
		$data = [
			'order_info' => $order_info,
			'shop_user' => $shop_user,
        	'return_url' => config('common.order_info_return_url').'?order_id='. $order_info->order_id,
        	'order_sn' => $order_info->order_sn,
        	'order_id' => $order_info->order_id,
        	'subject' => '校汇商店订单',
        	'body' => '校汇商店订单',
        	'total_fee' => $order_info->total_fee,
        	'trade_type' => 'Shopping',
        	'mobile_no' => $shop_user->mobile_no,
			'pay_id' => $request->pay_id,
			'shop' => $shop,
			'pay_from' => 'shop',
			'pay_platform' => $pay_platform,
        ];
		$this->orderInfoService->updateOrderInfo($order_info->order_sn,['pay_id' => $request->pay_id,'pay_name' => trans('common.pay_name.'.$request->pay_id)]);
        $data = $this->payService->payHandle($data);

        return [
			'code' => 200,
			'data' => $data
        ];

    }
    public function refund(Request $request)
    {
    	$rules = [
        	'token' 	=> 'required',
			'order_id'  => 'required|exists:order_info,order_id',
    	];
    	$this->helpService->validateParameter($rules);

    	$order_info = $this->orderInfoService->checkRefund($request->order_id,$this->user->uid);

		$shop = $this->shopService->isExistsShop(['shop_id' => $order_info->shop_id]);
		if($shop->shop_type == 3)
		{
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
		        $this->orderService->delete(['order_id' => $order_info->order_id]);
				$this->tradeAccountService->updateTradeAccount($order_info->order_sn,$tradeData);
				$this->orderInfoService->inGoodsNumber($order_info->order_id);
				$this->couponService->updateUserCoupon(['uid' => $order_info->uid,'user_coupon_id' => $order_info->user_coupon_id],['status' => 'unused']);
				throw new \App\Exceptions\Custom\RequestSuccessException('操作成功，退款金额已返回校汇钱包');
			}
		}else{
			$this->orderInfoService->updateOrderInfoById($order_info->order_id,['order_status' => 3,'cancelling_time' => dtime()]);
		}

    	throw new \App\Exceptions\Custom\RequestSuccessException('请等待商家退款，退款金额将返回校汇钱包');
    }
    public function agreeCancel(Request $request)
    {
    	$rules = [
        	'token' 	=> 'required',
			'order_id'  => 'required|exists:order_info,order_id',
    	];
    	$this->helpService->validateParameter($rules);

		$shop = $this->shopService->isExistsShop(['uid' => $this->user->uid]);

		$order_info = $this->orderInfoService->sellerCheckRefund($request->order_id,$shop->shop_id);

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

    }
    public function shipping (Request $request)
    {
    	$rules = [
        	'token' 	=> 'required',
			'order_id'  => 'required|exists:order_info,order_id',
    	];
    	$this->helpService->validateParameter($rules);

		$shop = $this->shopService->isExistsShop(['uid' => $this->user->uid]);

		$order_info = $this->orderInfoService->sellerCheckShipping($request->order_id,$shop->shop_id);

		//商家
		if($shop->shop_type == 2 || $shop->shop_type == 3){
			//生成任务
			$order_data = $this->orderInfoService->createOrder($order_info,$shop,$this->user);
			$order = $this->orderService->createOrder($order_data);
		}
		$this->orderInfoService->updateOrderInfoById($order_info->order_id,['shipping_status' => 1,'shipping_time' => dtime()]);

		throw new \App\Exceptions\Custom\RequestSuccessException('操作成功');
    }
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
            throw new \App\Exceptions\Custom\OutputServerMessageException('当前任务状态不允许取消');
        }

        $this->orderService->delete(['oid' => $order->oid]);
        //更新订单状态
        $this->orderInfoService->updateOrderInfoById($order->order_id,['shipping_status' => 0]);
        return [
            'code' => 200,
            'detail' => '取消任务成功，请重新发货',
        ];

	}
    public function confirm (Request $request)
    {
    	$rules = [
        	'token' 	=> 'required',
			'order_id'  => 'required|exists:order_info,order_id',
    	];
    	$this->helpService->validateParameter($rules);

    	$order_info = $this->orderInfoService->checkConfirm($request->order_id,$this->user->uid);

		$shop = $this->shopService->isExistsShop(['shop_id' => $order_info->shop_id]);

		$this->orderInfoService->confirm($order_info,$shop,$this->walletService,$this->tradeAccountService);

		$task = $this->orderService->getOrder(['order_id' => $order_info->order_id],['*'],false);

		if($task) {
			$task->uid = $shop->uid;
			$this->orderService->confirmFinishWork($task,$this->walletService,$this->tradeAccountService);
		}

    	throw new \App\Exceptions\Custom\RequestSuccessException('确认成功');
    }
	public function check_pick_code(Request $request)
	{
		$rules = [
        	'token' 	=> 'required',
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
}
