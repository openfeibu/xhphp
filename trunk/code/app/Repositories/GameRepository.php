<?php

namespace App\Repositories;

use DB;
use Session;
use App\Shop;
use App\Game;
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
}