<?php

namespace App\Services;

use Log;
use Illuminate\Http\Request;
use App\Services\HelpService;
use App\Repositories\UserRepository;
use App\Repositories\CouponRepository;

class CouponService
{
	protected $request;

    protected $orderInfoRepository;

    protected $userRepository;

	public function __construct(Request $request,
						 HelpService $helpService,
						 CouponRepository $couponRepository,
						 UserRepository $userRepository)
	{
		$this->request = $request;
		$this->helpService = $helpService;
        $this->couponRepository = $couponRepository;
        $this->userRepository = $userRepository;
	}
    public function getUserCoupons($where,$type,$num =20)
    {
        return $this->couponRepository->getUserCoupons($where,$type,$num);
    }
	public function getOrderInfoCoupons($where,$min_price)
	{
		return $this->couponRepository->getOrderInfoCoupons($where,$min_price);
	}
}
