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
	//��ȡ�û�����һ��ʵ������
	public function getLastReal ($uid)
	{
		return $this->telecomRepository->getLastReal($uid);
	}
	//��ȡ�����ײ�
	public function getPackageList ()
	{
		return $this->telecomRepository->getPackageList();
	}
	//����ʵ����¼
	public function storeRealName ($realData)
	{
		return $this->telecomRepository->storeRealName($realData);
	}
	//���ݵ����ֻ���ѯ������֧������
	public function hasTelecomOrder ($telecom_phone)
	{
		return $this->telecomRepository->hasTelecomOrder($telecom_phone);
	}
	//���ݵ����ֻ���ȡʵ����Ϣ
	public function getRealByPhone ($telecom_phone)
	{
		return $this->telecomRepository->getRealByPhone($telecom_phone);
	}
	//��ȡ�����ײ�
	public function getTelecomPackage ($package_id)
	{
		return $this->telecomRepository->getTelecomPackage($package_id);
	}
	//������Ŷ���
	public function storeTelecomOrderStore ($telecomOrderData)
	{
		return $this->telecomRepository->storeTelecomOrderStore($telecomOrderData);
	}
	//���������ʱ����
	public function storeTelecomOrderTemStore ($telecomOrderData)
	{
		return $this->telecomRepository->storeTelecomOrderTemStore($telecomOrderData);
	}
	//���µ��Ŷ���
	public function updateTelecomTemOrder($telecom_trade_no,$updateArr)
	{
		return $this->telecomRepository->updateTelecomTemOrder($telecom_trade_no,$updateArr);
	}
	//���ݵ��Ŷ����Ż�ȡ��������
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