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
						 ShopRepository $shopRepository)
	{
		$this->request = $request;
		$this->cartRepository = $cartRepository;
        $this->shopRepository = $shopRepository;
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
	public function getShop ($shop_id,$columns = ['*'])
	{
		$shop = $this->shopRepository->getShop($shop_id,$columns);
		if(!$shop){
            throw new \App\Exceptions\Custom\OutputServerMessageException('���̲�����');
	    }
	    return $shop;
	}
}