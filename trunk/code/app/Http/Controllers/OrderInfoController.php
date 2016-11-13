<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Services\GoodsService;
use App\Services\ShopService;
use App\Services\CartService;
use App\Services\HelpService;
use App\Services\OrderInfoService;

class OrderInfoController extends Controller
{
	protected $helpService;

	protected $goodsService;

	protected $shopService;
	
	protected $userService;

	protected $cartService;

	protected $orderInfoService;

	protected $user;
	
	public function __construct (UserService $userService,
								ShopService $shopService,
								GoodsService $goodsService,
								CartService $cartService,
								HelpService $helpService,
								OrderInfoService $orderInfoService)
	{
		parent::__construct();
		$this->middleware('auth');
		$this->userService = $userService;
		$this->goodsService = $goodsService ;
		$this->shopService = $shopService ;
		$this->cartService = $cartService;
	 	$this->helpService = $helpService;
	 	$this->orderInfoService = $orderInfoService;
	 	$this->user = $this->userService->getUser(); 
	}

	public function index (Request $request)
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
        	'cart_ids'  => 'required|string',
        	'pay_id' 	=> "required|integer|between:1,3",
            'pay_password' => 'sometimes|required|string|digits:6',
    	];
    	$this->helpService->validateParameter($rules);
    	
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
