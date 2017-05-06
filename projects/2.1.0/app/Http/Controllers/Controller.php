<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Route;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Clients\StatisticClient;

class Controller extends CommonController
{
   public function __construct ()
   {
   		$currentRouteName = Route::currentRouteName();
		if($currentRouteName){
			$currentRouteNameHandle = explode('_',$currentRouteName);
			if(is_array($currentRouteNameHandle)){
				$module = $currentRouteNameHandle[0];
				$interface = $currentRouteNameHandle[1] ? $currentRouteNameHandle[1] : 0;
				$module_name = config('statistics.'.$module.'.name');
				$interface_name = config('statistics.'.$module.'.interface.'.$interface);
				$success = true; $code = 200; $msg = '';
				StatisticClient::report($module_name, $interface_name, $success, $code, $msg,'udp://127.0.0.1:55656');
			}
		}
   }
}