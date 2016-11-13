<?php

namespace App\Repositories;

use DB;
use Session;
use App\User;
use App\TradeAccount;
use App\Services\OrderService;
use Illuminate\Http\Request;

class TradeAccountRepository
{
	protected $request;

	protected $orderService;
	
	function __construct(Request $request,OrderService $orderService )
	{
		$this->request = $request;
		$this->orderService = $orderService;
	}
	private function store (array $trade)
	{
		$tradeAccount = new TradeAccount;
		$tradeAccount->setConnection('write');
		$tradeAccount->uid = $trade['uid'];
		$tradeAccount->out_trade_no = $trade['out_trade_no'];                
		$tradeAccount->trade_no = $trade['trade_no'];
		$tradeAccount->description = $trade['description'];
		$tradeAccount->from = $trade['from'];
		$tradeAccount->trade_status = $trade['trade_status'];
		$tradeAccount->trade_type = $trade['trade_type'];
		$tradeAccount->fee = $trade['fee'];
		$tradeAccount->service_fee = $trade['service_fee'];
		$tradeAccount->pay_id = $trade['pay_id'];
		$tradeAccount->wallet_type = $trade['wallet_type'];
		$tradeAccount->created_at = date('Y-m-d H:i:s');
		$tradeAccount->save();
	}
	public function addThradeAccount($trade)
	{	
		if(!$this->getThreadAccountByTradeNo($trade['trade_no'])){		
			$this->store($trade);					
		}
	}
	public function updateTradeAccount ($out_trade_no,$tradeData)
	{
		TradeAccount::where('out_trade_no',$out_trade_no)->update($tradeData);
	}
	public function getThreadAccountByTradeNo($trade_no)
	{		
		return TradeAccount::where('trade_no',$trade_no)->first();
	}
}