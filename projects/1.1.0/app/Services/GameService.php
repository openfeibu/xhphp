<?php

namespace App\Services;

use Session;
use Illuminate\Http\Request;
use App\Game;
use App\Services\HelpService;
use App\Repositories\UserRepository;
use App\Repositories\GameRepository;
use App\Repositories\MessageRepository;
use App\Services\MessageService;
use App\Services\PushService;
use App\Services\TradeAccountService;
use App\Services\WalletService;
use DB;

class GameService
{
	protected $orderService;

    protected $messageRepository;

    protected $userRepository;
	
	protected $messageService;

	protected $gameRepository;

	protected $pushService;

	function __construct( GameRepository $gameRepository,
						 WalletService $walletService,
                         TradeAccountService $tradeAccountService,
                         HelpService $helpService,
                         OrderService $orderService,
						 MessageService $messageService,
						 PushService $pushService)
	{
		$this->orderService = $orderService;
        $this->gameRepository = $gameRepository;
		$this->messageService = $messageService;
		$this->helpService = $helpService;
		$this->walletService = $walletService;
		$this->pushService = $pushService;
        $this->tradeAccountService = $tradeAccountService;
	}
	public function freeOrder ($user,$order)
	{
		$game = $this->checkStatus('free_order');
		$order_count =  $this->orderService->getOrderCount(['owner_id' => $user->uid,'status'=>'completed']);
		if($game && $order_count == 1){
			$wallet = $user->wallet + 2 ;
			$this->walletService->updateWallet($user->uid,$wallet);
			$walletData = array(
				'uid' => $order->owner_id,
				'out_trade_no' => $order->order_sn,
				'wallet' => $wallet ,
				'fee'	=> 2,
				'service_fee' => 0,
				'pay_id' => 4,
				'wallet_type' => 1,
				'trade_type' => 'FreeOrder',
				'description' => '首单返现',
	        );
	        $this->walletService->store($walletData);
	        $trade_no = 'wallet'.$this->helpService->buildOrderSn('XH');
			$trade = array(
	    		'uid' => $order->owner_id,
				'out_trade_no' => $order->order_sn,
				'trade_no' => $trade_no,
				'fee' => 2,
				'service_fee' => 0,
				'pay_id' => 4,
				'wallet_type' => 1,
				'trade_status' => 'income',
				'from' => 'order',
				'trade_type' => 'FreeOrder',
				'description' => '首单返现' ,
			);
			$this->tradeAccountService->addThradeAccount($trade);
			$this->messageService->SystemMessage2SingleOne($user->uid, '您好，首单返现的2元金额已经返回您的钱包，请注意查收');
			$this->pushService->PushUserTokenDevice('首单返现', '您好，首单返现的2元金额已经返回您的钱包，请注意查收', $user->uid);
			Game::where('id',$game->id)->increment('count');
		}
	}
	public function checkStatus ($name)
	{
		$game = $this->gameRepository->getGame(['name' => $name]);
		$time = time();
		if($game->status == 1 && $time>strtotime($game->starttime) && $time<strtotime($game->endtime)){
			return $game;
		}
		return false;
	}
}