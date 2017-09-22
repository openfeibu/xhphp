<?php

namespace App\Http\Controllers\TelecomAdmin;

use Illuminate\Http\Request;

use DB;
use Auth;
use Route;
use Redirect;
use App\User;
use App\Http\Requests;
use App\Http\Controllers\CommonController;
use Clients\StatisticClient;

class Controller extends CommonController
{
   public function __construct ()
   {
	   	$this->middleware('telecom:telecom');
   		$this->user = Auth::guard('telecom')->user();
        if(!$this->user->is_admin)
        {
            Auth::guard('telecom')->logout();
            return Redirect::to('telecomAdmin/login')->withErrors(['mobile_no' => '没有权限']);
        }

   }
}
