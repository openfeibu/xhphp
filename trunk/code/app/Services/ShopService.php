<?php

namespace App\Services;

use Log;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Repositories\ShopRepository;

class ShopService
{
	protected $request;

    protected $shopRepository;

    protected $userRepository;

	function __construct(Request $request,
						 ShopRepository $shopRepository)
	{
		$this->request = $request;
        $this->shopRepository = $shopRepository;
	}
	public function addShop ($user)
	{
		$this->shopRepository->addShop($user);
	}
	public function getShops()
	{
		return $this->shopRepository->getShops();
	}
	public function getShop ($shop_id)
	{
		return $this->shopRepository->getShop($shop_id);
	}
}