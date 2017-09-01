<?php

namespace App\Services;

use Session;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Repositories\EducationReposity;

class EducationService
{
    protected $educationReposity;

	public function __construct(Request $request,
                                UserRepository $userRepository,
                                EducationReposity $educationReposity)
	{
        $this->request = $request;
        $this->userRepository = $userRepository;
        $this->educationReposity = $educationReposity;
	}
    public function getEducations()
    {
        $educations = $this->educationReposity->getEducations();
        foreach($educations as $key => $education)
        {
            $education->min_price = $this->educationReposity->getMinPrice($education->edu_id);
        }
        return $educations;
    }
    public function getEducation($edu_id)
    {
        $education = $this->educationReposity->getEducation(['edu_id' => $edu_id]);
        $education->prodoucts = $this->educationReposity->getPros($edu_id);
        $education->enroll_id  = 0;
        if($this->request->token)
        {
            $user = $this->userRepository->getUserByToken($this->request->token);
            if($user)
            {
                $record = $this->educationReposity->getEnrollRecord(['education_enrollment.edu_id' => $edu_id,'education_enrollment.uid' => $user->uid],$columns = ['education_enrollment.enroll_id']);
                $education->enroll_id  = $record ? $record->enroll_id : 0;
            }

        }
        return $education;
    }
    public function isExistsEducation($edu_id)
    {
        $education = $this->getEducation($edu_id);
        if(!$education)
        {
            throw new \App\Exceptions\Custom\FoundNothingException();
        }
        return $education;
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
        $product = $this->educationReposity->getPro($product_id);
        return $product;
    }
    public function enroll($product)
    {
        $record = $this->educationReposity->getEnrollRecord(['education_enrollment.uid' => $product->uid,'education_enrollment.edu_id' => $product->edu_id ]);
        if($record)
        {
            throw new \App\Exceptions\Custom\OutputServerMessageException('请勿重复报名！');
        }
        $enrollment = [
            'name' => $this->request->name,
            'mobile' => $this->request->mobile,
            'content' => $this->request->content,
            'uid' => $product->uid,
            'edu_id' => $product->edu_id,
            'product_id' => $product->product_id,
        ];
        return $this->educationReposity->enroll($enrollment);
    }
    public function cancel($where = [])
    {
        return $this->educationReposity->cancel($where);
    }
    public function getEnrollRecords($uid)
    {
        $records = $this->educationReposity->getEnrollRecords($uid);
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
        $record = $this->educationReposity->getEnrollRecord(['education_enrollment.enroll_id' => $enroll_id,'education_enrollment.uid' => $uid]);
        return $record;
    }
    public function getAdminEducation($uid)
    {
        $education = $this->educationReposity->getEducation(['uid' => $uid]);
        if(!$education)
        {
            throw new \App\Exceptions\Custom\OutputServerMessageException('没有权限');
        }
        return $education;
    }
    public function getAdminEnrollRecords($edu_id)
    {
        $records = $this->educationReposity->getAdminEnrollRecords($edu_id);
        return $records;
    }
    public function getAdminEnrollRecord($edu_id,$enroll_id)
    {
        $record = $this->educationReposity->getEnrollRecord(['education_enrollment.enroll_id' => $enroll_id,'education_enrollment.edu_id' => $edu_id]);
        return $record;
    }
}
