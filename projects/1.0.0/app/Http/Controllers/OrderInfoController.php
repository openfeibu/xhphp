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
use App\Services\OrderInfoService;
use App\Services\UserAddressService;
use App\Services\TradeAccountService;
use App\Services\WalletService;

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
								OrderInfoService $orderInfoService,
								UserAddressService $userAddressService,
								WalletService $walletService,
                         		TradeAccountService $tradeAccountService,
								PayService $payService)
	{
		parent::__construct();
		$this->middleware('auth');
		$this->payService = $payService;
		$this->userService = $userService;
		$this->goodsService = $goodsService ;
		$this->shopService = $shopService ;
		$this->cartService = $cartService;
	 	$this->helpService = $helpService;
	 	$this->orderInfoService = $orderInfoService;
	 	$this->userAddressService = $userAddressService;
	 	$this->walletService = $walletService;
        $this->tradeAccountService = $tradeAccountService;
	 	$this->user = $this->userService->getUser(); 
	}

	/*public function index (Request $request)
	{
		$rules = [
        	'token' 	=> 'required',
        	'cart_ids'  => 'required|string'
    	];
    	$this->helpService->validateParameter($rules);
    	$cart_ids = array_filter(explode(',',$request->cart_ids));
    	$shop_ids = $this->cartService->getShopIds($cart_ids,$this->user->uid);  	
    	if(!count($shop_ids)){
	    	throw new \App\Exceptions\Custom\OutputServerMessageException('参数错误');
    	}
    	$total = 0;
    	foreach( $shop_ids as $key => $shop_id )
    	{    	
    		$carts = $this->cartService->getShopCartsByCartIds($shop_id,$cart_ids,$this->user->uid);
			$shopDetail = $this->shopService->getShop($shop_id);
			$arrCarts[$shop_id] = array(
				'shop_name' 	=> $shopDetail->shop_name,
				'shop_id'		=> $shopDetail->shop_id,
				'shop_status'	=> $shopDetail->shop_status,
				'shop_status_description' => trans("common.shop_status.$shopDetail->shop_status"),
			);
			$shop_total = 0; 
			foreach( $carts as $k => $cartsValue )
			{
				$goodsDetail = $this->goodsService->existGoods($cartsValue->goods_id);
				$goods_total = $cartsValue->goods_price * $cartsValue->goods_number;
				$arrCarts[$shop_id]['carts'][$cartsValue->cart_id] = array(
					'goods_desc' 	=> $goodsDetail->goods_desc ,
					'goods_name' 	=> $goodsDetail->goods_name,
					'goods_img'  	=> $goodsDetail->goods_img,
					'is_on_sale'	=> $goodsDetail->is_on_sale,
					'goods_id'	 	=> $cartsValue->goods_id,				
					'goods_price'	=> $cartsValue->goods_price,
					'goods_number'	=> $cartsValue->goods_number,
					'cart_id'		=> $cartsValue->cart_id,
					'goods_total'	=> $goods_total,
				);
				$shop_total += $goods_total;
			}
			$arrCarts[$shop_id]['shop_total'] =  $shop_total;
			$total += $shop_total;
    	}
    	$pay = config("pay");
    	//$address = 
    	return [
            'code' => 200,
            'data' => [
            	'total' => $total,
            	'allCarts' => $arrCarts,
            	'pay' => $pay
            ],
        ];
	}*/
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
    	$shop = $this->shopService->getShop(['shop_id' => $request->shop_id],['min_goods_amount','shipping_fee','shop_name','shop_img']);
		$total_fee = $goods_amount = $carts['shop_total'];
		$shipping_fee = 0;
		if($goods_amount < $shop->min_goods_amount){
			$total_fee = $goods_amount + $shop->shipping_fee;
			$shipping_fee = $shop->shipping_fee;
		}
		$count = $this->cartService->getCount(['uid' => $this->user->uid,'shop_id' => $request->shop_id]);
		return [
            'code' => 200,
            'shop_name' =>  $shop->shop_name,
            'user_address' => $user_address,
            'pay' => $pay,
        	'carts' => $carts['carts'],
        	'total_fee' => $total_fee,
        	'shipping_fee' => $shipping_fee,
        	'goods_count' => $count
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
        	'pay_id' 	=> "required|integer|in:1,3",
        	'pay_password' => 'sometimes|required|string',
        	'address_id' => 'required|integer',
    	];
    	$this->helpService->validateParameter($rules);

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
		$carts = $this->cartService->getShopCarts($request->shop_id,$this->user->uid);
		$total_fee = $goods_amount = $carts['shop_total'];
		$shop = $this->shopService->getShop(['shop_id' => $request->shop_id]);
		buyerHandle($shop);
		$shipping_fee = 0;
		if($goods_amount < $shop->min_goods_amount){
			$total_fee = $goods_amount + $shop->shipping_fee;
			$shipping_fee = $shop->shipping_fee;
		}
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
        											'shipping_fee' => $shipping_fee
        										]);	        																								
        $pay_platform = isset($request->platform) ? $request->platform : 'web';	
        $data = [
        	'return_url' => config('common.order_info_return_url'),
        	'order_sn' => $order_sn,
        	'subject' => '校汇商店订单',
        	'body' => '校汇商店订单', 
        	'total_fee' => $total_fee,
        	'trade_type' => 'Shopping',
        	'mobile_no' => $this->user->mobile_no
        ];								
        $data = $this->payService->payHandle($request->pay_id,$pay_platform,'shop',$data);		
        return [
			'code' => 200,
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
        ];
        $is_exists = $this->orderInfoService->isExistsOrderInfo(['order_id' => $request->order_id,'uid' => $this->user->uid],$columns = ['order_id']);
        $order_info = $this->orderInfoService->getOrderInfo($request->order_id,$this->user->uid);
        return [
        	'code' => 200,
			'order_info' => $order_info,
        ];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
    	];
    	
    	$this->helpService->validateParameter($rules);
    	
    	$order_info = $this->orderInfoService->checkPay($request->order_id,$this->user->uid);
    	
    	$data = [
        	'return_url' => config('common.order_info_return_url'),
        	'order_sn' => $order_info->order_sn,
        	'subject' => '校汇商店订单',
        	'body' => '校汇商店订单', 
        	'total_fee' => $order_info->total_fee,
        	'trade_type' => 'Shopping',
        	'mobile_no' => $this->user->mobile_no
        ];			
        $data = $this->payService->payHandle($request->pay_id,$pay_platform,'shop',$data);	

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

    	$this->orderInfoService->updateOrderInfoById($order_info->order_id,['order_status' => 3,'cancelling_time' => dtime()]);

    	throw new \App\Exceptions\Custom\RequestSuccessException('请等待商家退款，退款金额将返回钱包');
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
		
		$fee = 	$user->wallet + $user->total_fee;
		
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
		
		$this->tradeAccountService->updateTradeAccount($order_info->order_sn,$tradeData);

		$this->orderInfoService->updateOrderInfoById($order_info->order_id,['order_status' => 4,'shipping_status' => 3,'cancelled_time' => dtime()]);
		
    	throw new \App\Exceptions\Custom\RequestSuccessException('操作成功，退款金额将返回用户钱包');
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

		$this->orderInfoService->updateOrderInfoById($order_info->order_id,['shipping_status' => 1,'shipping_time' => dtime()]);
		
		throw new \App\Exceptions\Custom\RequestSuccessException('操作成功');
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

		$user = $this->userService->getUserByUserID($shop->uid);

		$total_fee = $order_info->total_fee;

		$service_fee = 0;
		
		$fee = 	$user->wallet + $total_fee;
        
       	$walletData = array(
			'uid' => $user->uid,
			'wallet' => $fee,
			'fee'	=> $total_fee,
			'service_fee' => $service_fee,
			'out_trade_no' => $order_info->order_sn,
			'pay_id' => 5,
			'wallet_type' => 1,
			'trade_type' => 'shop',
			'description' => '商店收入',
        );
       	$this->walletService->store($walletData);
		$trade_no = 'walletseller'. $order_info->order_sn;
        $trade = array(
        	'uid' => $user->uid,
			'out_trade_no' =>  $order_info->order_sn,
			'trade_no' => $trade_no,
			'trade_status' => 'success',
			'wallet_type' => 1,
			'from' => 'shop',
			'trade_type' => 'Shop',
			'fee' => $total_fee,
			'service_fee' => $service_fee,
			'pay_id' => 5,
			'description' => '商店收入',
		);
		$this->tradeAccountService->addThradeAccount($trade);
		
		$this->orderInfoService->confirm($order_info->order_id,$shop->shop_id);

		$this->walletService->updateWallet($user->uid,$fee);
		
		$this->shopService->inIncome(['shop_id' => $shop->shop_id],$total_fee);		
		
    	throw new \App\Exceptions\Custom\RequestSuccessException('确认成功');
    }
}
