<?php

namespace App\Services;

use Log;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Repositories\GoodsRepository;

class GoodsService
{
	protected $request;

    protected $goodsRepository;

    protected $userRepository;

	function __construct(Request $request,
						 GoodsRepository $goodsRepository,
						 UserRepository $userRepository)
	{
		$this->request = $request;
        $this->goodsRepository = $goodsRepository;
        $this->userRepository = $userRepository;

	}
	public function addGoods($user,$shop)
	{
		$this->goodsRepository->addGoods($user,$shop);
	}
	public function existShopGoods ($shop_id)
	{
		$shop_id = intval($shop_id);
		return $this->goodsRepository->existShopGoods($shop_id);		
	}
	public function existGoods ($goods_id)
	{	
		$goods_id = intval($goods_id);
		return $this->goodsRepository->existGoods($goods_id);
	}
	public function getShopGoodses ()
	{
		return $this->goodsRepository->getShopGoodses();		
	}
	public function getGoods ($goods_id)
	{
		$goods_id = intval($goods_id);
		return 	$this->goodsRepository->getGoods($goods_id);			
	}
	public function getGoodses ($page)
	{
		return $this->goodsRepository->getGoodses($page);
	}
	
}