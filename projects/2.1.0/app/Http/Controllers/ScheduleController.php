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
    	$orders = Order::select('oid','courier_id','owner_id','order_sn','updated_at','total_fee','service_fee','fee','pay_id')->where('status','finish')->where('updated_at','<=',DB::raw('(select date_sub(now(), interval 24 HOUR))'))->where('order_id','=','0')->orderBy('oid','DESC')->get();
   // 	if($_SERVER['SERVER_NAME']!='127.0.0.1'){
			//return [
			//	'code' => '404',
			//];
	  //  }
	  	Log::debug('??????:'.$orders);

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

	        //?????????????????????
	        $this->messageService->SystemMessage2SingleOne($order->courier_id, '?????????????????????????????????????????????????????????????????????');

	        //??????????????????
			$data = [
				'refresh' => 1,
				'target' => '',
				'open' => 'mytask',
				'data' => [
					'id' => $order->oid,
					'title' => '????????????',
					'content' => '?????????????????????????????????????????????????????????????????????',
				],
			];
			$this->pushService->PushUserTokenDevice('????????????', '?????????????????????????????????????????????????????????????????????', $order->courier_id,2,$data);


			Log::debug('courier:'.$courier);
			Log::debug('owner:'.$owner);
	        //????????????(??????????????????)
        	Event::fire(new Integrals('????????????',$owner));
	        //????????????(??????????????????)
	        Event::fire(new Integrals('????????????', $courier));

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
				'description' => '?????????',
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
				'description' => '?????????' ,
			);

			$this->tradeAccountService->addThradeAccount($trade);
		}
		return [
			'code' => 200,
			'detail' => '????????????',
		];
    }
    public function autoCheckRealName ()
    {
	    set_time_limit(0);
    	$orders = $this->telecomService->getTelecomOrders();
    	Log::debug('????????????:'.$orders);
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
	/*  24????????????????????? */
	private function shipping()
	{
		$order_infos = OrderInfo::select(DB::raw('order_info.order_id,order_info.uid,order_info.total_fee,order_info.order_sn,shop.shop_id,shop.uid as shop_uid,shop.service_rate,shop.shop_type'))
                                ->Join('shop','shop.shop_id','=','order_info.shop_id')
								->where('shipping_status','<>',2)
                                ->where('notice',0)
								->where('shipping_time','<=',DB::raw('(select date_sub(now(), interval 24 HOUR))'))
								->get();
		$data = [
			'refresh' => 1,
			'target' => '',
			'open' => 'order_info',
			'data' => [
				'url' => '',
				'title' => '????????????',
				'content' => '??????????????????????????????24??????????????????????????????????????????????????????24???????????????????????????????????????????????????????????????',
			],
		];
        //var_dump($order_infos);exit;
		foreach($order_infos as $key => $order_info)
		{
            $task = $this->orderService->getOrder(['order_id' => $order_info->order_id],['*'],false);

            if($order_info->shop_type == 1 || ($task && $task->status == 'finish'))
            {
    			$data['data']['url'] = config('app.web_url').'/shop/shop-orderDetail.html?device=android&order_id='.$order_info->order_id;
                $this->messageService->SystemMessage2SingleOne($order_info->uid, $data['data']['content']);
    			$ret = $this->pushService->PushUserTokenDevice($data['data']['title'], $data['data']['content'], $order_info->uid,2,$data);
                $this->orderInfoService->updateOrderInfoById($order_info->order_id,['notice' => 1]);
            }
		}
	}
	/*  48????????????????????? */
	private function shipped()
	{
		$order_infos = OrderInfo::select(DB::raw('order_info.order_id,order_info.uid,order_info.total_fee,order_info.order_sn,shop.shop_id,shop.uid as shop_uid,shop.service_rate,shop.shop_type'))
								->Join('shop','shop.shop_id','=','order_info.shop_id')
								->Join('user','user.uid','=','shop.uid')
								->where('order_info.shipping_status','<>',2)
								->where('order_info.shipping_time','<=',DB::raw('(select date_sub(now(), interval 48 HOUR))'))
                                ->orderBy('order_info.order_id','desc')
								->get();

        foreach($order_infos as $key => $order_info)
		{
			$shop = (object)array();
			$shop->shop_id = $order_info->shop_id;
			$shop->uid = $order_info->shop_uid;
			$shop->service_rate = $order_info->service_rate;
            $shop->shop_type = $order_info->shop_type;
            if($shop->shop_type == 1)
            {
                $this->orderInfoService->confirm($order_info,$shop,$this->walletService,$this->tradeAccountService);
            }
			$task = $this->orderService->getOrder(['order_id' => $order_info->order_id],['*'],false);
			if($task && $task->status == 'finish') {
                $this->orderInfoService->confirm($order_info,$shop,$this->walletService,$this->tradeAccountService);
				$task->uid = $order_info->shop_uid;
				$this->orderService->autoConfirmFinishWork($task,$this->walletService,$this->tradeAccountService);
			}
			$data = [
				'refresh' => 1,
				'target' => '',
				'open' => 'order_info',
				'data' => [
					'url' => '',
					'title' => '????????????',
					'content' => '??????????????????????????????48????????????????????????????????????????????????????????????',
				],
			];
			$this->messageService->SystemMessage2SingleOne($order_info->uid, '??????????????????????????????48????????????????????????????????????????????????????????????');
			$ret = $this->pushService->PushUserTokenDevice($data['data']['title'], $data['data']['content'], $order_info->uid,2,$data);
		}
	}
}
