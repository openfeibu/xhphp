<?php

namespace App\Services;

use Log;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Repositories\GoodsRepository;
use App\Repositories\CartRepository;

class GoodsService
{
	protected $request;

    protected $goodsRepository;

    protected $userRepository;

	function __construct(Request $request,
						 GoodsRepository $goodsRepository,
						 UserRepository $userRepository,
						 CartRepository $cartRepository)
	{
		$this->request = $request;
        $this->goodsRepository = $goodsRepository;
        $this->userRepository = $userRepository;
		$this->cartRepository = $cartRepository;
	}
	public function addGoods($user,$shop)
	{
		$this->goodsRepository->addGoods($user,$shop);
	}
	public function existShopGoods ($shop_id,$goods_name)
	{
		$shop_id = intval($shop_id);
		return $this->goodsRepository->existShopGoods($shop_id,trim($goods_name));		
	}
	public function existGoods ($goods_id)
	{	
		$goods_id = intval($goods_id);
		return $this->goodsRepository->existGoods($goods_id);
	}
	public function getShopGoodses ($where,$uid = 0)
	{
		$goodses = $this->goodsRepository->getShopGoodses($where);		
		foreach( $goodses as $key => $goods )
		{
			if($uid){
				$cart_goods_number = $this->cartRepository->getCartGoodsNumber($goods->goods_id,$uid);
				$goods->cart_goods_number = $cart_goods_number ? $cart_goods_number : 0;
			}else{
				$goods->cart_goods_number = 0;
			}
		}
		return $goodses;
	}
	public function isExistsGoods ($where,$columns = ['*'])
	{
		$goods = $this->goodsRepository->getGoods($where,$columns);	
		if(!$goods){
			throw new \App\Exceptions\Custom\OutputServerMessageException('商品不存在');
		}
		return $goods;
	}
	public function getGoods ($goods_id)
	{
		$goods_id = intval($goods_id);
		return 	$this->goodsRepository->getGoods(['goods_id' => $goods_id]);			
	}
	public function getGoodses ($page)
	{
		return $this->goodsRepository->getGoodses($page);
	}
	public function update ($where = [],$update = [])
	{
		return $this->goodsRepository->update($where,$update);
	}	
	public function getCount ($where)
	{
		return $this->goodsRepository->getCount($where);
	}
	
}