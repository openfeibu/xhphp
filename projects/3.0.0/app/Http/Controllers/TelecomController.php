<?php

namespace App\Http\Controllers;

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

		parent::__construct();
		$this->middleware('auth',['except' => ['getTelecomEnrollmentTimes','getSchoolCampusBuildings']]);
		$this->helpService = $helpService;
		$this->userService = $userService;
		$this->telecomService = $telecomService;

	}
    public function queryRealName (Request $request)
    {

	    $user = $this->userService->getUser();

	    $ordersCount = $this->telecomService->getUserTelecomOrdersCount($user->uid);

		if($ordersCount>=3){
			throw new \App\Exceptions\Custom\OutputServerMessageException("抱歉，一个用户最多只能办理三次套餐");
		}

	    $rules = [
			'phone' => 'required|regex:/^1[34578][0-9]{9}$/',
			'iccid' => 'required|digits:6',
			'outOrderNumber' => 'required|regex:/^1[34578][0-9]{9}$/'
	    ];

	    $this->helpService->validateParameter($rules);


		/*
	    $real = $this->telecomService->getRealByPhone($request->phone);

	    if($real&&$real->uid==$user->uid){
		    return [
		    	'code' => 200,
		    	'detail' => '已实名',
		    ];
	    }
	    else if($real&&$real->uid!=$user->uid){
		    return [
		    	'code' => 8403,
		    	'detail' => '该手机号码已被其他人绑定',
		    ];
	    }*/
	    $fields = array(
			'phone' => $request->phone,
			'iccid' => $request->iccid,
			'outOrderNumber' => $request->outOrderNumber,
 		);

    	$file_contents = $this->helpService->telecomCheckReal($fields);

		if($file_contents->resultCode == 'PPARAM_ERROR'){
		    throw new \App\Exceptions\Custom\OutputServerMessageException($file_contents->resultMessage);
		}

		$telecomOrder = $this->telecomService->hasTelecomOrder($request->phone);

    	if($telecomOrder){
	    	throw new \App\Exceptions\Custom\OutputServerMessageException('该手机号码已办理过套餐，不能再办理');
    	}

		throw new \App\Exceptions\Custom\RequestSuccessException();

    }
    public function  telecomPackage	(Request $request)
    {

    	$package = $this->telecomService->getPackageList();
    	return [
    		'code' => 200,
    		'data' => [
				'package' => $package,
				//'real_user' => $real_user
    		],
    	];
    }
    public function telecomPackageStore (Request $request)
    {

	    $user = $this->userService->getUser();

	    $ordersCount = $this->telecomService->getUserTelecomOrdersCount($user->uid);

		if($ordersCount>=3){
			throw new \App\Exceptions\Custom\OutputServerMessageException("抱歉，一个用户最多只能办理三次套餐");
		}

    	$rules = [
    		'telecom_phone' => 'required|string',
			'telecom_iccid' => 'required|digits:6',
			'telecom_outOrderNumber' => 'required|regex:/^1[34578][0-9]{9}$/',
    		'package_id' => 'required|exists:telecom_package,package_id',
    		'idcard' => 'required|string|size:18',
    		'name' => 'required|string',
    		'major' => 'required|string',
    		'dormitory_no' => 'required|string',
    		'student_id' => 'required|string',
    		'transactor' => 'sometimes|required|digits:3',
    	];
    	$this->helpService->validateParameter($rules);



    	$telecomOrder = $this->telecomService->hasTelecomOrder($request->telecom_phone);
    	if($telecomOrder){
	    	throw new \App\Exceptions\Custom\OutputServerMessageException('该手机号码已办理过套餐，不能再办理');
    	}
    	$telecomPackage = $this->telecomService->getTelecomPackage($request->package_id) ;
    	$telecom_trade_no = $this->helpService->buildOrderSn('TP');
    	$telecomOrderData = array(
    		'telecom_trade_no' => $telecom_trade_no,
    		'trade_no' => '',
    		'uid' => $user->uid,
    		'transactor' => $request->transactor,
			'telecom_phone' => $request->telecom_phone,
			'telecom_iccid' => $request->telecom_iccid,
			'telecom_outOrderNumber' => $request->telecom_outOrderNumber,
    		'idcard' => $request->idcard,
    		'major' => $request->major,
    		'dormitory_no' => $request->dormitory_no,
    		'student_id' => $request->student_id,
    		'name' => $request->name,
    		'fee' => $telecomPackage->package_price,
    		'package_id' => $telecomPackage->package_id,
    		'package_name' => $telecomPackage->package_name,
    	);
    	/*$alipay_config = config('alipay-telecom-wap');
		$alipay = app('alipay-telecom.wap');*/
		$alipay_config = array_merge(config('alipay-wap'),config('alipay'));
		$alipay = app('alipay.wap');
		//构造要请求的参数数组，无需改动
		$parameter = array(
			"service"       => $alipay_config['service'],
			"partner"       => $alipay_config['partner'],
			"seller_id"  	=> $alipay_config['seller'],
			"payment_type"	=> $alipay_config['payment_type'],
			"_input_charset"=> $alipay_config['input_charset'],
			'notify_url' 	=> config("app.url")."/alipay/alipayTelecomWapNotify",
			'return_url'	=> config('common.telecom_return_url'),
			"out_trade_no"	=> $telecom_trade_no,
			"subject"		=> $user->nickname." 购买套餐 ".$telecomPackage->package_name,
			"body"			=> $telecomPackage->package_detail,
			"total_fee"		=> $telecomPackage->package_price,
			//"total_fee"		=> 0.01,
			"show_url"		=> config('common.telecom_show_url'),
			"app_pay"	=> "Y",//启用此参数能唤起钱包APP支付宝
		);

    	$this->telecomService->storeTelecomOrderTemStore($telecomOrderData);
    	$html_text = $alipay->buildRequestForm($parameter,"get", "确认");
		return [
            'code' => 200,
            'data' => $html_text,
        ];
    }
    public function getTelecomOrders ()
    {
    	$user = $this->userService->getUser();
    	$telecomOrders = $this->telecomService->getTelecomOrdersByUid($user->uid);
    	return [
            'code' => 200,
            'data' => $telecomOrders,
        ];
    }
    public function hasTelecomOrder ()
    {
    	$user = $this->userService->getUser();
    	$hasTelecomOrder = $this->telecomService->hasTelecomOrderByUid($user->uid);

    	return [
			'code' => 200,
            'data' => $hasTelecomOrder ? true : false,
    	];
    }
    public function getTransactorTelecomOrders ()
    {

    	$user = $this->userService->getUser();

    	if(!$user->transactor){
	    	throw new \App\Exceptions\Custom\OutputServerMessageException('您没有该权限');
    	}

		if($user->transactor == '000' || $user->transactor == '999' || $user->transactor == '001'){
			$count_data = $this->telecomService->getTelecomOrdersCount();
			$super_transactor = true;
		}
		else{
			$telecomOrders = $this->telecomService->getTelecomOrdersByTransactor($user->transactor);
			$super_transactor = false;
		}
    	return [
            'code' => 200,
            'transactor' => $user->transactor,
            'super_transactor' => $super_transactor,
            'data' => isset($telecomOrders) ? $telecomOrders : $count_data,
        ];
    }
    public function  getTelecomOrdersCount()
    {
	    $user = $this->userService->getUser();

    	if(!$user->transactor||$user->transactor!='000'){
	    	throw new \App\Exceptions\Custom\OutputServerMessageException('您没有该权限');
    	}

    	$count_data = $this->telecomService->getTelecomOrdersCount();
    }
	public function getTelecomTimes(Request $request)
	{

		$times = $this->telecomService->getTelecomEnrollmentTimes();
		return [
			'code' => 200,
			'data' => $times ? $times : [],
		];
	}
	public function enroll(Request $request)
	{
		$rules = [
			'token' => 'required',
			'name' => 'required',
			'dormitory_number' => 'required',
			'building_id' => 'required|exists:school_building,building_id'
		];
		$this->helpService->validateParameter($rules);
		$user = $this->userService->getUser();
		$enroll_data = $this->telecomService->enrollData(['telecom_enrollment.uid' => $user->uid]);
		if($enroll_data){
			throw new \App\Exceptions\Custom\OutputServerMessageException('已经预约过，请勿重复预约');
		}
		$school_building = $this->telecomService->getSchoolBuilding($request->building_id);
		$count = $this->telecomService->getTelecomEnrollmentSurplusCount(['campus_id' => $school_building->campus_id]);
		if($count <=0)
		{
			throw new \App\Exceptions\Custom\OutputServerMessageException('人数已满，请选择其他时间段');
		}
		$date = date("Y-m-d");
		$this->telecomService->enroll([
			'uid' => $user->uid,
			'date' => $date,
			'name' => $request->name,
			'campus_id' => $school_building->campus_id,
			'building_id' => $request->building_id,
			'dormitory_number' => $request->dormitory_number,
		]);
		throw new \App\Exceptions\Custom\RequestSuccessException('报名成功');
	}
	public function getEnroll(Request $request)
	{
		$user = $this->userService->getUser();
		$enroll_data = $this->telecomService->enrollData(['telecom_enrollment.uid' => $user->uid]);
		return [
			'code' => 200,
			'data' => $enroll_data ? $enroll_data : []
		];
	}
	public function getTelecomEnrollmentSurplusCount(Request $request)
	{
		$rules = [
			'campus_id' => 'required',
		];
		$this->helpService->validateParameter($rules);
		$count = $this->telecomService->getTelecomEnrollmentSurplusCount(['campus_id' => $request->campus_id]);
		return [
			'code' => 200,
			'count' => $count,
		];
	}
	public function getSchoolBuildings(Request $request)
	{
		$buildings = $this->telecomService->getSchoolBuildings();
		return [
			'code' => '200',
			'data' => $buildings,
		];
	}
	public function getSchoolCampusBuildings(Request $request)
	{
		$campus_buildings = $this->telecomService->getSchoolCampusBuildings();
		return [
			'code' => '200',
			'data' => $campus_buildings,
		];
	}
}
