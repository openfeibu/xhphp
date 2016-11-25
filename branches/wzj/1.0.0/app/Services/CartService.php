<?php

namespace App\Services;

use Log;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Repositories\CartRepository;
use App\Services\ShopService;
use App\Services\GoodsService;

class CartService
{
	protected $request;

    protected $cartRepository;

    protected $userRepository;

	function __construct(Request $request,
						 CartRepository $cartRepository,
						 UserRepository $userRepository,
						 ShopService $shopService,
						 GoodsService $goodsService)
	{
		$this->request = $request;
        $this->cartRepository = $cartRepository;
        $this->userRepository = $userRepository;
        $this->shopService = $shopService;
        $this->goodsService = $goodsService;
	}
	public function addToCart($goods,$uid)
	{
		return $this->cartRepository->addToCart($goods,$uid);
	}
	public function existCartGoods ($goods,$uid)
	{
		return $this->cartRepository->existCartGoods($goods,$uid);
	}
	public function updateCartGoodsNumber ($cart_id,$goods_number,$uid )
	{
		return $this->cartRepository->updateCartGoodsNumber($cart_id,$goods_number,$uid);
	}
	public function updateGoodsNumber ($goods_id,$goods_number,$uid)
	{
		return $this->cartRepository->updateGoodsNumber($goods_id,$goods_number,$uid);
	}
	public function getShopCarts($shop_id,$uid)
	{
		return $this->cartRepository->getShopCarts($shop_id,$uid);
	}
	public function getShop ($uid)
	{
		return $this->cartRepository->getShop($uid);
	}
	public function getCount ($uid)
	{
		return $this->cartRepository->getCount($uid);
	}
	public function existCart ($cart_id,$uid)
	{
		return $this->cartRepository->existCart($cart_id,$uid);
	}
	public function removeCartGoods ($ids,$uid)
	{
		return $this->cartRepository->removeCartGoods($ids,$uid);
	}
	public function getCartGoodsByIds ($ids,$uid)
	{
		return $this->cartRepository->getCartGoodsByIds($ids,$uid);
	}
	public function getShopIds ($cart_ids,$uid)
	{
		$shop_ids = $this->cartRepository->getShopIds($cart_ids,$uid);
		$shop_ids = array_column($shop_ids,'shop_id');
		return $shop_ids;
	}
	public function getShopCartsByCartIds ($shop_id,$cart_ids,$uid)
	{
		return $this->cartRepository->getShopCartsByCartIds($shop_id,$cart_ids,$uid);
	}
	public function getCarts ($uid)
	{
		$cartShop = $this->getShop($uid);
		$total = 0;
		$arrCarts = array();
		foreach( $cartShop as $key => $cartShopValue )
		{
			$carts = $this->getShopCarts($cartShopValue->shop_id,$uid);
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
			'carts' => $arrCarts,
			'total' => $total,
		];
	}
}