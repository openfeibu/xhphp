<?php

namespace App\Repositories;

use DB;
use Cache;
use App\TelecomPackage;
use App\TelecomRealName;
use App\TelecomOrder;
use App\TelecomOrderTem;
use App\TelecomEnrollment;
use App\TelecomEnrollmentTime;
use App\TelecomEnrollmentCount;
use App\SchoolBuilding;
use App\SchoolCampus;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class TelecomRepository
{
	protected $request;

	function __construct(Request $request)
	{
		$this->request = $request;
	}
	public function getLastReal ($uid)
	{
		return TelecomRealName::where('uid',$uid)->orderBy('real_id','DESC')->first();
	}
	public function getPackageList ()
	{
		return TelecomPackage::orderBy('sort','ASC')->get();
	}
	public function storeRealName ($realData)
	{
		$real = new TelecomRealName;
		$real->uid = $realData['uid'];
		$real->telecom_phone = $realData['telecom_phone'];
		$real->telecom_iccid = $realData['telecom_iccid'];
		$real->telecom_outOrderNumber = $realData['telecom_outOrderNumber'];
		$real->created_at = date('Y-m-d H:i:s');
		$real->save();
	}
	public function hasTelecomOrder ($telecom_phone)
	{
		return TelecomOrder::select(DB::raw('id'))->where('telecom_phone',$telecom_phone)->where('pay_status',1)->first();
	}
	public function getRealByPhone ($telecom_phone)
	{
		return TelecomRealName::where('telecom_phone',$telecom_phone)->orderBy('real_id','DESC')->first();
	}
	public function getTelecomPackage ($package_id)
	{
		return TelecomPackage::where('package_id',$package_id)->first();
	}
	public function storeTelecomOrderTemStore($telecomOrderData)
	{
		$telecomOrder = new TelecomOrderTem;
		$telecomOrder->telecom_trade_no = $telecomOrderData['telecom_trade_no'];
    	$telecomOrder->trade_no = $telecomOrderData['trade_no'];
    	$telecomOrder->uid = $telecomOrderData['uid'];
    	$telecomOrder->transactor = $telecomOrderData['transactor'];
    	$telecomOrder->telecom_iccid = $telecomOrderData['telecom_iccid'];
		$telecomOrder->telecom_phone = $telecomOrderData['telecom_phone'];
		$telecomOrder->telecom_outOrderNumber = $telecomOrderData['telecom_outOrderNumber'];
    	$telecomOrder->idcard = $telecomOrderData['idcard'];
    	$telecomOrder->major = $telecomOrderData['major'];
    	$telecomOrder->dormitory_no = $telecomOrderData['dormitory_no'];
    	$telecomOrder->student_id = $telecomOrderData['student_id'];
    	$telecomOrder->name = $telecomOrderData['name'];
    	$telecomOrder->fee = $telecomOrderData['fee'];
    	$telecomOrder->package_id = $telecomOrderData['package_id'];
    	$telecomOrder->package_name = $telecomOrderData['package_name'];
    	$telecomOrder->created_at = date('Y-m-d H:i:s');
		$telecomOrder->save();
	}
	private function storeTelecomOrderStore($telecomOrderData)
	{
		$telecomOrder = new TelecomOrder;
		$telecomOrder->telecom_trade_no = $telecomOrderData['telecom_trade_no'];
    	$telecomOrder->trade_no = $telecomOrderData['trade_no'];
    	$telecomOrder->uid = $telecomOrderData['uid'];
    	$telecomOrder->transactor = $telecomOrderData['transactor'];
    	$telecomOrder->telecom_iccid = $telecomOrderData['telecom_iccid'];
		$telecomOrder->telecom_phone = $telecomOrderData['telecom_phone'];
		$telecomOrder->telecom_outOrderNumber = $telecomOrderData['telecom_outOrderNumber'];
    	$telecomOrder->idcard = $telecomOrderData['idcard'];
    	$telecomOrder->major = $telecomOrderData['major'];
    	$telecomOrder->dormitory_no = $telecomOrderData['dormitory_no'];
    	$telecomOrder->student_id = $telecomOrderData['student_id'];
    	$telecomOrder->name = $telecomOrderData['name'];
    	$telecomOrder->fee = $telecomOrderData['fee'];
    	$telecomOrder->package_id = $telecomOrderData['package_id'];
    	$telecomOrder->package_name = $telecomOrderData['package_name'];
    	$telecomOrder->pay_status = 1;
    	$telecomOrder->telecom_real_name_status = 0;
    	$telecomOrder->created_at = date('Y-m-d H:i:s');
		$telecomOrder->save();
	}
	public function updateTelecomTemOrder($telecom_trade_no,$updateArr)
	{
		TelecomOrderTem::where('telecom_trade_no', $telecom_trade_no)->where('pay_status','0')->update($updateArr);
		if(!$this->getTelecomOrderByNo ($telecom_trade_no))
		{
			$telecomOrderData = TelecomOrderTem::where('telecom_trade_no',$telecom_trade_no)->first();
			$this->storeTelecomOrderStore($telecomOrderData);
		}

	}
	public function getTelecomOrderByNo ($telecom_trade_no)
	{
		return TelecomOrder::select(DB::raw('id,telecom_trade_no,trade_no,uid,telecom_phone,telecom_outOrderNumber,major,dormitory_no,student_id,name,fee,package_id,package_name,telecom_real_name_status,created_at'))->where('telecom_trade_no',$telecom_trade_no)->first();
	}
	public function getTelecomOrdersByUid ($uid)
	{
		return TelecomOrder::select(DB::raw('id,telecom_trade_no,trade_no,uid,telecom_phone,telecom_outOrderNumber,major,dormitory_no,student_id,name,fee,package_id,package_name,telecom_real_name_status,created_at'))->where('uid',$uid)->orderBy('id','desc')->get();
	}
	public function hasTelecomOrderByUid ($uid)
	{
		return TelecomOrder::select(DB::raw('id'))->where('uid',$uid)->first();
	}
	public function getTelecomOrdersByTransactor ($transactor)
	{
		//if (Cache::has('transactorTelecomOrders')) {
		//	$data = Cache::get('transactorTelecomOrders');
		//}else{
			$transactorTelecomOrders =  TelecomOrder::select(DB::raw('id,transactor,telecom_trade_no,trade_no,uid,telecom_phone,telecom_outOrderNumber,dormitory_no,name,fee,package_id,package_name,telecom_real_name_status,created_at'))->where('transactor',$transactor)->orderBy('id','desc')->get();
			$real_count = TelecomOrder::where('transactor',$transactor)->where('telecom_real_name_status',1)->count();
			$unreal_count = TelecomOrder::where('transactor',$transactor)->where('telecom_real_name_status',0)->count();
			$realing_count =  TelecomOrder::where('transactor',$transactor)->where('telecom_real_name_status',2)->count();
			$data = [
				'real_count' => $real_count,
            	'unreal_count' => $unreal_count,
            	'realing_count' => $realing_count,
				'telecomOrders' => $transactorTelecomOrders,
			];
		//	Cache::put('transactorTelecomOrders', $data, 1);
		//}
		return $data;
	}
	public function getTelecomOrdersCount ()
	{
		$real_count =  TelecomOrder::where('telecom_real_name_status',1)->count();
		$unreal_count = TelecomOrder::where('telecom_real_name_status',0)->count();
		$realing_count =  TelecomOrder::where('telecom_real_name_status',2)->count();
		$startTime = date("Y-m-d H:i:s",mktime(0,0,0,date("m"),date("d"),date("Y")));
		$endTime = date("Y-m-d H:i:s",mktime(23,59,59,date("m"),date("d"),date("Y")));
		$today_count = TelecomOrder::whereBetween('created_at',[$startTime,$endTime])->count();
		$count = $real_count + $unreal_count + $realing_count;
		$data = [
			'real_count' => $real_count,
            'unreal_count' => $unreal_count,
            'realing_count' => $realing_count,
            'today_count' => $today_count,
            'count' => $count
		];
		return $data;
	}
	public function getUserTelecomOrdersCount ($uid)
	{
		return TelecomOrder::where('uid',$uid)->count();
	}
	public function getTelecomOrders ()
	{
		return TelecomOrder::select(DB::raw('id,telecom_iccid,telecom_phone,telecom_outOrderNumber'))->where('telecom_real_name_status','<>',1)->get();
	}
	public function updateTelecomOrdersById ($id,$updateArr)
	{
		return TelecomOrder::where('id', $id)->update($updateArr);
	}
	public function getTelecomEnrollmentTimes()
	{
		return TelecomEnrollmentTime::orderBy('time_id','asc')->get(['time_id','count','time_start','time_end']);
	}
	public function getTelecomEnrollmentTime($where)
	{
		return TelecomEnrollmentTime::where($where)->first(['time_id','count','time_start','time_end']);
	}
	public function getTelecomEnrollmentCount($where)
	{
		return TelecomEnrollmentCount::where($where)->first(['count','date']);
	}
	public function enroll($data)
	{
		return TelecomEnrollment::create($data);
	}
	public function getEnrollData($where)
	{
		return TelecomEnrollment::where($where)->first(['enroll_id','name','date','created_at','dormitory_number','building_id','campus_id']);
	}
	public function getEnrolls($where)
	{
		return TelecomEnrollment::where($where)
								->skip(20 * $this->request->page - 20)
								->take(20)
								->orderBy('enroll_id', 'desc')
								->get(['enroll_id','name','date','created_at','dormitory_number','building_id','campus_id']);
	}
	public function createEnrollmentCount($data)
	{
		return TelecomEnrollmentCount::create($data);
	}
	public function incrementEnrollCount($where)
	{
		return TelecomEnrollmentCount::where($where)->increment('count');
	}
	public function getSchoolBuildings($campus_id)
	{
		if($campus_id){
			return SchoolBuilding::where('campus_id',$campus_id)->orderBy('building_id','asc')->get();
		}
		$school_buildings =  SchoolBuilding::join('school_campus','school_campus.campus_id','=','school_building.campus_id')->orderBy('school_building.campus_id','asc')->orderBy('school_building.building_id','asc')->get();
		return $school_buildings;
	}
	public function getSchoolCampuses()
	{
		return SchoolCampus::orderBy('campus_id','asc')->get();
	}
	public function getSchoolBuilding($building_id)
	{
		return SchoolBuilding::join('school_campus','school_campus.campus_id','=','school_building.campus_id')->where('school_building.building_id',$building_id)->first();
	}
}
