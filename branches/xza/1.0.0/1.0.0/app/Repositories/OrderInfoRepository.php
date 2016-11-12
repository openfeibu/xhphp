<?php

namespace App\Repositories;

use DB;
use Session;
use App\Shop;
use App\Goods;
use App\OrderInfo;
use Illuminate\Http\Request;

class OrderInfoRepository
{
	protected $request;

	public function __construct(Request $request)
	{
		$this->request = $request;
	}
}