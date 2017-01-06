<?php

namespace App\Repositories;

use DB;
use Session;
use App\Shop;
use App\Goods;
use App\Cart;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;

class CartRepository
{
	protected $request;

	protected $user;

	protected $userRepository;
	
	public function __construct(Request $request,
								UserRepository $userRepository)
	{
		$this->request = $request;
		$this->userRepository = $userRepository;
	}
	public function addToCart($goods,$uid)
	{
		$cart = new Cart;
		$cart->setConnection('write');
		$cart->uid = $uid;
		$cart->shop_id = $goods->shop_id;	
		$cart->goods_id = $goods->goods_id;		
		$cart->goods_name = $goods->goods_name;                 
		$cart->goods_price = $goods->goods_price;
		$cart->goods_number = $this->request->goods_number;
		$cart->created_at = date('Y-m-d H:i:s');
		$cart->save();
		return $cart->cart_id;
	}
	public function existCartGoods ($goods,$uid)
	{
		$existCartGoods = Cart::where('goods_id', $goods->goods_id)->where('uid',$uid)->first();
		return $existCartGoods;
	}
	public function updateCartGoodsNumber ($cart_id,$goods_number,$uid )
	{
		return Cart::where('cart_id', $cart_id)->where('uid',$uid)->update(['goods_number' => $goods_number]);		 
	}
	public function updateGoodsNumber ($goods_id,$goods_number,$uid)
	{
		return Goods::where('goods_id', $goods_id)->where('uid',$uid)->update(['goods_number' => $goods_number]);
	}
	public function getCarts($uid)
	{
		return Cart::where('uid',$uid)->orderBy('shop_id','desc')->orderBy('cart_id','desc')->get();
	}
	public function getShopCarts($shop_id,$uid)
	{
		return Cart::where('shop_id',$shop_id)->where('uid',$uid)->orderBy('cart_id','desc')->get();
	}
	public function getShop ($uid)
	{
		return Cart::select(DB::raw('shop_id,cart_id'))->where('uid',$uid)->orderBy('cart_id','desc')->groupBy('shop_id')->get();
	}
	public function getCount ($where)
	{
		return Cart::where($where)->count();
	}
	public function getGoodsNumber ($where)
	{
		$data = Cart::select(DB::raw('SUM(goods_number) as goods_number'))->where($where)->first();
		return $data->goods_number;
	}
	public function existCart ($where)
	{
		return Cart::where($where)->first();
	}
	public function removeCartGoods ($ids,$uid)
	{
		return Cart::whereIn('cart_id', $ids)->where('uid',$uid)->delete();
	}
	public function removeCarts ($where)
	{
		return Cart::where($where)->delete();
	}
	public function getCartGoodsByIds ($ids,$uid)
	{
		return Cart::whereIn('cart_id', $ids)->where('uid',$uid)->get();
	}
	public function getShopIds ($cart_ids,$uid)
	{
		return Cart::select(DB::raw('shop_id'))->whereIn('cart_id', $cart_ids)->where('uid',$uid)->distinct()->orderBy('cart_id','DESC')->get()->toArray();
	}
	public function getShopCartsByCartIds ($shop_id,$cart_ids,$uid)
	{
		return Cart::where('shop_id',$shop_id)->where('uid',$uid)->whereIn('cart_id',$cart_ids)->orderBy('cart_id','desc')->get();
	}
	public function getCartGoodsNumber ($goods_id,$uid)
	{
		return Cart::where('goods_id',$goods_id)->where('uid',$uid)->pluck('goods_number');
	}
}