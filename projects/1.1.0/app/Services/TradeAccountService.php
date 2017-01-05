<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Repositories\TradeAccountRepository;
use App\TradeAccount;

class TradeAccountService
{
	protected $request;

	protected $tradeAccountRepository;

	function __construct(Request $request,
                         TradeAccountRepository $tradeAccountRepository)
	{
        $this->request = $request;
		$this->tradeAccountRepository = $tradeAccountRepository;
	}
	public function store (array $trade)
	{
		$this->tradeAccountRepository->store($trade);
	}
	public function addThradeAccount($trade)
	{
		$this->tradeAccountRepository->addThradeAccount($trade);
	}
	public function updateTradeAccount ($out_trade_no,$tradeData)
	{
		$this->tradeAccountRepository->updateTradeAccount($out_trade_no,$tradeData);
	}
	public function getThreadAccountByTradeNo ($trade_no)
	{
		return $this->tradeAccountRepository->getThreadAccountByTradeNo($trade_no);
	}
}