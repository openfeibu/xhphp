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
        $coupons = UserCoupon::select(DB::raw('*'))->where($where);
        switch ($type) {
            case 'overdue':
                $coupons = $coupons->where('status','unused')->where('overdue','<',date('Y-m-d'));
                break;
            case 'used':
                $coupons = $coupons->where('status','used');
                break;
            default:
                $coupons = $coupons->where('status','unused')->where('overdue','>',date('Y-m-d'));
                break;
        }
        $coupons = $coupons->OrderBy('user_coupon_id','desc')
                            ->skip($num * $this->request->page - $num)
                            ->take($num)
                            ->get();
        return $coupons;
    }
	public function getUserCoupon($where = [])
    {
        $coupon = UserCoupon::select(DB::raw('*'))->where($where)->get();

        return $coupon;
    }
	public function getOrderInfoCoupons($where,$min_price)
	{
		$coupons = UserCoupon::select(DB::raw('*'))
                                ->where($where)
								->where('min_price','<=', $min_price)
								->where('status','unused')
								->where('overdue','>',date('Y-m-d'))
								->OrderBy('user_coupon_id','desc')
								->get();

        return $coupons;
	}
	public function getOrderInfoCoupon($where,$min_price)
	{
		$coupon = UserCoupon::select(DB::raw('user_coupon.*'))
								->where($where)
								->where('min_price','<=', $min_price)
								->where('status','unused')
								->where('overdue','>',date('Y-m-d'))
								->first();
	}
	public function createUserCoupon($data)
	{
		config(['database.default' => 'write']);
		$coupon = UserCoupon::create($data);
		return $coupon;
	}
	public function updateUserCoupon($where,$data)
	{
		return UserCoupon::where($where)->update($data);
	}
}
