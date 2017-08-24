<?php

namespace App\Services;

use Session;
use Illuminate\Http\Request;
use App\Repositories\DrivingSchoolReposity;

class DrivingSchoolService
{
    protected $drivingSchoolReposity;

	public function __construct(Request $request,
                                DrivingSchoolReposity $drivingSchoolReposity)
	{
        $this->request = $request;
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
        $driving_school = $this->drivingSchoolReposity->getDrivingSchool($ds_id);
        $driving_school->prodoucts = $this->drivingSchoolReposity->getPros($ds_id);
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
        $record = $this->drivingSchoolReposity->getEnrollRecord(['driving_school_enrollment.uid' => $product->uid,'driving_school_enrollment.pro_id' => $product->product_id ]);
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
            'pro_id' => $product->product_id,
        ];
        return $this->drivingSchoolReposity->enroll($enrollment);
    }
    public function getEnrollRecords($uid)
    {
        $records = $this->drivingSchoolReposity->getEnrollRecords($uid);
        return $records;
    }
    public function getEnrollRecord($uid,$enroll_id)
    {
        $record = $this->drivingSchoolReposity->getEnrollRecord(['driving_school_enrollment.enroll_id' => $enroll_id,'driving_school_enrollment.uid' => $uid]);
        return $record;
    }
}
