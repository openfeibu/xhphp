<?php

namespace App\Services;

use Log;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Repositories\ShopRepository;
use App\Repositories\CartRepository;
use App\Repositories\GoodsRepository;

class ShopService
{
	protected $request;

    protected $shopRepository;

    protected $userRepository;

    protected $cartRepository;

	function __construct(Request $request,
						 GoodsRepository $goodsRepository,
						 CartRepository $cartRepository,
						 ShopRepository $shopRepository,
						 UserRepository $userRepository)
	{
		$this->request = $request;
		$this->cartRepository = $cartRepository;
        $this->shopRepository = $shopRepository;
        $this->userRepository = $userRepository;
        $this->goodsRepository = $goodsRepository;
	}
	public function addShop ($user)
	{
		$this->shopRepository->addShop($user);
	}
	public function update ($where = [],$update = [])
	{
		$this->shopRepository->update($where,$update);
	}
	public function getShops($uid = 0)
	{
		$shops = $this->shopRepository->getShops();
		foreach( $shops as $key => $shop )
		{
			$time = strtotime(date('H:i:s',time()));
			if($shop->shop_status == 1 && ($time < strtotime($shop->open_time) || $time > strtotime($shop->close_time))){
				$shop->shop_status = 3;
			}
			$shop->url = config('app.web_url').'/shop/shop-detail.html?device=android&sid='.$shop->shop_id;
		}
		if($uid){
			foreach( $shops as $key => $shop )
			{
				$shop->cart_count = $this->cartRepository->getCount(['uid' => $uid,'shop_id' => $shop->shop_id ]);
				$shop->goods_number = $this->cartRepository->getGoodsNumber(['uid' => $uid,'shop_id' => $shop->shop_id ]);
			}
		}
		return $shops;
	}
	public function isExistsShop ($where,$columns = ['*'])
	{
		$shop = $this->shopRepository->getShop($where,$columns);
		if(!$shop){
			if(isset($where['uid'])){
				throw new \App\Exceptions\Custom\OutputServerMessageException('非商家没有权限');
			}
			throw new \App\Exceptions\Custom\OutputServerMessageException('店铺不存在');
		}
		if(isset($where['shop_id']))
	    {
		    $user = $this->userRepository->getUserByToken($this->request->token);
			$shop->is_collect = 0;
			if ($user) {
				$shop->is_collect =  $this->isCollect($where['shop_id'],$user->uid);
			}
	    }
	    if($shop){
		    $shop->goods_count = $this->goodsRepository->getCount(['shop_id' =>$shop->shop_id,'is_on_sale' => 1]);
	    }
		return $shop;
	}
	public function getShop ($where,$columns = ['*'])
	{
		$shop = $this->shopRepository->getShop($where,$columns);

	    if(isset($where['shop_id']))
	    {
		    $user = $this->userRepository->getUserByToken($this->request->token);
			$shop->is_collect = 0;
			if ($user) {
				$shop->is_collect =  $this->isCollect($where['shop_id'],$user->uid);
			}
	    }
	    if($shop){
		    $shop->goods_count = $this->goodsRepository->getCount(['shop_id' =>$shop->shop_id,'is_on_sale' => 1]);
	    }
	    return $shop;
	}
	public function collect ($shop_id,$uid)
	{
		//检验是否已收藏
		$is_collect = $this->isCollect($shop_id,$uid);
		if ($is_collect) {
			$this->shopRepository->unCollect($shop_id,$uid);
			return -1;
		}else{
			$this->shopRepository->collect($shop_id,$uid);
			return 1;
		}

	}
	public function isCollect ($shop_id,$uid)
	{
		$is_collect = $this->shopRepository->isCollect($shop_id,$uid);
		if($is_collect){
			return 1;
		}
		else{
			return 0;
		}
	}
	public function userCollects ($uid)
	{
		$shops = $this->shopRepository->userCollects($uid);
		foreach ($shops as $key => $shop) {
			$shop->url = config('app.web_url').'/shop/shop-detail.html?device=android&sid='.$shop->shop_id;
		}
		return $shops;
	}
	public function inIncome ($where = [],$number)
	{
		return $this->shopRepository->inIncome($where,$number);
	}
}
