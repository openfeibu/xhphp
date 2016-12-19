<?php

namespace App\Services;

use Log;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Repositories\OrderInfoRepository;

class OrderInfoService
{
	protected $request;

    protected $orderInfoRepository;

    protected $userRepository;

	function __construct(Request $request,
						 OrderInfoRepository $orderInfoRepository,
						 UserRepository $userRepository)
	{
		$this->request = $request;
        $this->orderInfoRepository = $orderInfoRepository;
        $this->userRepository = $userRepository;
	}
	public function create($order_info)
	{
		$order_info['postscript'] = $order_info['postscript'] ? $order_info['postscript'] : '';
		return $this->orderInfoRepository->create($order_info);
	}
	public function updateOrderInfo($order_sn,$update)
	{
		return $this->orderInfoRepository->updateOrderInfo($order_sn,$update);
	}
	public function getOrderInfos($page = '20',$uid = '',$type)
	{
		$where = ['order_info.uid' => $uid];
		
		$order_infos = $this->orderInfoRepository->getOrderInfos($page,$where,$type);
		
		foreach( $order_infos as $key => $order_info )
		{
			$order_info->status_desc = trans('common.pay_status.'.$order_info->pay_status);
			if($order_info->pay_status == 1){
				$order_info->status_desc = trans('common.order_status.'.$order_info->order_status);
				if($order_info->order_status == 1)
				{
					$order_info->status_desc = trans('common.shipping_status.'.$order_info->shipping_status);
				}
			}
		}
		return $order_infos;
	}
	public function getOrderInfo($order_id)
	{
		$order_info = $this->orderInfoRepository->getOrderInfo($order_id);
		$order_goodses =  $this->orderInfoRepository->getOrderGoodses($order_id);
		foreach( $order_goodses as $key => $order_goods )
		{
			$order_goods->total_fee = $order_goods->goods_price * $order_goods->goods_number;
		}
		$order_info->order_goodses = $order_goodses;
		return $order_info;
	}
}