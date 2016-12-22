<?php

namespace App\Services;

use Log;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Repositories\ShopRepository;
use App\Repositories\CartRepository;

class ShopService
{
	protected $request;

    protected $shopRepository;

    protected $userRepository;

    protected $cartRepository;

	function __construct(Request $request,
						 CartRepository $cartRepository,
						 ShopRepository $shopRepository,
						 UserRepository $userRepository)
	{
		$this->request = $request;
		$this->cartRepository = $cartRepository;
        $this->shopRepository = $shopRepository;
        $this->userRepository = $userRepository;
	}
	public function addShop ($user)
	{
		$this->shopRepository->addShop($user);
	}
	public function getShops($uid = 0)
	{
		$shops = $this->shopRepository->getShops();
		if($uid){
			foreach( $shops as $key => $shop )
			{
				$shop->cart_count = $this->cartRepository->getCount(['uid' => $uid,'shop_id' => $shop->shop_id ]);
			}
		}
		return $shops;
	}
	public function isExistsShop ($where,$columns = ['*'])
	{
		$shop = $this->shopRepository->getShop($where,$columns);
		if(!$shop){
			throw new \App\Exceptions\Custom\OutputServerMessageException('店铺不存在');
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
		return $this->shopRepository->userCollects($uid);
	}
}