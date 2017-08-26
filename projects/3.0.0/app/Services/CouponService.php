<?php

namespace App\Services;

use Log;
use Illuminate\Http\Request;
use App\Services\HelpService;
use App\Repositories\UserRepository;
use App\Repositories\CouponRepository;
use App\Repositories\GameRepository;

class CouponService
{
	protected $request;

    protected $orderInfoRepository;

    protected $userRepository;

	public function __construct(Request $request,
						 HelpService $helpService,
						 CouponRepository $couponRepository,
						 GameRepository $gameRepository,
						 UserRepository $userRepository)
	{
		$this->request = $request;
		$this->helpService = $helpService;
        $this->couponRepository = $couponRepository;
        $this->userRepository = $userRepository;
		$this->gameRepository = $gameRepository;
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
	public function createUserCoupon($data)
	{
		return $this->couponRepository->createUserCoupon($data);
	}
	public function updateUserCoupon($where,$data)
	{
		return $this->couponRepository->updateUserCoupon($where,$data);
	}
	public function createUserRegisterCoupon($uid)
	{
		$game = $this->gameRepository->getGame(['name' => 'coupon']);
		$time = time();
		if($game->status ==1 && $time >= strtotime($game->starttime) && $time <= strtotime($game->endtime))
		{
			$this->couponRepository->createUserCoupon([
			   'uid' => $uid,
			   'overdue' => date("Y-m-d H:i:s",strtotime("+1week",time())) ,
			   'receive' => dtime(),
			   'status' => 'unused',
			   'min_price' => '10',
			   'price' => '5',
		   ]);
		}
	}
	public function getCount($where = [])
	{
		return $this->couponRepository->getCount($where);
	}
}
