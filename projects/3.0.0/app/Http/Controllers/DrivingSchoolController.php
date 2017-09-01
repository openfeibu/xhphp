<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Services\HelpService;
use App\Services\UserService;
use App\Services\DrivingSchoolService;

class DrivingSchoolController extends Controller
{
    protected $helpService;

    public function __construct(HelpService $helpService,
                                DrivingSchoolService $drivingSchoolService,
                                UserService $userService)
    {
	    parent::__construct();
        $this->middleware('auth', ['only' => ['apply','getApplys','getAdminEnrollRecords'] ]);
        $this->helpService = $helpService;
        $this->drivingSchoolService = $drivingSchoolService;
        $this->userService = $userService;
	}
    public function getDrivingSchools(Request $request)
    {
        $rule = [
            'page' => 'required',
        ];

        $this->helpService->validateParameter($rule);

        $driving_schools = $this->drivingSchoolService->getDrivingSchools();

        return [
            'code' => 200,
            'data' => $driving_schools,
            'detail' => '请求成功'
        ];
    }
    public function getDrivingSchool(Request $request)
    {
        $rule = [
            'ds_id' => 'required|exists:driving_school',
        ];
        $this->helpService->validateParameter($rule);

        $driving_school = $this->drivingSchoolService->getDrivingSchool($request->ds_id);

        return [
            'code' => 200,
            'data' => $driving_school,

        ];
    }
    /*报名*/
    public function enroll(Request $request)
    {
        $rule = [
            'product_id' => 'required|exists:driving_school_product,product_id',
            'name' => 'required',
            'mobile' => 'required|mobile',
            'content' => 'sometimes|between:5,150',

        ];
        $this->helpService->validateParameter($rule);
        $product = $this->drivingSchoolService->isExistsPro($request->product_id);
        $user = $this->userService->getUser();
        $product->uid = $user->uid;
        $this->drivingSchoolService->enroll($product);
        throw new \App\Exceptions\Custom\RequestSuccessException('报名成功');
    }
    public function cancel(Request $request)
    {
        $rule = [
            'enroll_id' => 'required',
            'token' => 'required'
        ];
        $this->helpService->validateParameter($rule);
        $user = $this->userService->getUser();
        $this->drivingSchoolService->cancel(['enroll_id' => $request->enroll_id,'uid' => $user->uid]);
        throw new \App\Exceptions\Custom\RequestSuccessException('取消成功');
    }
    /*报名记录*/
    public function getEnrollRecords()
    {
        $user = $this->userService->getUser();
        $records = $this->drivingSchoolService->getEnrollRecords($user->uid);
        return [
            'code' => 200,
            'data' => $records
        ];
    }
    public function getEnrollRecord(Request $request)
    {
        $rule = [
            'enroll_id' => 'required',
        ];
        $this->helpService->validateParameter($rule);

        $user = $this->userService->getUser();
        $record = $this->drivingSchoolService->isExitsEnrollRecord($user->uid,$request->enroll_id);

        return [
            'code' => 200,
            'data' => $record
        ];
    }
    public function getAdminEnrollRecords(Request $request)
    {
        $user = $this->userService->getUser();
        $driving_school = $this->drivingSchoolService->getAdminDrivingSchool($user->uid);
        $records = $this->drivingSchoolService->getAdminEnrollRecords($driving_school->ds_id);
        return [
            'code' => 200,
            'data' => $records
        ];
    }
}
