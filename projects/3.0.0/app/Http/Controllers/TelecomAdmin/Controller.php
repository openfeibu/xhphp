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
   }
}
