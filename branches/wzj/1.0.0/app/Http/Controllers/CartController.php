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
        	'goods_id' 	=> 'required|integer',
        	'goods_number' 	=> 'required|integer'
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
	       	return [
	            'code' => 200,
	            'detail' => '添加成功'
	        ];	
       	}
        $newCartId = $this->cartService->addToCart($exitGoods,$this->user->uid);
		if($newCartId){
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
		$carts = $this->cartService->getCarts($this->user->uid);
		$cart_count = $this->cartService->getCount($this->user->uid);
		return [
            'code' => 200,
        	'cart_count' => $cart_count,
        	'total' => $carts['total'],
        	'carts' => $carts['carts'],

        ];
	}
	public function updateCartGoodsNumber (Request $request)
	{
		$rules = [
        	'token' 		=> 'required',
        	'cart_id' 		=> 'required|integer',
        	'goods_number'	=> 'required|integer|min:1'
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
        	'goods_number' => $request->goods_number,
        	'goods_total'  => $request->goods_number * $exitCart->goods_price,
        ];
	}
	public function destroyAll (Request $request)
	{
		$rules = [
        	'token' 		=> 'required',
        	'cart_ids' 		=> 'required|array',
    	];
    	$this->helpService->validateParameter($rules);
    	$cart_ids = $request->cart_ids;
    	$deletedRows = $this->cartService->removeCartGoods($cart_ids,$this->user->uid);
    	return [
            'code' => 200,
            'detail' => '删除成功',
        ];
	}
	public function getTotal (Request $request)
	{
		$rules = [
        	'token' 	=> 'required',
        	'cart_ids' 		=> 'required|array|min:1',
    	];
    	$this->helpService->validateParameter($rules);
    	$cart_ids = $request->cart_ids;
    	$cartGoodses =  $this->cartService->getCartGoodsByIds($cart_ids,$this->user->uid);
    	$total = 0;
    	foreach( $cartGoodses as $key => $value )
    	{
    		$total += $value->goods_price * $value->goods_number;
    	}
    	return [
    		'code'	=> 200,
			'total' => $total,
    	];
	}

}