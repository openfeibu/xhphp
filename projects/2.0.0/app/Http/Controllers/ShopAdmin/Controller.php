<?php

namespace App\Http\Controllers\ShopAdmin;

use Illuminate\Http\Request;

use Auth;
use Route;
use App\User;
use App\Http\Requests;
use App\Services\ShopService;
use App\Http\Controllers\CommonController;
use Clients\StatisticClient;

class Controller extends CommonController
{
   public function __construct (ShopService $shopService)
   {
	   	$this->middleware('business:business');
	   	$this->shopService = $shopService ;
   		$this->user = Auth::guard('business')->user();
   		$this->shop = $this->shopService->isExistsShop(['uid' => $this->user->uid]);
   }
}
