<?php

namespace App\Http\Controllers\ShopAdmin;

use Illuminate\Http\Request;

use DB;
use Auth;
use Route;
use App\User;
use App\WalletAccount;
use Redirect;
use App\Http\Requests;
use App\Services\ShopService;
use App\Http\Controllers\CommonController;
use Clients\StatisticClient;

class Controller extends CommonController
{
   public function __construct (ShopService $shopService)
   {
	   	//$this->middleware('business:business');
	   	$this->shopService = $shopService ;
		$this->user = User::where('uid','85')->first();
   		//$this->user = Auth::guard('business')->user();

   		$this->shop = $this->shopService->isExistsShop(['uid' => $this->user->uid]);
        $t = time();
        $beginThisToday = mktime(0,0,0,date("m",$t),date('d',$t),date('Y',$t));
        $endThisToday = mktime(23,59,59,date("m",$t),date('d',$t),date('Y',$t));
        $todayIncome = WalletAccount::select(DB::raw('SUM(fee) as fee'))
                                            ->whereBetween('created_at',[dtime($beginThisToday),dtime($endThisToday)])->where('trade_type','Shopping')->value('fee');
        $this->shop->todayIncome = $todayIncome ? $todayIncome : 0;
        if(in_array($this->shop->shop_status,[0,4]))
        {
            $error = trans('common.shop_status_validator.'.$this->shop->shop_status);
            throw new \App\Exceptions\Custom\AccessForbiddenException($error);
        }
   }
}
