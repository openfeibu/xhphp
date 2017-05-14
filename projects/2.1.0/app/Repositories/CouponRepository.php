<?php

namespace App\Repositories;

use DB;
use Session;
use App\Coupon;
use App\UserCoupon;
use Illuminate\Http\Request;

class CouponRepository
{
	protected $request;

	public function __construct(Request $request)
	{
		$this->request = $request;

    }
    public function getUserCoupons($where = [],$type,$num)
    {
        $coupons = UserCoupon::select(DB::raw('coupon.*,user_coupon.*'))
                                ->Join('coupon', 'coupon.coupon_id', '=', 'user_coupon.coupon_id')
                                ->where($where);
        switch ($type) {
            case 'overdue':
                $coupons = $coupons->where('user_coupon.status','unused')->where('overdue','<',date('Y-m-d'));
                break;
            case 'used':
                $coupons = $coupons->where('user_coupon.status','used');
                break;
            default:
                $coupons = $coupons->where('user_coupon.status','unused')->where('overdue','>',date('Y-m-d'));
                break;
        }
        $coupons = $coupons->OrderBy('user_coupon.user_coupon_id','desc')
                            ->skip($num * $this->request->page - $num)
                            ->take($num)
                            ->get();
        return $coupons;
    }
	public function getOrderInfoCoupons($where,$min_price)
	{
		$coupons = UserCoupon::select(DB::raw('coupon.*,user_coupon.*'))
                                ->Join('coupon', 'coupon.coupon_id', '=', 'user_coupon.coupon_id')
                                ->where($where)
								->where('coupon.min_price','<=', $min_price)
								->where('user_coupon.status','unused')
								->where('overdue','>',date('Y-m-d'))
								->OrderBy('user_coupon.user_coupon_id','desc')
								->get();

        return $coupons;
	}
}
