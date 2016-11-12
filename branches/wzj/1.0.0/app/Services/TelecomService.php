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
}