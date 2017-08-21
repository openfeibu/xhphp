<?php

namespace App\Repositories;

use DB;
use App\Order;
use Carbon\Carbon;
use App\OrderHistory;
use Illuminate\Http\Request;

class OrderRepository
{
	protected static $order;

	protected $request;

	function __construct(Request $request)
	{
		$this->request = $request;
	}

	/**
	 * 获取任务列表
	 */
	public function getOrderList($page,$type)
	{

        /*return Order::join('user', 'order.owner_id', '=', 'user.uid')
                    ->select(DB::raw('order.oid, user.openid, user.nickname, user.avatar_url, order.destination, order.description, order.fee, order.created_at'))
                    ->where('order.status', 'new')
                    ->where('order.created_at', '>', date('Y-m-d H:i:s',strtotime("-1 day")))
                    ->orderBy('order.created_at', 'desc')
                    ->skip(10 * $page - 10)
                    ->take(10)
                    ->get();*/
        $orders = Order::join('user', 'order.owner_id', '=', 'user.uid')
                    ->select(DB::raw('order.oid,order.owner_id,order.courier_id, user.openid, user.nickname, user.avatar_url, order.type,order.destination, order.description, order.fee, order.created_at ,order.status, CASE order.status WHEN "new" THEN 1 WHEN "accepted" THEN 2 WHEN "cancelling" THEN 3 WHEN "finish" THEN 4 WHEN "completed" THEN 5 WHEN "cancelled" THEN 6 END as order_status_num'))
                    ->whereIn('order.status', ['new','accepted','finish','completed']);
        if(!empty($type) && $type != 'all'){
	        $orders = $orders->where('type',$type);
	    }
	    return $orders->orderBy('order.created_at', 'desc')
                    ->skip(20 * $page - 20)
                    ->take(20)
                    ->get();
	}


	/**
	 * 获取指定任务ID可公开信息
	 */
	public function getSingleOrder($order_id)
	{
		return Order::join('user', 'order.owner_id', '=', 'user.uid')
                    ->select(DB::raw('order.oid,order.owner_id, order.type, order.order_id,order.order_sn,user.openid,user.mobile_no,order.pay_id, user.nickname, user.avatar_url,order.courier_id, order.alt_phone,order.destination,
                    				  order.description, order.fee, order.status, order.created_at,order.service_fee,courier.mobile_no as courier_mobile_no,courier.avatar_url as courier_avatar_url,courier.nickname as courier_nickname'))
                    ->leftJoin('user as courier', 'order.courier_id', '=', 'courier.uid')
                    ->where('oid', $order_id)
                    ->first();
	}

	public function getSingleOrderByCoutoms($where =[],$columns=[])
	{
		return Order::join('user', 'order.owner_id', '=', 'user.uid')
                    ->select(DB::raw('order.oid,order.owner_id, order.type, order.order_id,order.order_sn,user.openid,user.mobile_no,order.pay_id, user.nickname, user.avatar_url,order.courier_id, order.alt_phone,order.destination,
                    				  order.description, order.fee, order.status, order.created_at,order.service_fee,courier.mobile_no as courier_mobile_no,courier.avatar_url as courier_avatar_url,courier.nickname as courier_nickname'))
                    ->leftJoin('user as courier', 'order.courier_id', '=', 'courier.uid')
                    ->where($where)
					->where('courier_id','>','0')
                    ->first();
	}

	public function getOrderColumn($where,$columns)
	{
		$order = Order::where($where)->first($columns);
		return $order;
	}
	/**
	 * 获取指定任务ID可公开信息
	 */
	public function getSingleOrderByToken($order_id)
	{
		$order = Order::join('user', 'order.owner_id', '=', 'user.uid')
                    ->select(DB::raw('order.oid,order.owner_id, order.type,order.order_id,order.order_sn,user.openid,order.pay_id, user.nickname, user.avatar_url,order.courier_id, order.alt_phone,order.destination,
                    				  order.description, order.fee, order.status, order.created_at,courier.mobile_no as courier_mobile_no,courier.avatar_url as courier_avatar_url,courier.nickname as courier_nickname'))
                    ->leftJoin('user as courier', 'order.courier_id', '=', 'courier.uid')
                    ->where('oid', $order_id)
                    ->first()->toArray();

			$orderHistory = OrderHistory::select(DB::raw('created_at as status_change_at,new_status'))
									->where('oid',$order['oid'])
									->get();
			$or['new_time'] = "";
			$or['accepted_time'] = "";
			$or['finish_time'] = "";
			$or['completed_time'] = "";
			$or['cancelled_time'] = "";
			foreach($orderHistory as $k=>$v){
				if($v['new_status']=='new'){
					$or['new_time'] = $v['status_change_at'];
				}
				if($v['new_status']=='accepted'){
					$or['accepted_time'] = $v['status_change_at'];
				}
				if($v['new_status']=='finish'){
					$or['finish_time'] = $v['status_change_at'];
				}
				if($v['new_status']=='completed'){
					$or['completed_time'] = $v['status_change_at'];
				}
				if($v['new_status']=='cancelled'){
					$or['cancelled_time'] = $v['status_change_at'];
				}
			}
		$order_time = ['time'=>$or];
		if($order){
			return array_merge_recursive($order_time,$order);
		}else{
			return $order;
		}
	}
	/**
	 * 获取指定任务order_sn可公开信息
	 */
	public function getSingleOrderBySn($order_sn)
	{
		return Order::join('user', 'order.owner_id', '=', 'user.uid')
                    ->select(DB::raw('order.oid, order.description, order.fee, order.status, order.created_at,order_sn'))
                    ->where('order_sn', $order_sn)
                    ->first();
	}
	/**
	 * 获取指定任务ID的所有信息
	 */
	public function getSingleOrderAllInfo($order_id)
	{
		return Order::select(DB::raw('order.oid, order.owner_id, order.courier_id,order.pay_id, order.fee, order.service_fee,order.total_fee,order.alt_phone, order.description,
									  order.destination, order.status, order.created_at,order.order_sn,order.type,order.order_id,
									  owner.uid as owner_id, owner.mobile_no as owner_mobile_no, owner.nickname as owner_nickname,
									  owner.avatar_url as owner_avatar_url, owner.today_integral as owner_today_integral,
									  courier.uid as courier_id, courier.mobile_no as courier_mobile_no, courier.nickname as courier_nickname,
									  courier.avatar_url as courier_avatar_url, courier.today_integral as courier_today_integral'))
                    ->join('user as owner', 'order.owner_id', '=', 'owner.uid')
                    ->join('user as courier', 'order.courier_id', '=', 'courier.uid')
                    ->where('oid', $order_id)
                    ->first();
	}
	public function getOrderBySn ($order_sn)
	{
		return Order::select(DB::raw('order.oid, order.owner_id,order.order_sn,order.total_fee, owner.mobile_no as owner_mobile_no, owner.nickname as owner_nickname '))
                    ->join('user as owner', 'order.owner_id', '=', 'owner.uid')
                    ->where('order_sn', $order_sn)
                    ->first();
	}

	/**
	 * 统计今天用户发单数量
	 */
	public function userOrderCount($user_id)
	{
		return Order::where('owner_id', $user_id)
					->where('created_at', '>=', date('Y-m-d'))
					->count();
	}

	/**
	 * 检验是否接同一个人发的单的数量太多
	 */
	public function checkScalping($user_id, $order_id)
	{
		return Order::join('order as order2', function ($join) use ($order_id) {
						$join->where('order2.oid', '=', $order_id);
					})
					->where('order.courier_id', $user_id)
					->where('order.created_at', '>=', Carbon::today())
					->whereRaw('order.owner_id = order2.owner_id')
					->count();
	}

	/**
	 * 记录order状态改变时间
	 */
	public function logOrderstatusChg($user_id, $order_id, $chg2status)
	{
		$orderHistory = new OrderHistory;
		$orderHistory->uid = $user_id;
		$orderHistory->oid = $order_id;
		$orderHistory->new_status = $chg2status;
		$orderHistory->save();
	}

	/**
	 * 创建新任务
	 */
	public function createOrder(array $order_info)
	{
        try {
			self::$order = new Order;
			self::$order->setConnection('write');
			self::$order->owner_id = $order_info['uid'];
			self::$order->destination = $order_info['destination'];
			self::$order->description = $order_info['description'];
			self::$order->fee = $order_info['fee'];
			//self::$order->goods_fee = $order_info['goods_fee'];
			self::$order->total_fee = $order_info['total_fee'];
			self::$order->service_fee = $order_info['service_fee'];
			self::$order->alt_phone = $order_info['phone'];
			self::$order->order_sn = $order_info['order_sn'];
			self::$order->pay_id = $order_info['pay_id'];
			self::$order->type = $order_info['type'];
			self::$order->order_id = $order_info['order_id'];
			self::$order->status = $order_info['status'] ? $order_info['status'] : 'new';
			self::$order->save();

			return self::$order;
        } catch (Exception $e) {
        	throw new \App\Exceptions\Custom\RequestFailedException('无法新建任务');
        }
	}

	/**
	 * 接受任务
	 */
	public function claimOrder(array $order_info)
	{
		try {
			self::$order->setConnection('write');
	        self::$order->courier_id = $order_info['courier_id'];
	        self::$order->status = 'accepted';
	        self::$order->save();
		} catch (Exception $e) {
        	throw new \App\Exceptions\Custom\RequestFailedException('无法接受任务');
		}
	}

	/**
	 * 	获取当前任务信息
	 */
	public function getOrder($where,$columns,$is_exception)
	{
		self::$order = Order::where($where)->first($columns);
		if (!self::$order && $is_exception) {
        	throw new \App\Exceptions\Custom\FoundNothingException();
		}
		return self::$order;
	}
	public function getOrderDetail($order_id)
	{
		$order = Order::select(DB::raw('order.oid, if(user_courier.nickname IS NOT NULL,user_courier.nickname,"") as nickname, if(user_courier.avatar_url IS NOT NULL,user_courier.avatar_url,"") as avatar_url, if(order.courier_id!=0,user_courier.openid,"") as openid,
									  if(user_courier.mobile_no IS NOT NULL,user_courier.mobile_no,"") as phone, order.destination, order.description,
									  order.fee, order.alt_phone as alt_phone,CASE order.status WHEN "new" THEN 1 WHEN "accepted" THEN 2 WHEN "cancelling" THEN 3 WHEN "finish" THEN 4 WHEN "completed" THEN 5 WHEN "cancelled" THEN 6 END as order_status_num, order.status,order.created_at'))
                    ->join('user as user_owner', 'order.owner_id', '=', 'user_owner.uid')
                    ->leftJoin('user as user_courier', 'order.courier_id', '=', 'user_courier.uid')
                    ->where('order.oid', $order_id)
                    ->first();
		if(!$order && $is_exception) {
        	throw new \App\Exceptions\Custom\FoundNothingException();
		}
		$orderHistory = OrderHistory::select(DB::raw('created_at as status_change_at,new_status'))
								->where('oid',$order->oid)
								->get();

		$order->new_time = $order->created_at;
		$order->accepted_time = "";
		$order->finish_time = "";
		$order->completed_time = "";
		$order->cancelled_time = "";
		foreach($orderHistory as $kk=>$v){
			if($v['new_status']=='new'){
				$order->new_time = $v['status_change_at'];
			}
			if($v['new_status']=='accepted'){
				$order->accepted_time = $v['status_change_at'];
			}
			if($v['new_status']=='finish'){
				$order->finish_time = $v['status_change_at'];
			}
			if($v['new_status']=='completed'){
				$order->completed_time = $v['status_change_at'];
			}
			if($v['new_status']=='cancelled'){
				$order->cancelled_time = $v['status_change_at'];
			}
		}
		return $order;
	}
	/**
	 * 获取我的任务列表
	 */
	public function getMyOrder($uid, $page)
	{
		$orders = Order::select(DB::raw('order.oid, if(user_courier.nickname IS NOT NULL,user_courier.nickname,"") as nickname, if(user_courier.avatar_url IS NOT NULL,user_courier.avatar_url,"") as avatar_url, if(order.courier_id!=0,user_courier.openid,"") as openid,
									  if(user_courier.mobile_no IS NOT NULL,user_courier.mobile_no,"") as phone, order.destination, order.description,
									  order.fee, order.alt_phone as alt_phone,CASE order.status WHEN "new" THEN 1 WHEN "accepted" THEN 2 WHEN "cancelling" THEN 3 WHEN "finish" THEN 4 WHEN "completed" THEN 5 WHEN "cancelled" THEN 6 END as order_status_num, order.status,order.created_at'))
                    ->join('user as user_owner', 'order.owner_id', '=', 'user_owner.uid')
                    ->leftJoin('user as user_courier', 'order.courier_id', '=', 'user_courier.uid')
                    ->where('order.owner_id', $uid)
                    ->where('order.status','<>','waitpay')
                    ->orderBy('order_status_num','ASC')
                    ->orderBy('order.created_at', 'desc')
                    ->skip(10 * $page - 10)
                    ->take(10)
                    ->get()->toArray();

		foreach($orders as $k => $order){
			$orderHistory = OrderHistory::select(DB::raw('created_at as status_change_at,new_status'))
									->where('oid',$order['oid'])
									->get();
			$or[$k]['new_time'] = $order['created_at'];
			$or[$k]['accepted_time'] = "";
			$or[$k]['finish_time'] = "";
			$or[$k]['completed_time'] = "";
			$or[$k]['cancelled_time'] = "";
			foreach($orderHistory as $kk=>$v){
				if($v['new_status']=='new'){
					$or[$k]['new_time'] = $v['status_change_at'];
				}
				if($v['new_status']=='accepted'){
					$or[$k]['accepted_time'] = $v['status_change_at'];
				}
				if($v['new_status']=='finish'){
					$or[$k]['finish_time'] = $v['status_change_at'];
				}
				if($v['new_status']=='completed'){
					$or[$k]['completed_time'] = $v['status_change_at'];
				}
				if($v['new_status']=='cancelled'){
					$or[$k]['cancelled_time'] = $v['status_change_at'];
				}
			}
			if(empty($orderHistory)){
				$orderAll[$k] = $order;
			}else{
				$order_time[$k] = ["time" => $or[$k]];
				$orderAll[$k] = $order+$order_time[$k];
			}
		}
		if(empty($orders)){
			return $orders;
		}else{
			return $orderAll;
		}
	}

	/**
	 * 获取我的工作列表
	 */
	public function getMyWork($uid, $page)
	{
		$orders = Order::select(DB::raw('order.oid, if(user_owner.nickname IS NOT NULL,user_owner.nickname,"") as nickname, if(user_owner.avatar_url IS NOT NULL,user_owner.avatar_url,"") as avatar_url, user_owner.openid as openid,
									  if(user_owner.mobile_no IS NOT NULL,user_owner.mobile_no,"") as phone, order.alt_phone as alt_phone, order.destination, order.description,
									  order.fee, CASE order.status WHEN "new" THEN 1 WHEN "accepted" THEN 2 WHEN "cancelling" THEN 3 WHEN "finish" THEN 4 WHEN "completed" THEN 5 WHEN "cancelled" THEN 6 END as order_status_num, order.status,order.created_at'))
                    ->join('user as user_courier', 'order.courier_id', '=', 'user_courier.uid')
                    ->leftJoin('user as user_owner', 'order.owner_id', '=', 'user_owner.uid')
					->leftJoin('order_status_history', 'order.oid', '=', 'order_status_history.oid')
                    ->where('order.courier_id', $uid)
                    ->where('order.status','<>','waitpay')
                    ->orderBy('order_status_num','ASC')
                    ->orderBy('order.created_at', 'desc')
					->groupBy('order_status_history.oid')
                    ->skip(10 * $page - 10)
                    ->take(10)
                    ->get()->toArray();

		foreach($orders as $k => $order){
			$orderHistory = OrderHistory::select(DB::raw('created_at as status_change_at,new_status'))
								->where('oid',$order['oid'])->get();

			$or[$k]['new_time'] = "2016-10-01 09:40:44";
			$or[$k]['accepted_time'] = "";
			$or[$k]['finish_time'] = "";
			$or[$k]['completed_time'] = "";
			$or[$k]['cancelled_time'] = "";

			foreach($orderHistory as $kk=>$v){
				if($v['new_status']=='new'){
					$or[$k]['new_time'] = $v['status_change_at'];
				}
				if($v['new_status']=='accepted'){
					$or[$k]['accepted_time'] = $v['status_change_at'];
				}
				if($v['new_status']=='finish'){
					$or[$k]['finish_time'] = $v['status_change_at'];
				}
				if($v['new_status']=='completed'){
					$or[$k]['completed_time'] = $v['status_change_at'];
				}
				if($v['new_status']=='cancelled'){
					$or[$k]['cancelled_time'] = $v['status_change_at'];
				}
			}
			if(empty($orderHistory)){
				$orderAll[$k] = $order;
			}else{
				$order_time[$k] = ["time" => $or[$k]];
				$orderAll[$k] = $order+$order_time[$k];
			}
		}
		if(empty($orders)){
			return $orders;
		}else{
			return $orderAll;
		}
	}

    /**
     * 更新任务状态
     */
    public function updateOrderStatus(array $param)
    {
        config(['database.default' => 'write']);
    	$update = Order::where('oid', $param['order_id'])
				    ->where(function ($query) use ($param) {
				    		switch ($param['status']) {
				    			case 'finish':
				    				$query->where('courier_id', $param['uid']);
				    				break;

				    			case 'completed':
				    				$query->where('owner_id', $param['uid']);
				    				break;

				    			default:
				    				return false;
				    				break;
				    		}
				    	})
				    ->where(function ($query) use ($param) {
				    		if (isset($param['only_in_status'])) {
			    				$query->whereIn('status', $param['only_in_status']);
				    		} else {
				    			return 1;
				    		}
				    	});

		switch ($param['status']) {
			case 'new':
				return $update->update(['status' => $param['status'],'courier_id' => 0]);
				break;
			default:
				return $update->update(['status' => $param['status']]);
				break;
		}


    }

	public function updateOrderStatusNew ($out_trade_no)
	{
		return Order::where('order_sn', $out_trade_no)->where('status','waitpay')->update(['status' => 'new']);
	}
    /**
     * (搁置)发单人获取接单人列表
     */
    public function getCourierList()
    {
        # code...
    }
    public function dropWaitPayOrder ($uid)
    {
    	Order::where('status','waitpay')->where('owner_id',$uid)->delete();
    }
	public function remindOrder($uid){
		$order_accepted = Order::select(DB::raw('oid'))
						->where('courier_id',$uid)
						->where('status','accepted')
						->first();
		$order_send = Order::select(DB::raw('oid'))
						->where('owner_id',$uid)
						->where('status','finish')
						->first();
		if(!empty($order_accepted) || !empty($order_send)){
			return 200;
		}else{
			return;
		}
	}
	public function getRecommendOrders ($number)
	{
		return Order::join('user', 'order.owner_id', '=', 'user.uid')
                    ->select(DB::raw('order.oid, user.openid, user.nickname, user.avatar_url, order.destination, order.description, order.fee, order.created_at,order.status as status,order.is_recommend'))
                    ->whereIn('order.status', ['new'])
                   	->orderBy('is_recommend','DESC')
                   	->orderBy('fee','DESC')
                    ->orderBy('order.created_at', 'desc')
                    ->take($number)
                    ->get();
	}
	public function getOrderCount ($where)
	{
		return Order::where($where)->count();
	}
	public function delete($where)
	{
		return Order::where($where)->delete();
	}
}
