<?php

namespace App\Http\Controllers\TelecomAdmin;

use Illuminate\Http\Request;
use Session;
use Cache;
use Excel;
use App\Http\Requests;
use App\Services\HelpService;
use App\Services\UserService;
use App\Services\TelecomService;
use App\Http\Controllers\TelecomAdmin\Controller;

class TelecomController extends Controller
{
	protected $userService;

	protected $helpService;

	protected $telecomService;

	public function __construct (UserService $userService,
								 TelecomService $telecomService,
								 HelpService $helpService)
	{
        parent::__construct();
		$this->helpService = $helpService;
		$this->userService = $userService;
		$this->telecomService = $telecomService;
    }
    public function index()
    {
        return view('telecom.index');
    }
    public function getEnrolls(Request $request)
    {
        $rules = [
			'page' => 'required|integer',
            'campus_id' => 'sometimes',
            'building_id' => 'sometimes',
            'date' => 'sometimes',
			'keyword' => 'sometimes'
	    ];
	    $this->helpService->validateParameter($rules);
        $where = [];
        if(isset($request->date) && !empty($request->date)){
            $where['telecom_enrollment.date'] =  $request->date;
        }
        if(isset($request->campus_id) && $request->campus_id){
            $where['telecom_enrollment.campus_id'] =  $request->campus_id;
        }
        if(isset($request->building_id) && $request->building_id){
            $where['telecom_enrollment.building_id'] =  $request->building_id;
        }
		if(isset($request->keyword) && !empty($request->keyword)){
			if(preg_match("/^1[34578]\d{9}$/", $request->keyword)){
				$where[] =  ['user.mobile_no' ,'like','%'.$request->keyword.'%'];
			}else{
				$where[] =  ['telecom_enrollment.name' ,'like','%'.$request->keyword.'%'];
			}
        }
        $enrolls = $this->telecomService->getEnrolls($where);

        $enrollment_count = $this->telecomService->get_enrollment_count($where);

        return [
            'code' => '200',
            'data' => $enrolls,
            'count' => $enrollment_count
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
    public function explodeEnrolls(Request $request)
    {
        $rules = [
            'campus_id' => 'sometimes',
            'building_id' => 'sometimes',
            'date' => 'sometimes',
	    ];
	    $this->helpService->validateParameter($rules);
        $where = [];
        if(isset($request->date) && !empty($request->date)){
            $where['telecom_enrollment.date'] =  $request->date;
        }
        if(isset($request->campus_id) && $request->campus_id){
            $where['telecom_enrollment.campus_id'] =  $request->campus_id;
        }
        if(isset($request->building_id) && $request->building_id){
            $where['telecom_enrollment.building_id'] =  $request->building_id;
        }
        $enrolls = $this->telecomService->getAllEnrolls($where);
        $name = '预约名单';
        Excel::create($name,function($excel) use ($enrolls){
		  $excel->sheet('score', function($sheet) use ($enrolls){
			$sheet->fromArray($enrolls);
		  });
		})->export('xls');


    }
}
