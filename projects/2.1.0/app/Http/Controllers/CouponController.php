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
use App\Services\GameService;

class CouponController extends Controller
{
	protected $helpService;

	protected $userService;

	protected $user;

	public function __construct (Request $request,
                                UserService $userService,
								GameService $gameService,
                                CouponService $couponService,
								HelpService $helpService)
	{
		parent::__construct();
		$this->middleware('auth');
		$this->userService = $userService;
	 	$this->couponService = $couponService;
        $this->helpService = $helpService;
		$this->gameService = $gameService;

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
	public function getCouponPrizes(Request $request)
	{
		$user = $this->userService->getUser();
		$game = $this->gameService->checkGame(['name' => 'coupon']);
		$prizes = $this->gameService->getCouponPrizes();

		$game_user_count = $this->gameService->getGameUserCount(['uid' => $user->uid,'game_id' => $game->id]);
		$num  =  $game_user_count ? 0 :1;

		return [
			'code' => 200,
			'data' => $prizes,
			'num'  => $num,
		];
	}
	public function getUserPrizes(Request $request)
	{
		$user = $this->userService->getUser();
		$prizes = $this->gameService->getUserPrizes(['game_id' => 2,'uid' => $user->uid]);
		return [
			'code' => 200,
			'data' => $prizes,
		];
	}
	/*
 	优惠券抽奖
	*/
	public function couponLottery(Request $request)
	{
		$user = $this->userService->getUser();
		$game = $this->gameService->checkGame(['name' => 'coupon']);
		$game_user_count = $this->gameService->getGameUserCount(['uid' => $user->uid,'game_id' => $game->id]);
		if(isset($game_user_count) && $game_user_count->count >= 1)
		{
			throw new \App\Exceptions\Custom\OutputServerMessageException('已参加过活动');
		}
		$prizes = $this->gameService->getCouponPrizes();

		foreach ($prizes as $key => $val) {
		    $arr[$val['prize_id']] = $val->prize_value;
		}
		$rid = get_rand($arr); //根据概率获取奖项id
		$prize = $this->gameService->getCouponPrize(['prize_id' => $rid]);

		$this->couponService->createUserCoupon([
			'uid' => $user->uid,
			'overdue' => date("Y-m-d H:i:s",strtotime("+1week",time())) ,
	        'receive' => dtime(),
			'status' => 'unused',
	        'min_price' => $prize->min_price,
	        'price' => $prize->price,
		]);
		$this->gameService->createUserPrize([
			'uid' => $user->uid,
			'prize_name' => $prize->price_desc,
			'game_id' => 2
		]);
		if(!$game_user_count){
			$this->gameService->createGameUserCount([
				'uid' => $user->uid,
				'game_id' => $game->id,
		        'num' => 1,
		        'count' => 1,
		        'lasttime' => dtime(),
		        'share_num' => 0,
			]);
		}
		return [
			'code' => 200,
			'data' => $prize
		];
	}

}
