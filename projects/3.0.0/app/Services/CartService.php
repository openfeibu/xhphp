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
		$carts = $this->cartRepository->getShopCarts($shop_id,$uid);
		$shop_total = $weight = 0;
		foreach( $carts as $k => $cart )
		{
			$goods = $this->goodsService->existGoods($cart->goods_id);
			if(!$goods){
				$this->removeCartGoods([$cart->cart_id],$uid);
				return $this->getShopCarts($shop_id,$uid);
			}
			$goods_total = $cart->goods_price * $cart->goods_number;
			$goods_weight = $goods->weight * $cart->goods_number;
			$cart->goods_thumb = $goods->goods_thumb;
			$cart->goods_img = $goods->goods_img;
			$cart->goods_total = $goods_total;
			$shop_total += $goods_total;
			$weight += $goods_weight;
		}
		return [
			'carts' => $carts,
			'weight' => $weight,
			'shop_total' => $shop_total,
		];
	}
	public function checkGoodsNumber($shop_id,$uid)
	{
		$carts = $this->cartRepository->getShopCarts($shop_id,$uid);
		$shop_total = $weight = $goods_count = 0;
		$str = "";
		foreach( $carts as $k => $cart )
		{
			$goods = $this->goodsService->existGoods($cart->goods_id);
			$goods_total = $cart->goods_price * $cart->goods_number;
			if($goods->goods_number <= 0){
				$str.= '商品 '.$goods->goods_name.' 已售罄；';
			}else if($cart->goods_number > $goods->goods_number){
				$str.= '商品 '.$goods->goods_name.' 选择的数量超出库存,最多可购买'.$goods->goods_number.'件';
			}
			$cart->goods_thumb = $goods->goods_thumb;
			$cart->goods_img = $goods->goods_img;
			$cart->goods_total = $goods_total;
			$shop_total += $goods_total;
			$goods_weight = $goods->weight * $cart->goods_number;
			$weight += $goods_weight;
			$goods_count += $cart->goods_number;
		}
		if($str){
			throw new \App\Exceptions\Custom\OutputServerMessageException($str);
		}
		return [
			'carts' => $carts,
			'weight' => $weight,
			'shop_total' => $shop_total,
			'goods_count' => $goods_count,
		];
	}
	public function getShop ($uid)
	{
		return $this->cartRepository->getShop($uid);
	}
	public function getCount ($where)
	{
		return $this->cartRepository->getCount($where);
	}
	public function existCart ($where)
	{
		return $this->cartRepository->existCart($where);
	}
	public function removeCartGoods ($ids,$uid)
	{
		return $this->cartRepository->removeCartGoods($ids,$uid);
	}
	public function removeCarts ($where)
	{
		return $this->cartRepository->removeCarts($where);
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
		$cartShops = $this->getShop($uid);
		$total = 0;
		$arrCarts = array();
		foreach( $cartShops as $key => $cartShop )
		{
			$carts = $this->getShopCarts($cartShop->shop_id,$uid);
			$shopDetail = $this->shopService->getShop(['shop_id' => $cartShop->shop_id]);
			$arrCarts[$cartShop->shop_id] = array(
				'shop_name' 	=> $shopDetail->shop_name,
				'shop_id'		=> $shopDetail->shop_id,
				'shop_status'	=> $shopDetail->shop_status,
				'shop_status_description' => trans("common.shop_status.$shopDetail->shop_status"),
			);
			$shop_total = 0;
			foreach( $carts['carts'] as $k => $cart )
			{
				$goodsDetail = $this->goodsService->existGoods($cart->goods_id);
				$goods_total = $cart->goods_price * $cart->goods_number;
				$arrCarts[$cartShop->shop_id]['carts'][$cart->cart_id] = array(
					'goods_img'  	=> $goodsDetail->goods_img,
					'goods_name' 	=> $cart->goods_name,
					'goods_desc' 	=> $cart->goods_desc,
					'goods_id'	 	=> $cart->goods_id,
					'goods_price'	=> $cart->goods_price,
					'goods_number'	=> $cart->goods_number,
					'cart_id'		=> $cart->cart_id,
					'goods_total'	=> $goods_total,
				);

			}
			$arrCarts[$cartShop->shop_id]['shop_total'] =  $carts['shop_total'];
			$total += $shop_total;
		}
		return [
			'carts' => $arrCarts,
			'total' => $total,
		];
	}
}
