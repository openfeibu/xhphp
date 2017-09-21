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
	  // 	$this->middleware('business:business');
   	//	$this->user = Auth::guard('business')->user();
   }
}
