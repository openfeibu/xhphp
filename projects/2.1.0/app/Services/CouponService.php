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
        $coupons = $this->couponRepository->getUserCoupons($where,$type,$num);
		foreach ($coupons as $key => $coupon) {
			if(strtotime($coupon->overdue) < strtotime(date('Y-m-d')) && $coupon->status == 'unused')
			{
				$coupon->status = 'overdue'	;
			}
			$coupon->status_desc = trans('coupon.status.'.$coupon->status);
		}
		return $coupons;
    }
	public function getOrderInfoCoupons($where,$min_price)
	{
		return $this->couponRepository->getOrderInfoCoupons($where,$min_price);
	}
	public function getOrderInfoCoupon($where,$min_price)
	{
		return $this->couponRepository->getOrderInfoCoupon($where,$min_price);
	}
}
