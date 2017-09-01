<?php

namespace App\Repositories;

use DB;
use Log;
use Session;
use App\User;
use App\Education;
use App\EducationPro;
use App\EducationEcrollment;
use Illuminate\Http\Request;

class EducationReposity
{
    public function __construct(Request $request)
    {
        $this->request = $request;
        if(isset($this->request->page))
        {
            $this->page = intval($this->request->page);
        }
    }
	public function getEducations()
    {
        $educations = Education::skip(20 * $this->page - 20)
                                        ->orderBy('education.edu_id','desc')
                                        ->take(20)
                                        ->get(['education.edu_id', 'education.name','education.desc','education.logo_url']);
        return $educations;
    }
    public function getEducation($where)
    {
        $education = Education::where($where)->first(['logo_url','img_url','edu_id','name','desc','content','tell']);
        return $education;
    }
    /*  获取该驾校最低的价格 */
    public function getMinPrice($edu_id)
    {
        return EducationPro::where('edu_id',$edu_id)->min('price');
    }
    public function getPro($product_id){
        $product = EducationPro::join('education as edu','edu.edu_id','=','education_product.edu_id')
                                ->where('education_product.product_id',$product_id)
                                ->first(['edu.edu_id','edu.name','education_product.product_id','education_product.name as product_name','education_product.price']);
        return $product;
    }
    public function getPros($edu_id)
    {
        $pros = EducationPro::where('edu_id',$edu_id)->get(['name as product_name','desc','price','original_price','product_id']);
        return $pros;
    }
    public function enroll($enrollment)
    {
        return EducationEcrollment::create($enrollment);
    }
    public function cancel($where)
    {
        return EducationEcrollment::where($where)->update(['status' => 'canceled']);
    }
    public function getEnrollRecords($uid)
    {
        $records = EducationEcrollment::join('education as edu','edu.edu_id','=','education_enrollment.edu_id')
                                          ->join('education_product as edup','edup.product_id','=','education_enrollment.product_id')
                                          ->where('education_enrollment.uid',$uid)
                                          ->where('status','succ')
                                          ->get(['edu.name','edup.name as product_name','edup.price','education_enrollment.enroll_id']);
        return $records;
    }
    public function getEnrollRecord($where,$columns = [])
    {
        $columns = $columns ? $columns : ['edu.name','edup.name as product_name','edup.price','edup.desc','education_enrollment.enroll_id','education_enrollment.name as enroll_name','education_enrollment.mobile','education_enrollment.content','edu.edu_id'];
        $record = EducationEcrollment::join('education as edu','edu.edu_id','=','education_enrollment.edu_id')
                                          ->join('education_product as edup','edup.product_id','=','education_enrollment.product_id')
                                          ->where($where)
                                          ->where('status','succ')
                                          ->first($columns);
        return $record;
    }
    public function getAdminEnrollRecords($edu_id)
    {
        $records = EducationEcrollment::join('education as edu','edu.edu_id','=','education_enrollment.edu_id')
                                          ->join('education_product as edup','edup.product_id','=','education_enrollment.product_id')
                                          ->join('user','user.uid','=','education_enrollment.uid')
                                          ->where('education_enrollment.edu_id',$edu_id)
                                          ->where('status','succ')
                                          ->skip(20 * $this->request->page - 20)
                                          ->take(20)
                                          ->get(['edu.name','edup.name as product_name','edup.price','education_enrollment.enroll_id','user.uid','user.nickname','user.avatar_url','education_enrollment.name','education_enrollment.mobile','education_enrollment.content']);
        return $records;
    }
}
