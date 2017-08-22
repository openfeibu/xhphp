<?php

namespace App\Services;

use Log;
use Illuminate\Http\Request;
use App\Services\HelpService;
use App\Repositories\UserRepository;
use App\Repositories\ShopRepository;
use App\Repositories\GoodsRepository;
use App\Repositories\OrderInfoRepository;
use App\Repositories\OrderRepository;
use App\Repositories\CouponRepository;

class OrderInfoService
{
	protected $request;

    protected $orderInfoRepository;

    protected $userRepository;

	function __construct(Request $request,
						 HelpService $helpService,
						 GoodsRepository $goodsRepository,
						 ShopRepository $shopRepository,
						 OrderInfoRepository $orderInfoRepository,
						 UserRepository $userRepository,
						 OrderRepository $orderRepository,
						 CouponRepository $couponRepository)
	{
		$this->request = $request;
		$this->helpService = $helpService;
        $this->orderInfoRepository = $orderInfoRepository;
        $this->userRepository = $userRepository;
        $this->goodsRepository = $goodsRepository;
        $this->shopRepository = $shopRepository;
		$this->couponRepository = $couponRepository;
		$this->orderRepository = $orderRepository;
	}
	public function create($order_info)
	{
		$order_info['postscript'] = $order_info['postscript'] ? $order_info['postscript'] : '';
		return $this->orderInfoRepository->create($order_info);
	}
	public function updateOrderInfo($order_sn,$update = [])
	{
		return $this->orderInfoRepository->updateOrderInfo($order_sn,$update);
	}
	public function updateOrderInfoById ($order_id,$update = [])
	{
		return $this->orderInfoRepository->updateOrderInfoById($order_id,$update);
	}
	public function getOrderInfos($uid = '',$type)
	{
		$where = ['order_info.uid' => $uid];

		$order_infos = $this->orderInfoRepository->getOrderInfos($where,$type);

		foreach( $order_infos as $key => $order_info )
		{
			$order_goodses =  $this->getOrderGoodses($order_info->order_id);
			$goods_number = 0;
			foreach( $order_goodses as $k => $order_goods )
			{
				$goods_number = $goods_number + $order_goods->goods_number;
				$order_goods->total_fee = $order_goods->goods_price * $order_goods->goods_number;
				$goods = $this->goodsRepository->getGoods(['goods_id' => $order_goods->goods_id],['goods_img','goods_thumb']);
				if(!$goods){
					$order_goods->goods_img = $order_goods->goods_thumb = config('common.no_goods_img');
				}else{
					$order_goods->goods_img = $goods->goods_img;
					$order_goods->goods_thumb = $goods->goods_thumb;
				}
			}
			$order_info->order_goodses = $order_goodses;
			$order_info->goods_number = $goods_number;
			$order_info->status_desc = trans('common.pay_status.'.$order_info->pay_status);
			if($order_info->pay_status == 1){
				$order_info->status_desc = trans('common.order_status.buyer.'.$order_info->order_status);
				if($order_info->order_status == 1)
				{
					$order_info->status_desc = trans('common.shipping_status.'.$order_info->shipping_status);
				}
			}
			$result = check_refund_order_info($order_info->pay_status,$order_info->shipping_status,$order_info->order_status);
			$order_info->can_cancel = 1;
			if(!$result){
				$order_info->can_cancel = 0;
			}
			$task = $this->orderRepository->getSingleOrderByCoutoms(['order.order_id' => $order_info->order_id]);

			$order_info->task = $task;
			$coupon = [];
			if($order_info->user_coupon_id)
			{
				$coupon = $this->couponRepository->getUserCoupon(['user_coupon_id' => $order_info->user_coupon_id]);
			}
			//$order_info->coupon = $coupon;
			$order_info->coupon_price = $coupon ? $coupon->price : 0;
		}
		return $order_infos;
	}
	public function getShopOrderInfos ($shop_id,$type,$num = 20)
	{
		$where = ['order_info.shop_id' => $shop_id];

		$order_infos = $this->orderInfoRepository->getOrderInfos($where,$type,$num);

		foreach( $order_infos as $key => $order_info )
		{
			$order_goodses =  $this->getOrderGoodses($order_info->order_id);
			foreach( $order_goodses as $key => $order_goods )
			{
				$order_goods->total_fee = $order_goods->goods_price * $order_goods->goods_number;
				$goods = $this->goodsRepository->getGoods(['goods_id' => $order_goods->goods_id],['goods_img','goods_thumb']);
				if(!$goods){
					$order_goods->goods_img = $order_goods->goods_thumb = config('common.no_goods_img');
				}else{
					$order_goods->goods_img = $goods->goods_img;
					$order_goods->goods_thumb = $goods->goods_thumb;
				}
			}
			$order_info->order_goodses = $order_goodses;

			$order_info->status_desc = trans('common.pay_status.'.$order_info->pay_status);
			if($order_info->pay_status == 1){
				$order_info->status_desc = trans('common.order_status.seller.'.$order_info->order_status);
				if($order_info->order_status == 1)
				{
					$order_info->status_desc = trans('common.shipping_status.'.$order_info->shipping_status);
				}
			}
			$task = $this->orderRepository->getSingleOrderByCoutoms(['order.order_id' => $order_info->order_id]);

			$order_info->task = $task;
			$coupon = [];
			if($order_info->user_coupon_id)
			{
				$coupon = $this->couponRepository->getUserCoupon(['user_coupon_id' => $order_info->user_coupon_id]);
			}
			//$order_info->coupon = $coupon;
			$order_info->coupon_price = $coupon ? $coupon->price : 0;
		}
		return $order_infos;
	}
	public function getOrderInfo($order_id,$type = 'buyer')
	{
		$order_info = $this->orderInfoRepository->getOrderInfo($order_id);
		$order_info->status_desc = trans('common.pay_status.'.$order_info->pay_status);
		if($order_info->pay_status == 1){
			$order_info->status_desc = trans('common.order_status.'.$type.'.'.$order_info->order_status);
			if($order_info->order_status == 1)
			{
				$order_info->status_desc = trans('common.shipping_status.'.$order_info->shipping_status);
			}
		}
		$order_goodses =  $this->getOrderGoodses($order_id);
		foreach( $order_goodses as $key => $order_goods )
		{
			$order_goods->total_fee = $order_goods->goods_price * $order_goods->goods_number;
			$goods = $this->goodsRepository->getGoods(['goods_id' => $order_goods->goods_id],['goods_img','goods_thumb']);
			if(!$goods){
				$order_goods->goods_img = $order_goods->goods_thumb = config('common.no_goods_img');
			}else{
				$order_goods->goods_img = $goods->goods_img;
				$order_goods->goods_thumb = $goods->goods_thumb;
			}
		}
		$order_info->order_goodses = $order_goodses;
		$order_info->coupon = [];
		if($order_info->user_coupon_id)
		{
			$coupon = $this->couponRepository->getUserCoupon(['user_coupon_id' => $order_info->user_coupon_id]);
			$order_info->coupon = $coupon;
		}
		$shop = $this->shopRepository->getShop(['shop_id' => $order_info->shop_id],['*']);
		$order_info->shop = $shop;

		$task = $this->orderRepository->getSingleOrderByCoutoms(['order.order_id' => $order_info->order_id]);

		$order_info->task = $task;
		$coupon = [];
		if($order_info->user_coupon_id)
		{
			$coupon = $this->couponRepository->getUserCoupon(['user_coupon_id' => $order_info->user_coupon_id]);
		}
		//$order_info->coupon = $coupon;
		$order_info->coupon_price = $coupon ? $coupon->price : 0;
		return $order_info;
	}

	public function getOrderGoodses ($order_id,$columns = ['*'])
	{
		return $this->orderInfoRepository->getOrderGoodses($order_id,$columns);
	}
	public function getOrderInfoGoodses ($where,$columns = ['*'])
	{
		return $this->orderInfoRepository->getOrderInfoGoodses($where,$columns);
	}
	public function isExistsOrderInfo ($where,$columns = ['*'])
	{
		$order_info = $this->orderInfoRepository->isExistsOrderInfo($where,$columns = ['*']);
		if(!$order_info){
			throw new \App\Exceptions\Custom\OutputServerMessageException('订单不存在');
		}
		return $order_info;
	}
	public function checkPay ($order_id,$uid)
	{
		$order_info = $this->isExistsOrderInfo(['order_id' => $order_id,'uid' => $uid]);
		if($order_info->pay_status !=0){
			throw new \App\Exceptions\Custom\OutputServerMessageException('不能重复支付');
		}
		return $order_info;
	}
	public function checkCancel ($order_id,$uid)
	{
		$order_info = $this->isExistsOrderInfo(['order_id' => $order_id,'uid' => $uid]);
		if($order_info->pay_status != 0){
			throw new \App\Exceptions\Custom\OutputServerMessageException('订单不能删除');
		}
		return $order_info;
	}
	public function checkRefund ($order_id,$uid)
	{
		$order_info = $this->isExistsOrderInfo(['order_id' => $order_id,'uid' => $uid]);
		$result = check_refund_order_info($order_info->pay_status,$order_info->shipping_status,$order_info->order_status);
		if(!$result){
			throw new \App\Exceptions\Custom\OutputServerMessageException('该订单状态不支持退款');
		}
		return $order_info;
	}
	public function checkConfirm ($order_id,$uid)
	{
		$order_info = $this->isExistsOrderInfo(['order_id' => $order_id,'uid' => $uid]);
		$result = check_confirm_order_info($order_info->pay_status,$order_info->shipping_status,$order_info->order_status);
		if(!$result){
			throw new \App\Exceptions\Custom\OutputServerMessageException('该订单状态不支持确认收货');
		}

		$order = $this->orderRepository->getOrderColumn(['order_id' => $order_id],['oid','status']);

		if($order && $order->status != 'finish')
		{
			throw new \App\Exceptions\Custom\OutputServerMessageException('该订单状态不支持确认收货');
		}
		return $order_info;
	}
	public function sellerCheckRefund ($order_id,$shop_id)
	{
		$order_info = $this->isExistsOrderInfo(['order_id' => $order_id,'shop_id' => $shop_id]);
		$result = seller_check_refund_order_info($order_info->pay_status,$order_info->shipping_status,$order_info->order_status);
		if(!$result){
			throw new \App\Exceptions\Custom\OutputServerMessageException('该订单状态不支持退款');
		}
		return $order_info;
	}
	public function sellerCheckShipping ($order_id,$shop_id)
	{
		$order_info = $this->isExistsOrderInfo(['order_id' => $order_id,'shop_id' => $shop_id]);
		$result = seller_check_Shipping_order_info($order_info->pay_status,$order_info->shipping_status,$order_info->order_status);
		if(!$result){
			throw new \App\Exceptions\Custom\OutputServerMessageException('该订单状态不支持发货');
		}
		return $order_info;
	}

	public function confirm ($order_info,$shop,$walletService,$tradeAccountService)
	{
        $shop_user = $this->userRepository->getUserByUserID($shop->uid);

		if($shop->type == 1 || $shop->type == 2)
		{
			//应得款
			$receivable = get_receivable($shop->shop_type,$order_info);
			//服务费
			$service_fee = $this->helpService->shopServiceFee($receivable,$shop->service_rate) ;

			$fee = $receivable - $service_fee;

	        $wallet = $shop_user->wallet + $fee;
	        //商家加物品费用
			$walletData = array(
				'uid' => $shop->uid,
				'out_trade_no' => $order_info->order_sn,
				'wallet' => $wallet,
				'fee'	=> $fee,
				'service_fee' => $service_fee,
				'pay_id' => 5,
				'wallet_type' => 1,
				'trade_type' => 'Shop',
				'description' => '商店收入',
	        );
	        $walletService->store($walletData);
	        $trade_no = 'wallet'.$this->helpService->buildOrderSn('XH');
			$trade = array(
	    		'uid' => $shop->uid,
				'out_trade_no' => $order_info->order_sn,
				'trade_no' => $trade_no,
				'fee' => $fee,
				'service_fee' => $service_fee,
				'pay_id' => 5,
				'wallet_type' => 1,
				'trade_status' => 'income',
				'from' => 'shop',
				'trade_type' => 'Shop',
				'description' => '商店收入' ,
			);
			$tradeAccountService->addThradeAccount($trade);
			$walletService->updateWallet($shop_user->uid,$wallet);
			$this->shopRepository->inIncome(['shop_id' => $shop->shop_id],$fee);
		}

		$this->updateOrderInfoById($order_info->order_id,['order_status' => 2,'shipping_status' => 2,'succ_time' => dtime()]);
		$goodses = $this->getOrderGoodses($order_info->order_id,['goods_id','goods_number']);
		foreach( $goodses as $key => $goods )
		{
			$this->goodsRepository->inGoodsSale(['goods_id' => $goods->goods_id],$goods->goods_number);
			$this->shopRepository->inSale(['shop_id' => $shop->shop_id],$goods->goods_number);
		}

		return true;
	}
	public function destroy ($where)
	{
		return $this->orderInfoRepository->destroy($where);
	}
	public function deGoodsNumber ($order_id)
	{
		$goodses = $this->getOrderGoodses($order_id,['goods_id','goods_number']);
		foreach( $goodses as $key => $goods )
		{
			$this->goodsRepository->deGoodsNumber(['goods_id' => $goods->goods_id],$goods->goods_number);
		}
		return true;
	}
	public function InGoodsNumber ($order_id)
	{
		$goodses = $this->getOrderGoodses($order_id,['goods_id','goods_number']);
		foreach( $goodses as $key => $goods )
		{
			$this->goodsRepository->InGoodsNumber(['goods_id' => $goods->goods_id],$goods->goods_number);
		}
		return true;
	}
	public function getAllOrderInfos($where)
	{
		return $this->orderInfoRepository->getAllOrderInfos($where);
	}
	public function getPickCode()
	{
		$pick_code = get_pick_code();
		$order_info = $this->orderInfoRepository->isExistsOrderInfo(['pick_code' => $pick_code,'shipping_status' => 1],['order_id']);
		if($order_info)
		{
			return $this->getPickCode();
		}
		return $pick_code;
	}
	public function getOrderInfoCustom($where,$customs = ['*']){
		return $this->orderInfoRepository->isExistsOrderInfo($where,$customs);
	}
	public function uploadPickCode($where,$old_pick_code = '')
	{
		$pick_code = get_pick_code();
		if(!$old_pick_code)
		{
			$order_info = $this->orderInfoRepository->isExistsOrderInfo($where,['pick_code']);
			$old_pick_code = $order_info->pick_code;
		}
		if($old_pick_code == $pick_code)
		{
			return $this->uploadPickCode($where,$old_pick_code);
		}

		$this->orderInfoRepository->updateOrderInfoByWhere($where,['pick_code' => $pick_code]);
	}
	public function createOrder($order_info,$shop,$user)
	{
		$order_sn = $this->helpService->buildOrderSn('RT');
		if($shop->shop_type == 2)
		{
			$goods_fee = 0;
			//提货码
			$pick_code = $this->getPickCode();
			$this->updateOrderInfo($order_info->order_sn,['pick_code' => $pick_code]);
			$type = 'business';
		}else if($shop->shop_type == 3){
			$goods_fee = $order_info->goods_amount;
			$type = 'canteer';
		}
		$shipping_fee =  $order_info->shipping_fee + $order_info->seller_shipping_fee;
		$total_fee = $order_info->shipping_fee + $order_info->seller_shipping_fee + $goods_fee ;
		$service_fee = $this->helpService->serviceFee($total_fee) ;
		return [
			'destination' => $order_info->address,
			'description' => $shop->college_name.' '.$shop->shop_name.' '.$order_info->description,
			'fee' => $shipping_fee,
			'goods_fee' => $goods_fee ,
			'total_fee' => $total_fee,
			'service_fee' => $service_fee,
			'phone' => $order_info->mobile ? $order_info->mobile : $user->mobile_no,
			'order_sn' => $order_sn,
			'status' => 'new',
			'pay_id' => $order_info->pay_id,
			'type' => $type,
			'order_id' => $order_info->order_id,
			'uid' => $user->uid
		];
	}
}
