<?php

namespace App\Services;

use Log;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Repositories\CartRepository;

class CartService
{
	protected $request;

    protected $cartRepository;

    protected $userRepository;

	function __construct(Request $request,
						 CartRepository $cartRepository,
						 UserRepository $userRepository)
	{
		$this->request = $request;
        $this->cartRepository = $cartRepository;
        $this->userRepository = $userRepository;

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
	public function getCarts($uid)
	{
		return  $this->cartRepository->getCarts($uid);
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
}