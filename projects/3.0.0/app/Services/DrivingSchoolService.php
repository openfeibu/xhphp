<?php

namespace App\Services;

use Session;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Repositories\DrivingSchoolReposity;

class DrivingSchoolService
{
    protected $drivingSchoolReposity;

	public function __construct(Request $request,
                                UserRepository $userRepository,
                                DrivingSchoolReposity $drivingSchoolReposity)
	{
        $this->request = $request;
        $this->userRepository = $userRepository;
        $this->drivingSchoolReposity = $drivingSchoolReposity;
	}
    public function getDrivingSchools()
    {
        $driving_schools = $this->drivingSchoolReposity->getDrivingSchools();
        foreach($driving_schools as $key => $driving_school)
        {
            $driving_school->min_price = $this->drivingSchoolReposity->getMinPrice($driving_school->ds_id);
        }
        return $driving_schools;
    }
    public function getDrivingSchool($ds_id)
    {
        $driving_school = $this->drivingSchoolReposity->getDrivingSchool(['ds_id' => $ds_id]);
        $driving_school->prodoucts = $this->drivingSchoolReposity->getPros($ds_id);
        $driving_school->enroll_id  = 0;
        if($this->request->token)
        {
            $user = $this->userRepository->getUserByToken($this->request->token);
            if($user)
            {
                $record = $this->drivingSchoolReposity->getEnrollRecord(['driving_school_enrollment.ds_id' => $ds_id,'driving_school_enrollment.uid' => $user->uid],$columns = ['driving_school_enrollment.enroll_id']);
                $driving_school->enroll_id  = $record ? $record->enroll_id : 0;
            }

        }
        return $driving_school;
    }
    public function isExistsDrivingSchool($ds_id)
    {
        $driving_school = $this->getDrivingSchool($ds_id);
        if(!$driving_school)
        {
            throw new \App\Exceptions\Custom\FoundNothingException('驾校不存在');
        }
        return $driving_school;
    }
    public function isExistsPro($product_id)
    {
        $product = $this->getPro($product_id);
        if(!$product)
        {
            throw new \App\Exceptions\Custom\FoundNothingException('产品不存在');
        }
        return $product;
    }
    public function getPro($product_id)
    {
        $product = $this->drivingSchoolReposity->getPro($product_id);
        return $product;
    }
    public function enroll($product)
    {
        $record = $this->drivingSchoolReposity->getEnrollRecord(['driving_school_enrollment.uid' => $product->uid,'driving_school_enrollment.ds_id' => $product->ds_id ]);
        if($record)
        {
            throw new \App\Exceptions\Custom\OutputServerMessageException('请勿重复报名！');
        }
        $enrollment = [
            'name' => $this->request->name,
            'mobile' => $this->request->mobile,
            'content' => $this->request->content,
            'uid' => $product->uid,
            'ds_id' => $product->ds_id,
            'product_id' => $product->product_id,
        ];
        return $this->drivingSchoolReposity->enroll($enrollment);
    }
    public function cancel($where = [])
    {
        return $this->drivingSchoolReposity->cancel($where);
    }
    public function getEnrollRecords($uid)
    {
        $records = $this->drivingSchoolReposity->getEnrollRecords($uid);
        return $records;
    }
    public function isExitsEnrollRecord($uid,$enroll_id)
    {
        $record = $this->getEnrollRecord($uid,$enroll_id);
        if(!$record)
        {
            throw new \App\Exceptions\Custom\FoundNothingException();
        }
        return $record;
    }
    public function getEnrollRecord($uid,$enroll_id)
    {
        $record = $this->drivingSchoolReposity->getEnrollRecord(['driving_school_enrollment.enroll_id' => $enroll_id,'driving_school_enrollment.uid' => $uid]);
        return $record;
    }
    public function getAdminDrivingSchool($uid)
    {
        $driving_school = $this->drivingSchoolReposity->getDrivingSchool(['uid' => $uid]);
        if(!$driving_school)
        {
            throw new \App\Exceptions\Custom\OutputServerMessageException('没有权限');
        }
        return $driving_school;
    }
    public function getAdminEnrollRecords($ds_id)
    {
        $records = $this->drivingSchoolReposity->getAdminEnrollRecords($ds_id);
        return $records;
    }
    public function getAdminEnrollRecord($ds_id,$enroll_id)
    {
        $record = $this->drivingSchoolReposity->getEnrollRecord(['driving_school_enrollment.enroll_id' => $enroll_id,'driving_school_enrollment.ds_id' => $ds_id]);
        return $record;
    }
}
