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
		
        $order_infos = $this->orderInfoService->getOrderInfos($request->page,$this->user->uid,$request->type);
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
    	$shop = $this->shopService->getShop($request->shop_id,['min_goods_amount','shipping_fee','shop_name','shop_img']);
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
		$shop = $this->shopService->getShop($request->shop_id);
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
        $where = ['shop_id' => $request->shop_id,'uid' => $this->user->uid ];
        $goodses = 	Cart::select(DB::raw("$order_info->order_id as order_id ,goods_name,goods_price,goods_sn,goods_id,goods_number"))->where($where)->get()->toArray();
        DB::table('order_goods')->insert($goodses);
    	//$this->cartService->removeCarts($where);									
        $pay_platform = isset($request->platform) ? $request->platform : 'web';	
        $data = [
        	'return_url' => config('common.order_info_return_url'),
        	'order_sn' => $order_sn,
        	'subject' => '校汇商店订单',
        	'body' => '校汇商店订单', 
        	'total_fee' => $total_fee,
        	'trade_type' => 'Shopping',
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
        	'order_id' => 'required|integer',
        ];
        $order_info = $this->orderInfoService->getOrderInfo($request->order_id);
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
    public function destroy($id)
    {
        //
    }
}
