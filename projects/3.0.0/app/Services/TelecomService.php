<?php

namespace App\Services;

use Session;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Repositories\TelecomRepository;

class TelecomService
{
	protected $request;

	protected $telecomRepository;

	protected $userRepository;

	function __construct(Request $request,
						 TelecomRepository $telecomRepository,
						 UserRepository $userRepository)
	{
		$this->request = $request;
		$this->telecomRepository = $telecomRepository;
		$this->userRepository = $userRepository;
	}
	//获取用户最新一个实名几率
	public function getLastReal ($uid)
	{
		return $this->telecomRepository->getLastReal($uid);
	}
	//获取电信套餐
	public function getPackageList ()
	{
		return $this->telecomRepository->getPackageList();
	}
	//插入实名记录
	public function storeRealName ($realData)
	{
		return $this->telecomRepository->storeRealName($realData);
	}
	//根据电信手机查询存在已支付订单
	public function hasTelecomOrder ($telecom_phone)
	{
		return $this->telecomRepository->hasTelecomOrder($telecom_phone);
	}
	//根据电信手机获取实名信息
	public function getRealByPhone ($telecom_phone)
	{
		return $this->telecomRepository->getRealByPhone($telecom_phone);
	}
	//获取电信套餐
	public function getTelecomPackage ($package_id)
	{
		return $this->telecomRepository->getTelecomPackage($package_id);
	}
	//插入电信订单
	public function storeTelecomOrderStore ($telecomOrderData)
	{
		return $this->telecomRepository->storeTelecomOrderStore($telecomOrderData);
	}
	//插入电信临时订单
	public function storeTelecomOrderTemStore ($telecomOrderData)
	{
		return $this->telecomRepository->storeTelecomOrderTemStore($telecomOrderData);
	}
	//更新电信订单
	public function updateTelecomTemOrder($telecom_trade_no,$updateArr)
	{
		return $this->telecomRepository->updateTelecomTemOrder($telecom_trade_no,$updateArr);
	}
	//根据电信订单号获取订单详情
	public function getTelecomOrderByNo ($telecom_trade_no)
	{
		return $this->telecomRepository->getTelecomOrderByNo($telecom_trade_no);
	}
	public function getTelecomOrdersByUid ($uid)
	{
		return $this->telecomRepository->getTelecomOrdersByUid($uid);
	}
	public function hasTelecomOrderByUid ($uid)
	{
		return $this->telecomRepository->hasTelecomOrderByUid($uid);
	}
	public function getTelecomOrdersByTransactor ($transactor)
	{
		return $this->telecomRepository->getTelecomOrdersByTransactor($transactor);
	}
	public function getTelecomOrdersCount ()
	{
		return $this->telecomRepository->getTelecomOrdersCount();
	}
	public function getUserTelecomOrdersCount ($uid)
	{
		return $this->telecomRepository->getUserTelecomOrdersCount($uid);
	}
	public function getTelecomOrders ()
	{
		return $this->telecomRepository->getTelecomOrders();
	}
	public function updateTelecomOrdersById ($id,$updateArr)
	{
		return $this->telecomRepository->updateTelecomOrdersById($id,$updateArr);
	}
	public function getTelecomEnrollmentTimes()
	{
		$times = $this->telecomRepository->getTelecomEnrollmentTimes();
		foreach ($times as $key => $time) {
			$count_data = $this->getTelecomEnrollmentCount($time->time_id);
			if($count_data)
			{
				$time->count = max($time->count - $count_data->count,0);
			}
			$time->time_start = substr($time->time_start,0,5);
			$time->time_end = substr($time->time_end,0,5);
		}
		return $times;
	}
	public function getTelecomEnrollmentTime($time_id)
	{
		$time = $this->telecomRepository->getTelecomEnrollmentTime(['time_id' => $time_id]);
		if($time){
			$time->time_start = substr($time->time_start,0,5);
			$time->time_end = substr($time->time_end,0,5);
			$count_data = $this->getTelecomEnrollmentCount($time->time_id);
			if($count_data)
			{
				$time->count = max($time->count - $count_data->count,0);
			}
		}
		return $time;
	}
	public function getTelecomEnrollmentCount($time_id)
	{
		$date = date("Y-m-d",strtotime("+1 day"));
		$count_data = $this->telecomRepository->getTelecomEnrollmentCount(['date' => $date ,'time_id' => $time_id]);
		return $count_data;
	}
	public function	enrollData($where)
	{
		$enroll_data = $this->telecomRepository->getEnrollData($where);
		return $enroll_data;
	}
	public function enroll($data)
	{
		$enroll = $this->telecomRepository->enroll($data);
		if($enroll){
			$this->changeEnrollmentCount($data);
			return $enroll;
		}
		else{
			throw new \App\Exceptions\Custom\OutputServerMessageException('报名失败，请稍后再试！');
		}
	}
	public function changeEnrollmentCount($data)
	{
		$count_data = $this->telecomRepository->getTelecomEnrollmentCount(['date' => $data['date']]);
		if(!$count_data)
		{
			$this->telecomRepository->createEnrollmentCount([
				'date' => $data['date'],
				'count' => 1,
			]);
		}else{
			$this->telecomRepository->incrementEnrollCount(['date' => $count_data['date']]);
		}
	}
	public function getSchoolCampusBuildings()
	{
		$campuses = $this->getSchoolCampuses();
		foreach ($campuses as $key => $campus) {
			$campus->buildings = $this->getSchoolBuildings($campus->campus_id);
		}
		return $campuses;
	}
	public function getSchoolCampuses()
	{
		return $this->telecomRepository->getSchoolCampuses();
	}
	public function getSchoolBuildings($campus_id = '')
	{
		return $this->telecomRepository->getSchoolBuildings($campus_id);
	}
}
