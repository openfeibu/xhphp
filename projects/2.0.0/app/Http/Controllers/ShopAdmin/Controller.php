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
	   	$this->shopService = $shopService ;
	   	$this->user = User::where('uid', 85)->first(['uid','wallet','mobile_no','nickname','avatar_url','created_at']);
   		//$this->user = Auth::guard('business')->user();
   		$this->shop = $this->shopService->isExistsShop(['uid' => $this->user->uid]);  
   }
}