<?php

namespace App\Http\Controllers\Telecom;

use Illuminate\Http\Request;
use Session;
use Cache;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\HelpService;
use App\Services\UserService;
use App\Services\TelecomService;

class TelecomController extends Controller
{
	protected $userService;

	protected $helpService;

	protected $telecomService;

	public function __construct (UserService $userService,
								 TelecomService $telecomService,
								 HelpService $helpService)
	{
		$this->helpService = $helpService;
		$this->userService = $userService;
		$this->telecomService = $telecomService;
    }
    public function getEnrolls(Request $request)
    {
        $rules = [
			'page' => 'required|integer',
            'campus_id' => 'sometimes',
            'building_id' => 'sometimes',
            'date' => 'sometimes',
	    ];
	    $this->helpService->validateParameter($rules);
        $where = [];
        if(isset($request->date) && !empty($request->date)){
            $where['date'] =  $request->date;
        }
        if(isset($request->campus_id) && !empty($request->campus_id)){
            $where['campus_id'] =  $request->campus_id;
        }
        if(isset($request->building_id) && !empty($request->building_id)){
            $where['building_id'] =  $request->building_id;
        }
        $enrolls = $this->telecomService->getEnrolls($where);
        return [
            'code' => '200',
            'data' => $enrolls
        ];
    }
    public function getEnrollSettings(Request $request)
    {
        $setting = $this->telecomService->getEnrollSettings();
        return [
            'code' => 200,
            'data' => $setting,
        ];
    }
    public function updateEnrollSetting(Request $request)
    {
        $rules = [
			'setting_id' => 'required|integer',
            'count' => 'required|integer',
	    ];
	    $this->helpService->validateParameter($rules);
        $this->telecomService->updateEnrollSetting(['setting_id' => $request->setting_id],['count' => $request->count]);
        throw new \App\Exceptions\Custom\RequestSuccessException('更新成功');
    }
}
