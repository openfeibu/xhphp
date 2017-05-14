<?php

namespace App\Http\Controllers;

use DB;
use App\Cart;
use App\OrderGoods;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Services\CouponService;
use App\Services\HelpService;

class CouponController extends Controller
{
	protected $helpService;

	protected $userService;

	protected $user;

	public function __construct (Request $request,
                                UserService $userService,
                                CouponService $couponService,
								HelpService $helpService)
	{
		parent::__construct();
		$this->middleware('auth');
		$this->userService = $userService;
	 	$this->couponService = $couponService;
        $this->helpService = $helpService;

	}
    /*
    获取用户优惠券
    */
    public function getUserCoupons(Request $request)
    {
        $rule = [
            'page' => 'required|integer',
            'type' => 'required|in:,unused,used,overdue',
        ];
        $this->helpService->validateParameter($rule);
        $user = $this->userService->getUser();
        $coupons = $this->couponService->getUserCoupons(['user_coupon.uid' => $user->uid],$request->type);
        return [
            'code' => 200,
            'detail' => '请求成功',
            'data' => $coupons,
        ];
    }
    public function getOrderInfoCoupons(Request $request)
	{

	}
}
