<?php

namespace App\Repositories;

use DB;
use Session;
use App\Shop;
use App\Game;
use App\GameUserCount;
use App\GameCouponPrize;
use App\GameUserPrize;
use Illuminate\Http\Request;

class GameRepository
{
	protected $request;

	public function __construct(Request $request)
	{
		$this->request = $request;
	}
	public function getGame ($where)
	{
		$game = Game::where($where)->first();
		return $game;
	}
	public function getGameUserCount($where)
	{
		return GameUserCount::where($where)->first();
	}
	public function getCouponPrizes()
	{
		return GameCouponPrize::select(DB::raw('coupon.*,game_coupon_prize.prize_id,game_coupon_prize.prize_value'))
								->Join('coupon','coupon.coupon_id','=','game_coupon_prize.coupon_id')
								->OrderBy('prize_id','asc')->get();
	}
	public function getCouponPrize($where)
	{
		return GameCouponPrize::select(DB::raw('coupon.*,game_coupon_prize.prize_id,game_coupon_prize.prize_value'))
								->Join('coupon','coupon.coupon_id','=','game_coupon_prize.coupon_id')
								->where($where)->first();
	}
	public function getUserPrizes($where)
	{
		return GameUserPrize::where($where)->get();
	}
	public function createGameUserCount($data)
	{
		config(['database.default' => 'write']);
		return GameUserCount::create($data);
	}
	public function createUserPrize($data)
	{
		return GameUserPrize::create($data);
	}
}
