<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Repositories\WalletRepository;

class WalletService
{
	protected $request;

	protected $userRepository;

	protected $walletRepository;

	protected $helpService;

	function __construct(Request $request,
                         WalletRepository $walletRepository)
	{
        $this->request = $request;
		$this->walletRepository = $walletRepository;
	}
	public function store ($walletData)
	{
		$this->walletRepository->store($walletData);
	}
	public function updateWallet($uid,$fee)
	{
		return $this->walletRepository->updateWallet($uid,$fee);
	}
	public function getWalletAccount ($uid)
	{
		$walletAccount = $this->walletRepository->getWalletAccount($uid);
		$walletAccountArr = array();
		foreach( $walletAccount as $key => $value )
		{
			$walletAccountArr[$key] = array(
				'uid' => $value['uid'],
				//'description' => $value->description,
				'trade_type' => trans("common.trade_type.$value[trade_type]"),
				'wallet_type' => $value['wallet_type'],
				'wallet_type_trans' => 	trans("common.wallet_type.$value[wallet_type]"),
				'fee' => $value['wallet_type'] == 1 ? '+'.$value['fee'] : '-'.$value['fee'],
				'service_fee' =>$value['service_fee'],
				'wallet' => $value['wallet'],
				'out_trade_no' => $value['out_trade_no'],
				'time' => $value['updated_at'],
			);
		}

		return $walletAccountArr;
	}
	public function storeApply ($applyData)
	{
		$this->walletRepository->storeApply($applyData);
	}
	public function updateWalletAccout ($out_trade_no,$walletData)
	{
		$this->walletRepository->updateWalletAccout($out_trade_no,$walletData);
	}
}