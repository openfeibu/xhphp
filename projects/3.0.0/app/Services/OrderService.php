<?php

namespace App\Services;

use Log;
use Event;
use Mail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\HelpService;
use App\Services\PushService;
use App\Services\MessageService;
use App\Repositories\UserRepository;
use App\Repositories\GoodsRepository;
use App\Repositories\GoodsCategoryRepository;
use App\Repositories\OrderRepository;
use App\Repositories\OrderInfoRepository;
use App\Events\Integral\Integrals;

class OrderService
{
	protected $request;

    protected $orderRepository;

    protected $userRepository;

	function __construct(Request $request,
						 PushService $pushService,
						 HelpService $helpService,
						 MessageService $messageService,
						 OrderRepository $orderRepository,
						 UserRepository $userRepository,
						 OrderInfoRepository $orderInfoRepository,
						 GoodsCategoryRepository $goodsCategoryRepository,
						 GoodsRepository $goodsRepository)
	{
		$this->request = $request;
		$this->messageService = $messageService;
        $this->pushService = $pushService;
        $this->helpService = $helpService;
        $this->orderRepository = $orderRepository;
		$this->orderInfoRepository = $orderInfoRepository;
        $this->userRepository = $userRepository;
		$this->goodsCategoryRepository = $goodsCategoryRepository;
		$this->goodsRepository = $goodsRepository;
	}

	/**
	 * 获取任务列表
	 */
	public function getOrderList($page,$type)
	{
		$orders = $this->orderRepository->getOrderList($page,$type);
		foreach( $orders as $key => $order )
		{
			$order->share_url = config('app.order_share_url').'?oid='.$order->oid;
			$order->courier_openid = '';
			if($order->courier_id){
				$user = $this->userRepository->getUserByUserID($order->courier_id,'openid');
				$order->courier_openid = $user->openid;
			}
			$order->order_status = trans('common.task_status.'.$order->status);
			$order->created_at_desc = friendlyDate($order['created_at']->format('Y-m-d H:i:s'));
			if($order->type == 'personal')
			{
				$order->description = handleOrderDescription($order->description);
			}
		}
		return $orders;
	}

	/**
	 * 获取指定任务ID可公开信息
	 */
	public function getSingleOrder($order_id)
	{
		$order = $this->orderRepository->getSingleOrder($order_id);
		$order['created_at_desc'] = friendlyDate($order['created_at']->format('Y-m-d H:i:s'));
		return $this->hanle_order_info($order);
	}
	public function getOrderColumn($where,$columns = ['*'])
	{
		$order = $this->orderRepository->getOrderColumn($where,$columns);
		$order['created_at_desc'] = friendlyDate($order['created_at']->format('Y-m-d H:i:s'));
		return $order;
	}
	public function isExistsOrderColumn($where,$columns = ['*'])
	{
		$order = $this->orderRepository->getOrderColumn($where,$columns);
		if(!$order)
		{
			throw new \App\Exceptions\Custom\FoundNothingException();
		}
		$order['created_at_desc'] = friendlyDate($order['created_at']->format('Y-m-d H:i:s'));
		return $order;
	}
	public function getSingleOrderByToken($order_id)
	{
		$order = $this->orderRepository->getSingleOrderByToken($order_id);
		$order['created_at_desc'] = friendlyDate($order['created_at']);
		$is_user = false;
		if(isset($this->request->token))
		{
			$user = $this->userRepository->getUserByToken($this->request->token);
			if($user && ($user->uid == $order['courier_id'] || $user->uid == $order['owner_id']))
			{
				$is_user = true;
			}
		}

		$order['description'] = $is_user ? $order['description'] : handleOrderDescription($order['description']);
		return $this->hanle_order_info($order);
	}
	private function hanle_order_info($order)
	{
		$order['order_status'] = trans('common.task_status.'.$order['status']);
		$order['share_url'] = config('app.order_share_url').'?oid='.$order['oid'];
		$goods_desc = '';
		if($order['type'] == 'canteen' && $order['order_id'])
		{
			$order_info = $this->orderInfoRepository->isExistsOrderInfo(['order_id' => $order['order_id']],['postscript']);
			$order_goods = $this->orderInfoRepository->getOrderGoodses($order['order_id'],['goods_price','goods_number','goods_name','goods_id']);
			foreach ($order_goods as $key => $goods) {
				$org_goods = $this->goodsRepository->existGoods($goods->goods_id);
				$cat = $this->goodsCategoryRepository->isExistsCat(['cat_id' => $org_goods->cat_id],['cat_name']);
				$goods_desc .= "[".$cat->cat_name."]".$goods->goods_name . ' ' .$goods->goods_number.'件'. ' '.$org_goods->goods_price."/件\n";
			}
			$goods_desc = $order_info->postscript ? $goods_desc. "\n". "[备注]" .$order_info->postscript : $goods_desc;
		}
		$order['goods_desc'] = $goods_desc;
		$order['description'] = $goods_desc ? $order['description'] . "\n" . $order['goods_desc'] : $order['description'];
		$order['created_at_desc'] = friendlyDate($order['created_at']);
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
	public function getOrderBySn($order_sn)
	{
		$order = $this->orderRepository->getOrderBySn($order_sn);
		$order->order_status = trans('common.task_status.'.$order['status']);
		$order['created_at_desc'] = friendlyDate($order['created_at']->format('Y-m-d H:i:s'));
		return $order;
	}
	public function getOrder($where = [],$columns = ['*'],$is_exception = true)
	{
		$order = $this->orderRepository->getOrder($where,$columns,$is_exception);
		return $order;
	}
	public function getOrderDetail($order_id)
	{
		$order = $this->orderRepository->getOrderDetail($order_id);
		$order->order_status = trans('common.task_status.'.$order['status']);
		$order->share_url = config('app.order_share_url').'?oid='.$order->oid;
		$order['created_at_desc'] = friendlyDate($order['created_at']->format('Y-m-d H:i:s'));
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

        $order['uid'] = $order['uid'];
        $order['phone'] = $order['phone'] ;
		$order['type'] = isset($order['type']) ? $order['type'] : 'personal';
		$order['order_id'] = isset($order['order_id']) ? $order['order_id'] : 0;
		$order_id = $this->orderRepository->createOrder($order)->oid;

		$email = '434497443@qq.com';
		$num = Mail::send('emails.task', [], function($message) use($email) {
			$message->from(config('mail.from')['address'],config('mail.from')['name']);
			$message->subject('新任务');
			$message->to($email);
		});
		//记录发单时间
		$this->orderRepository->logOrderstatusChg($order['uid'], $order_id, 'new');
	}


	/**
	 * 接受任务
	 */
	public function claimOrder(array $orderInfo)
	{
		//获取当前任务信息
		$order = $this->getOrder(['oid' => $orderInfo['order_id']]);

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

			return $order;
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
			$orders[$key]['share_url'] = config('app.order_share_url').'?oid='.$order['oid'];
			$orders[$key]['type'] = 'task';
			$orders[$key]['created_at_desc'] = friendlyDate($order['created_at']);
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
			$orders[$key]['type'] = 'work';
			$orders[$key]['created_at_desc'] = friendlyDate($order['created_at']);
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
		if(isset($param['courier_cancel'])&&$param['courier_cancel']){
			$this->orderRepository->logOrderstatusChg($uid, $param['order_id'], 'courier_cancel');
		}else{
			$this->orderRepository->logOrderstatusChg($uid, $param['order_id'], $param['status']);
		}


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
	//切记，修改此方法必须修改   autoConfirmFinishWork  。以后修改
	public function confirmFinishWork($order,$walletService,$tradeAccountService)
	{
		if ($order->status != 'finish') {
            throw new \App\Exceptions\Custom\OutputServerMessageException('当前任务状态不允许结算任务');
        }
        $param = [
            'order_id' => $order->oid,
            'status' => 'completed',
        ];
        $courier = $this->userRepository->getUserByUserID($order->courier_id);

		if($order->uid)
		{
			$param['uid'] = $order->uid;
		}
        $wallet = $courier->wallet + $order->fee + $order->goods_fee - $order->service_fee;
        $fee = $order->fee + $order->goods_fee - $order->service_fee;
		$walletData = array(
			'uid' => $courier->uid,
			'out_trade_no' => $order->order_sn,
			'wallet' => $wallet ,
			'fee'	=> $fee,
			'service_fee' => $order->service_fee,
			'pay_id' => $order->pay_id,
			'wallet_type' => 1,
			'trade_type' => 'AcceptTask',
			'description' => '接任务',
        );

        $trade_no = 'wallet'.$this->helpService->buildOrderSn('XH');
		$trade = array(
    		'uid' => $courier->uid,
			'out_trade_no' => $order->order_sn,
			'trade_no' => $trade_no,
			'fee' => $fee,
			'service_fee' => $order->service_fee,
			'pay_id' => $order->pay_id,
			'wallet_type' => 1,
			'trade_status' => 'income',
			'from' => 'order',
			'trade_type' => 'AcceptTask',
			'description' => '接任务' ,
		);

		$this->updateOrderStatus($param);

		$walletService->updateWallet($courier->uid,$wallet);
		$walletService->store($walletData);
		$tradeAccountService->addThradeAccount($trade);
		//纸条通知接单人

        $order = $this->getSingleOrderAllInfo($order->oid);
        $this->messageService->SystemMessage2SingleOne($order->courier_id, '您好，发单人已结算你完成的任务，赶紧去看看吧。');

        //推送通知给接单人
        $custom = [
			'open' => 'mytask',
			'data' => $order->description,
		];
        $this->pushService->PushUserTokenDevice('校汇任务', '您好，发单人已结算你完成的任务，赶紧去看看吧。', $order->courier_id,1,$custom);

        //积分更新(给发单人加分)
        Event::fire(new Integrals('发布任务'));
        //积分更新(给接单人加分)
        Event::fire(new Integrals('完成任务', $courier));
	}
	public function autoConfirmFinishWork($order,$walletService,$tradeAccountService)
	{
		if ($order->status != 'finish') {
            throw new \App\Exceptions\Custom\OutputServerMessageException('当前任务状态不允许结算任务');
        }
        $param = [
            'order_id' => $order->oid,
            'status' => 'completed',
        ];
        $courier = $this->userRepository->getUserByUserID($order->courier_id);

		if($order->uid)
		{
			$param['uid'] = $order->uid;
		}
        $wallet = $courier->wallet + $order->fee + $order->goods_fee - $order->service_fee;
        $fee = $order->fee + $order->goods_fee - $order->service_fee;
		$walletData = array(
			'uid' => $courier->uid,
			'out_trade_no' => $order->order_sn,
			'wallet' => $wallet ,
			'fee'	=> $fee,
			'service_fee' => $order->service_fee,
			'pay_id' => $order->pay_id,
			'wallet_type' => 1,
			'trade_type' => 'AcceptTask',
			'description' => '接任务',
        );

        $trade_no = 'wallet'.$this->helpService->buildOrderSn('XH');
		$trade = array(
    		'uid' => $courier->uid,
			'out_trade_no' => $order->order_sn,
			'trade_no' => $trade_no,
			'fee' => $fee,
			'service_fee' => $order->service_fee,
			'pay_id' => $order->pay_id,
			'wallet_type' => 1,
			'trade_status' => 'income',
			'from' => 'order',
			'trade_type' => 'AcceptTask',
			'description' => '接任务' ,
		);

		$this->schedluUpdateOrderStatus($param);

		$walletService->updateWallet($courier->uid,$wallet);
		$walletService->store($walletData);
		$tradeAccountService->addThradeAccount($trade);
		//纸条通知接单人

        $order = $this->getSingleOrderAllInfo($order->oid);
        $this->messageService->SystemMessage2SingleOne($order->courier_id, '您好，发单人已结算你完成的任务，赶紧去看看吧。');

        //推送通知给接单人
        $custom = [
			'open' => 'mytask',
			'data' => $order->description,
		];
        $this->pushService->PushUserTokenDevice('校汇任务', '您好，发单人已结算你完成的任务，赶紧去看看吧。', $order->courier_id,1,$custom);
	}
	public function delete($where)
	{
		return $this->orderRepository->delete($where);
	}
	public function getOrderBonus($uid)
	{
		$today = date("Y-m-d");
		$w = date('w',strtotime($today));
		$week_start = date('Y-m-d',strtotime("$today - $w days"));
		$dates = [];
		for ($i=0; $i <= 6; $i++) {
			$dates[$i] = date('Y-m-d',strtotime($week_start." +$i day"));
		}
		$weekarray = array("日","一","二","三","四","五","六");
		foreach($dates as $k => $v)
		{
			$dates[$k] = [];
			$dates[$k]['date'] = '周'.$weekarray[$k].' '.date('m-d',strtotime($v));
			$user_bonus = $this->orderRepository->getUserOrderBonus(['date' => $v,'uid' => $uid ]);
			$dates[$k]['number'] = $user_bonus ? $user_bonus->number : 0;
			$dates[$k]['bonus'] = $user_bonus ? $user_bonus->bonus : 0;
			$dates[$k]['is_today'] = $today == $v ? 1 : 0;
		}
		return $dates;
	}
	public function updateUserOrderBonus($user)
	{
		$today = date("Y-m-d");
		$number = $this->orderRepository->getOrderBonusCount($user->uid,$today);
		$bonus = $this->orderRepository->getOrderBonus($number);
		$today_bonus = $this->orderRepository->getUserOrderBonus(['date' => $today,'uid' => $user->uid ]);
		if($today_bonus){
			$today_bonus = $this->orderRepository->updateUserOrderBonus(['date' => $today,'uid' => $user->uid ],['number' => $number,'bonus' => $bonus]);
		}else{
			$data = [
				'uid' => $user->uid,
				'number' => $number,
				'bonus' => $bonus,
				'date' => $today,
				'status' => 0,
			];
			$this->orderRepository->createUserOrderBonus($data);
		}
		return true;
	}

}
