<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Services\HelpService;
use App\Services\UserService;
use App\Services\EducationService;

class EducationController extends Controller
{
    protected $helpService;

    public function __construct(HelpService $helpService,
                                EducationService $educationService,
                                UserService $userService)
    {
	    parent::__construct();
        $this->middleware('auth', ['only' => ['apply','getApplys','getAdminEnrollRecords'] ]);
        $this->helpService = $helpService;
        $this->educationService = $educationService;
        $this->userService = $userService;
	}
    public function getEducations(Request $request)
    {
        $rule = [
            'page' => 'required',
        ];

        $this->helpService->validateParameter($rule);

        $educations = $this->educationService->getEducations();

        return [
            'code' => 200,
            'data' => $educations,
            'detail' => '请求成功'
        ];
    }
    public function getEducation(Request $request)
    {
        $rule = [
            'edu_id' => 'required|exists:education',
        ];
        $this->helpService->validateParameter($rule);

        $education = $this->educationService->getEducation($request->edu_id);

        return [
            'code' => 200,
            'data' => $education,

        ];
    }
    /*报名*/
    public function enroll(Request $request)
    {
        $rule = [
            'product_id' => 'required|exists:education_product,product_id',
            'name' => 'required',
            'mobile' => 'required|mobile',
            'content' => 'sometimes|between:5,150',

        ];
        $this->helpService->validateParameter($rule);
        $product = $this->educationService->isExistsPro($request->product_id);
        $user = $this->userService->getUser();
        $product->uid = $user->uid;
        $this->educationService->enroll($product);
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
        $this->educationService->cancel(['enroll_id' => $request->enroll_id,'uid' => $user->uid]);
        throw new \App\Exceptions\Custom\RequestSuccessException('取消成功');
    }
    /*报名记录*/
    public function getEnrollRecords()
    {
        $user = $this->userService->getUser();
        $records = $this->educationService->getEnrollRecords($user->uid);
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
        $record = $this->educationService->isExitsEnrollRecord($user->uid,$request->enroll_id);

        return [
            'code' => 200,
            'data' => $record
        ];
    }
    public function getAdminEnrollRecords(Request $request)
    {
        $user = $this->userService->getUser();
        $education = $this->educationService->getAdminEducation($user->uid);
        $records = $this->educationService->getAdminEnrollRecords($education->edu_id);
        return [
            'code' => 200,
            'data' => $records
        ];
    }
}
