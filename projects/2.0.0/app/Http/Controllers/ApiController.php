<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Log;
use Input;
use App\Paper;
use App\ApiVersion;
use App\DataVersion;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\HelpService;


class ApiController extends Controller
{

    protected $helpService;
	
    public function __construct(HelpService $helpService)
    {
	    parent::__construct();	    
        $this->helpService = $helpService;
	}
	public function getApi (Request $request)
	{
		//检验请求参数
        $rule = [
            'api_name' => 'required',
        ];
        $this->helpService->validateParameter($rule);
        $api = ApiVersion::where('api_name',$request->api_name)->first();
        return [
			'code' => 200,
			'data' => $api
        ];
	}
	public function getCommonData (Request $request)
	{
		$rule = [
            'data_version' => 'required',
        ];
        $this->helpService->validateParameter($rule);
        $data_version = DataVersion::first();
        if($data_version->data_version == $request->data_version){
	        return [
				'code' => 401,
	        ];
        }
        $shop = Paper::where('id',8)->first();
        return [
			'data_version' => $data_version->data_version,
			'request_cycle' => $data_version->request_cycle,
			'data_list' => [
				'main_url' => $data_version->main_url,
				"shop" => $shop->type.'|'.$data_version->main_url.$shop->url,
			],
        ];
	}
}
