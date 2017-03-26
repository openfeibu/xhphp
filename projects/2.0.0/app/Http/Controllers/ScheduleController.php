<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Input;
use Log;
use Event;
use DB;
use App\Order;
use App\OrderInfo;
use App\TradeAccount;
use App\Http\Requests;
use App\Services\HelpService;
use App\Services\UserService;
use App\Services\OrderService;
use App\Services\GameService;
use App\Services\ShopService;
use App\Services\MessageService;
use App\Services\OrderInfoService;
use App\Services\TradeAccountService;
use App\Services\WalletService;
use App\Events\Integral\Integrals;
use App\Http\Controllers\Controller;
use App\Services\TelecomService;
use App\Services\PushService;

class ScheduleController extends Controller
{
    protected $orderService;

    protected $userService;

    protected $helpService;

    protected $messageService;

	protected $walletService;

    protected $tradeAccountService;

    protected $gameService;
	
	protected $orderInfoService;

    function __construct(OrderService $orderService,
                         UserService $userService,
                         HelpService $helpService,
                         MessageService $messageService,
                         WalletService $walletService,
                         TelecomService $telecomService,
                         TradeAccountService $tradeAccountService,
                         PushService $pushService,
                         GameService $gameService,
						 OrderInfoService $orderInfoService,
						 ShopService $shopService)
    {
        $this->orderService = $orderService;
        $this->userService = $userService;
        $this->helpService = $helpService;
        $this->walletService = $walletService;
        $this->tradeAccountService = $tradeAccountService;
        $this->telecomService = $telecomService;
        $this->messageService = $messageService;
        $this->pushService = $pushService;
        $this->gameService = $gameService;
		$this->shopService = $shopService;
		$this->orderInfoService = $orderInfoService;
    }
    public function  autoFinishWork()
    {
	    set_time_limit(0);
    	$orders = Order::select('oid','courier_id','owner_id','order_sn','updated_at','total_fee','service_fee','fee','pay_id')->where('status','finish')->where('updated_at','<=',DB::raw('(select date_sub(now(), interval 24 HOUR))'))->orderBy('oid','DESC')->get();
   // 	if($_SERVER['SERVER_NAME']!='127.0.0.1'){
			//return [
			//	'code' => '404',
			//];
	  //  }
	  	Log::debug('订单:'.$orders);

		foreach( $orders as $key => $order )
		{
			$param = [
				'uid' =>$order->owner_id,
	            'order_id' => $order->oid,
	            'status' => 'completed',
	        ];
	        $this->orderService->schedluUpdateOrderStatus($param);

	        $courier = $this->userService->getUserByUserID($order->courier_id);
	        $owner = $this->userService->getUserByUserID($order->owner_id);
	        
			$this->gameService->freeOrder($owner,$order);
			
	        //纸条通知接单人
	        $this->messageService->SystemMessage2SingleOne($order->courier_id, '您好，发单人已结算你完成的任务，赶紧去看看吧。');

	        //推送给接单人
			$data = [
				'refresh' => 1,
				'target' => '',
				'open' => 'mytask',
				'data' => [
					'id' => $order->oid,
					'title' => '校汇任务',
					'content' => '您好，发单人已结算你完成的任务，赶紧去看看吧。',
				],
			];
			$this->pushService->PushUserTokenDevice('校汇任务', '您好，发单人已结算你完成的任务，赶紧去看看吧。', $order->courier_id,2,$data);
			
	      
			Log::debug('courier:'.$courier);
			Log::debug('owner:'.$owner);
	        //积分更新(给发单人加分)
        	Event::fire(new Integrals('发布任务',$owner));
	        //积分更新(给接单人加分)
	        Event::fire(new Integrals('完成任务', $courier));

	        $wallet = $courier->wallet + $order->fee - $order->service_fee;
	        $fee = $order->fee - $order->service_fee;
			$this->walletService->updateWallet($courier->uid,$wallet);
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
	        $this->walletService->store($walletData);
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

			$this->tradeAccountService->addThradeAccount($trade);
		}
		return [
			'code' => 200,
			'detail' => '请求成功',
		];
    }
    public function autoCheckRealName ()
    {
	    set_time_limit(0);
    	$orders = $this->telecomService->getTelecomOrders();
    	Log::debug('电信订单:'.$orders);
    	foreach( $orders as $key => $order )
    	{
    		$fields = array(			
				'phone' => $order->telecom_phone,
				'iccid' => $order->telecom_iccid,
				'outOrderNumber' => $order->telecom_outOrderNumber,
	 		);
	 		$file_contents = $this->helpService->telecomCheckReal($fields);
	 		if($file_contents->resultCode == 'CONFIRM_SUCCESS'){
				$this->telecomService->updateTelecomOrdersById($order->id,['telecom_real_name_status'=>1]);
			}
			if($file_contents->resultCode == 'CONFIRM_WAITING'){
				$this->telecomService->updateTelecomOrdersById($order->id,['telecom_real_name_status'=>2]);
			}
			sleep(5);
    	}
    }
	public function auto()
	{
		$this->shipping();
		$this->shipped();
	}
	/*  24小时后通知收货 */
	private function shipping()
	{
		$order_infos = OrderInfo::select(DB::raw('order_info.order_id,order_info.uid'))
								->where('shipping_status',1)
								->where('shipping_time','<=',DB::raw('(select date_sub(now(), interval 24 HOUR))'))
								->get();
		$data = [
			'refresh' => 1,
			'target' => '',
			'open' => 'order_info',
			'data' => [
				'url' => '',
				'title' => '订单通知',
				'content' => '您的订单已经发货超过24小时，如果已收到货物请及时确认收货。24小时后将自动收货，如未收到货物请联系管理员',
			],
		];
		foreach($order_infos as $key => $order_info)
		{
			$data['data']['url'] = config('app.web_url').'/shop/shop-orderDetail.html?order_id='.$order_info->order_id;
			$ret = $this->pushService->PushUserTokenDevice($data['data']['title'], $data['data']['content'], $order_info->uid,2,$data);
		}
	}
	/*  48小时后自动收货 */
	private function shipped()
	{
		$order_infos = OrderInfo::select(DB::raw('order_info.order_id,order_info.uid,order_info.total_fee,order_info.order_sn,shop.shop_id,shop.uid as shop_uid,shop.service_rate'))
								->rightJoin('shop','shop.shop_id','=','order_info.shop_id')
								->rightJoin('user','user.uid','=','shop.uid')
								->where('order_info.shipping_status',1)
								->where('order_info.shipping_time','<=',DB::raw('(select date_sub(now(), interval 48 HOUR))'))
								->get();
		foreach($order_infos as $key => $order_info)
		{
			$shop = (object)array();
			$shop->shop_id = $order_info->shop_id;
			$shop->uid = $order_info->shop_uid;
			$shop->service_rate = $order_info->service_rate;
			$this->orderInfoService->confirm($order_info,$shop,$this->walletService,$this->tradeAccountService);
		}
	}
}

