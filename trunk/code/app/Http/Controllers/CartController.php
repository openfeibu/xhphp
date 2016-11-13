<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Services\GoodsService;
use App\Services\ShopService;
use App\Services\CartService;
use App\Services\HelpService;

class CartController extends Controller
{
   	protected $helpService;

	protected $goodsService;

	protected $shopService;
	
	protected $userService;

	protected $cartService;

	protected $user;
	
	public function __construct (UserService $userService,
								ShopService $shopService,
								GoodsService $goodsService,
								CartService $cartService,
								HelpService $helpService)
	{
		parent::__construct();
		$this->middleware('auth');
		$this->userService = $userService;
		$this->goodsService = $goodsService ;
		$this->shopService = $shopService ;
		$this->cartService = $cartService;
	 	$this->helpService = $helpService;
	 	$this->user = $this->userService->getUser(); 
	}
	public function store (Request $request)
	{
		$user = $this->user;
        $rules = [
        	'token' 	=> 'required',
        	'goods_id' 	=> 'required|string|digits:1',
        	'goods_number' 	=> 'required|string|digits:1'
        ];
		$this->helpService->validateParameter($rules);
        $exitGoods = $this->goodsService->existGoods($request->goods_id,$this->user->uid);
        if(!$exitGoods){
	        throw new \App\Exceptions\Custom\OutputServerMessageException('不存在该商品');
        }
		if(!$exitGoods->is_on_sale){
	        throw new \App\Exceptions\Custom\OutputServerMessageException('操作失败，该商品已禁止销售');
		}
		if($exitGoods->goods_number<1){
	        throw new \App\Exceptions\Custom\OutputServerMessageException('操作失败，库存不足');
		}
		if($request->goods_number>$exitGoods->goods_number){
	        throw new \App\Exceptions\Custom\OutputServerMessageException('操作失败，选择的数量超出库存,最多可购买'.$exitGoods->goods_number.'件');
		}
        $existCartGoods = $this->cartService->existCartGoods($exitGoods,$this->user->uid);
        $goods_number = $exitGoods->goods_number - $request->goods_number;
       	if($existCartGoods){
	       	$cart_goods_number = $existCartGoods->goods_number + $request->goods_number;
	       	if($cart_goods_number>$exitGoods->goods_number){
		       	$mostNumber = $exitGoods->goods_number - $existCartGoods->goods_number;
		        throw new \App\Exceptions\Custom\OutputServerMessageException('操作失败，选择的数量超出库存,最多可购买'.$mostNumber.'件');
			}	       	
	       	$this->cartService->updateCartGoodsNumber($existCartGoods->cart_id,$cart_goods_number,$this->user->uid);
	      // 	$this->cartService->updateGoodsNumber($exitGoods->goods_id,$goods_number);
	       	return [
	            'code' => 200,
	            'detail' => '添加成功'
	        ];	
       	}
        $newCartId = $this->cartService->addToCart($exitGoods,$this->user->uid);
		if($newCartId){
			//$this->cartService->updateGoodsNumber($exitGoods->goods_id,$goods_number);
			return [
	            'code' => 200,
	            'detail' => '添加成功'
	        ];
		}
		return [
			'code' => 500 ,
			'detail' => '系统出错'
		];   	
	}
	public function getCarts (Request $request)
	{
		$user = $this->user;
		$cartShop = $this->cartService->getShop($this->user->uid);
		$total = 0;
		$cart_count = $this->cartService->getCount($this->user->uid);
		foreach( $cartShop as $key => $cartShopValue )
		{
			$carts = $this->cartService->getShopCarts($cartShopValue->shop_id,$this->user->uid);
			$shopDetail = $this->shopService->getShop($cartShopValue->shop_id);
			$arrCarts[$cartShopValue->shop_id] = array(
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
				$arrCarts[$cartShopValue->shop_id]['carts'][$cartsValue->cart_id] = array(
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
			$arrCarts[$cartShopValue->shop_id]['shop_total'] =  $shop_total;
			$total += $shop_total;
		}
		return [
            'code' => 200,
            'data' => [
            	'cart_count' => $cart_count,
            	'total' => $total,
            	'allCarts' => $arrCarts
            ],
        ];
	}
	public function updateCartGoodsNumber (Request $request)
	{
		$rules = [
        	'token' 		=> 'required',
        	'cart_id' 		=> 'required|string|digits:1',
        	'goods_number'	=> 'required|string|digits:1'
    	];
    	$this->helpService->validateParameter($rules);
    	$exitCart = $this->cartService->existCart($request->cart_id,$this->user->uid);
    	if(!$exitCart){
	    	throw new \App\Exceptions\Custom\OutputServerMessageException('购物车不存在');
    	}
    	$goods = $this->goodsService->existGoods($exitCart->goods_id);
    	if($request->goods_number>$goods->goods_number){	
	        throw new \App\Exceptions\Custom\OutputServerMessageException('操作失败，选择的数量超出库存,最多可购买'.$goods->goods_number.'件');
    	}
    	$this->cartService->updateCartGoodsNumber($exitCart->cart_id,$request->goods_number,$this->user->uid);
    	return [
            'code' => 200,
            'data' => [
            	'goods_number' => $request->goods_number,
            	'goods_total'  => $request->goods_number * $exitCart->goods_price,
            ],
        ];
	}
	public function destroyAll (Request $request)
	{
		$rules = [
        	'token' 		=> 'required',
        	'ids' 		=> 'required|string',
    	];
    	$this->helpService->validateParameter($rules);
    	$ids = array_filter(explode(',',$request->ids));
    	$deletedRows = $this->cartService->removeCartGoods($ids,$this->user->uid);
    	return [
            'code' => 200,
            'detail' => '删除成功',
        ];
	}
	public function getTotal (Request $request)
	{
		$rules = [
        	'token' 	=> 'required',
        	'ids' 		=> 'required|string',
    	];
    	$this->helpService->validateParameter($rules);
    	$ids = array_filter(explode(',',$request->ids));
    	$cartGoodses =  $this->cartService->getCartGoodsByIds($ids,$this->user->uid);
    	$total = 0;
    	foreach( $cartGoodses as $key => $value )
    	{
    		$total += $value->goods_price * $value->goods_number;
    	}
    	return [
    		'code'	=> 200,
    		'data'  => [
				'totla' => $total,
    		],
    	];
	}

}