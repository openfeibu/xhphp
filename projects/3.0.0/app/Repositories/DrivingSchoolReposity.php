<?php

namespace App\Repositories;

use DB;
use Log;
use Session;
use App\User;
use App\DrivingSchool;
use App\DrivingSchoolPro;
use App\DrivingSchoolEcrollment;
use Illuminate\Http\Request;

class DrivingSchoolReposity
{
    public function __construct(Request $request)
    {
        $this->request = $request;
        if(isset($this->request->page))
        {
            $this->page = intval($this->request->page);
        }
    }
	public function getDrivingSchools()
    {
        $driving_schools = DrivingSchool::skip(20 * $this->page - 20)
                                        ->orderBy('driving_school.ds_id','desc')
                                        ->take(20)
                                        ->get(['driving_school.ds_id', 'driving_school.name','driving_school.desc','driving_school.logo_url']);
        return $driving_schools;
    }
    public function getDrivingSchool($ds_id)
    {
        $driving_school = DrivingSchool::where('ds_id',$ds_id)->first(['logo_url','img_url','ds_id','name','desc','content','tell']);
        return $driving_school;
    }
    /*  获取该驾校最低的价格 */
    public function getMinPrice($ds_id)
    {
        return DrivingSchoolPro::where('ds_id',$ds_id)->min('price');
    }
    public function getPro($product_id){
        $product = DrivingSchoolPro::join('driving_school as ds','ds.ds_id','=','driving_school_product.ds_id')
                                ->where('driving_school_product.product_id',$product_id)
                                ->first(['ds.ds_id','ds.name','driving_school_product.product_id','driving_school_product.name as product_name','driving_school_product.price']);
        return $product;
    }
    public function getPros($ds_id)
    {
        $pros = DrivingSchoolPro::where('ds_id',$ds_id)->get(['name as product_name','desc','price','original_price']);
        return $pros;
    }
    public function enroll($enrollment)
    {
        return DrivingSchoolEcrollment::create($enrollment);
    }
    public function getEnrollRecords($uid)
    {
        //$records = DrivingSchoolEcrollment::where('uid',$uid)->get(['name','mobile','content','ds_id','pro_id','enroll_id']);
        $records = DrivingSchoolEcrollment::join('driving_school as ds','ds.ds_id','=','driving_school_enrollment.ds_id')
                                          ->join('driving_school_product as dsp','dsp.product_id','=','driving_school_enrollment.pro_id')
                                          ->where('driving_school_enrollment.uid',$uid)
                                          ->get(['ds.name','dsp.name as product_name','dsp.price','driving_school_enrollment.enroll_id']);
        return $records;
    }
    public function getEnrollRecord($where,$columns = [])
    {
        $columns = $columns ? $columns : ['ds.name','dsp.name as product_name','dsp.price','dsp.desc','driving_school_enrollment.enroll_id','driving_school_enrollment.name as enroll_name','driving_school_enrollment.mobile','driving_school_enrollment.content'];
        $record = DrivingSchoolEcrollment::join('driving_school as ds','ds.ds_id','=','driving_school_enrollment.ds_id')
                                          ->join('driving_school_product as dsp','dsp.product_id','=','driving_school_enrollment.pro_id')
                                          ->where($where)
                                          ->first($columns);
        return $record;
    }
}
