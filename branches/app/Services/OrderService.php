<?php

namespace App\Services;

use Log;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Repositories\OrderRepository;

class OrderService
{
	protected $request;

    protected $orderRepository;

    protected $userRepository;

	function __construct(Request $request,
						 OrderRepository $orderRepository,
						 UserRepository $userRepository)
	{
		$this->request = $request;
        $this->orderRepository = $orderRepository;
        $this->userRepository = $userRepository;

	}

	/**
	 * 获取任务列表
	 */
	public function getOrderList($page)
	{
		$orders = $this->orderRepository->getOrderList($page);
		foreach( $orders as $key => $order )
		{
			$order->share_url = config('app.order_share_url').'?oid='.$order->oid;
		}
		return $orders;
	}

	/**
	 * 获取指定任务ID可公开信息
	 */
	public function getSingleOrder($order_id)
	{
		$order = $this->orderRepository->getSingleOrder($order_id);
		$order->order_status = trans('common.task_status.'.$order['status']);
		return $order;
	}
	public function getSingleOrderByToken($order_id)
	{
		$order = $this->orderRepository->getSingleOrderByToken($order_id);
		$order['order_status'] = trans('common.task_status.'.$order['status']);
		return $order;
	}

	/**
	 * 获取指定任务ID的所有信息
	 */
	public function getSingleOrderAllInfo($order_id)
	{
		$order = $this->orderRepository->getSingleOrderAllInfo($order_id);
		$order->order_status = trans('common.task_status.'.$order['status']);
		return $order;
	}
	public function getOrderBySn ($order_sn)
	{
		$order = $this->orderRepository->getOrderBySn($order_sn);
		$order->order_status = trans('common.task_status.'.$order['status']);
		return $order;
	}

	/**
	 * 检验是否已超过发单限制
	 */
	public function checkOrderLimit()
	{
		$user_id = $this->userRepository->getUser()->uid;
		$result = $this->orderRepository->userOrderCount($user_id);
		if ($result >= 3) {
			throw new \App\Exceptions\Custom\OutputServerMessageException('今天发单数量已达到发单限制，请明天再试');
		}
		return true;
	}

	/**
	 * 检验是否接同一个人发的单的数量太多
	 */
	public function checkScalping($order_id)
	{
		$user_id = $this->userRepository->getUser()->uid;
		$result = $this->orderRepository->checkScalping($user_id, $order_id);
		if ($result >= 3) {
			throw new \App\Exceptions\Custom\OutputServerMessageException('不能多次接同一发单人的单。');
		}
		return true;
	}

	/**
	 * 创建新任务
	 */
	public function createOrder(array $order)
	{
        //获得当前用户信息
        $user = $this->userRepository->getUser();

        $order['uid'] = $user->uid;
        $order['phone'] = $order['phone'] ?: $user->mobile_no;

		$order_id = $this->orderRepository->createOrder($order)->oid;

		//记录发单时间
		$this->orderRepository->logOrderstatusChg($user->uid, $order_id, 'new');
	}

	/**
	 * 接受任务
	 */
	public function claimOrder(array $orderInfo)
	{
		//获取当前任务信息
		$order = $this->orderRepository->getOrder($orderInfo['order_id']);

		//获取当前用户信息
		$user = $this->userRepository->getUser();
		$orderInfo['courier_id'] = $user->uid;

		//检验接单人跟发单人是否为同一人
		$this->isCourierOwner($user->uid, $order->owner_id);
		// if ($order->status == 'new' and $order->created_at > date('Y-m-d H:i:s',strtotime("-1 day"))) {
		if ($order->status == 'new') {

			$this->orderRepository->claimOrder($orderInfo);

			//记录接单时间
			$this->orderRepository->logOrderstatusChg($user->uid, $orderInfo['order_id'], 'accepted');

		} elseif ($order->status != 'new') {
			throw new \App\Exceptions\Custom\OutputServerMessageException('任务已被接');
		} else {
			throw new \App\Exceptions\Custom\OutputServerMessageException('任务已过有效期');
		}
	}

	/**
	 * 检验接单人跟任务所有者是否为同一人
	 */
	public function isCourierOwner($courier_id, $owner_id)
	{
		if ($courier_id == $owner_id) {
        	throw new \App\Exceptions\Custom\OrderSameUserException('');
		}
	}

	/**
	 * 获取我的任务列表
	 */
	public function getMyOrder($page)
	{
		//获取当前用户信息
		$user = $this->userRepository->getUser();

		$orders =  $this->orderRepository->getMyOrder($user->uid, $page);

		foreach( $orders as $key => $order )
		{
			$orders[$key]['order_status'] =  trans('common.task_status.'.$order['status']);
			$order[$key]['share_url'] = config('app.order_share_url').'?oid='.$order['oid'];
		}
		return $orders;
	}

	/**
	 * 获取我的工作列表
	 */
	public function getMyWork($page)
	{
		//获取当前用户信息
		$user = $this->userRepository->getUser();
		$orders = $this->orderRepository->getMyWork($user->uid, $page);

		foreach( $orders as $key => $order )
		{
			$orders[$key]['order_status'] =  trans('common.task_status.'.$order['status']);
		}
		return $orders;
	}

	/**
     * 更新任务状态
     */
    public function updateOrderStatus($param)
    {
		$uid = $this->userRepository->getUser()->uid;
    	//获取当前用户信息
    	if(!isset($param['uid']))
    	{
			$param['uid'] = $uid;
    	}

		$result = $this->orderRepository->updateOrderStatus($param);
		if (!$result) {
    		throw new \App\Exceptions\Custom\FoundNothingException();
		}

		//记录操作时间
		$this->orderRepository->logOrderstatusChg($uid, $param['order_id'], $param['status']);

    }
    public function schedluUpdateOrderStatus ($param)
    {
    	$result = $this->orderRepository->updateOrderStatus($param);
		if (!$result) {
    		throw new \App\Exceptions\Custom\FoundNothingException();
		}

		//记录操作时间
		$this->orderRepository->logOrderstatusChg($param['uid'], $param['order_id'], $param['status']);
    }
    public function updateOrderStatusNew ($out_trade_no)
    {
    	$result = $this->orderRepository->updateOrderStatusNew($out_trade_no);
		if (!$result) {
    		throw new \App\Exceptions\Custom\FoundNothingException();
		}
    }
    public function dropWaitPayOrder ($uid)
    {
    	$this->orderRepository->dropWaitPayOrder($uid);
    }
	public function remindOrder(){
		$uid = $this->userRepository->getUser()->uid;
		return $this->orderRepository->remindOrder($uid);
	}
	public function getRecommendOrders ($number)
	{
		$orders = $this->orderRepository->getRecommendOrders($number);
		foreach( $orders as $key => $order )
		{
			$order->share_url = config('app.order_share_url').'?oid='.$order->oid;
		}
		return $orders;
	}
	public function getOrderCount ($where)
	{
		return $this->orderRepository->getOrderCount($where);
	}
}