<?php

namespace App\Repositories;

use DB;
use Log;
use Session;
use App\User;
use App\WalletAccount;
use App\ApplyWallet;
use Illuminate\Http\Request;

class WalletRepository
{
	protected $request;

	function __construct(Request $request)
	{
		$this->request = $request;
	}
	public function updateWallet ($uid,$fee)
	{

		User::where('uid',$uid)->update(['wallet' => $fee]);

		return true;
	}
	public function store (array $walletData)
	{
		$walletAccount = new WalletAccount;
		$walletAccount->setConnection('write');
		$walletAccount->uid = $walletData['uid'];
		$walletAccount->out_trade_no = $walletData['out_trade_no'];
		$walletAccount->wallet_type = $walletData['wallet_type'];
		$walletAccount->fee = $walletData['fee'];		
		$walletAccount->service_fee = $walletData['service_fee'];	
		$walletAccount->wallet = $walletData['wallet'];
		$walletAccount->trade_type = $walletData['trade_type'];
		$walletAccount->description = $walletData['description'];
		$walletAccount->created_at = date('Y-m-d H:i:s');
		$walletAccount->save();
	}
	/*Ç®°üÃ÷Ï¸*/
	public function getWalletAccount ($uid)
	{
		return WalletAccount::select(DB::raw('uid,description,trade_type,wallet_type,fee,service_fee,wallet,updated_at,out_trade_no'))
					->where('uid',$uid)
					->skip(20 * $this->request->page - 20)
					->orderBy('updated_at','desc')
					->take(20)
                    ->get()->toArray();
	}
	public function storeApply ($applyData)
	{
		$applyWallet = new ApplyWallet;
		$applyWallet->setConnection('write');
		$applyWallet->uid = $applyData['uid'];
		$applyWallet->out_trade_no = $applyData['out_trade_no'];
		$applyWallet->fee = $applyData['fee'];		 
		$applyWallet->service_fee = $applyData['service_fee'];	
		$applyWallet->total_fee = $applyData['total_fee'];	
		$applyWallet->description = $applyData['description'];
		$applyWallet->alipay = $applyData['alipay'];
		$applyWallet->alipay_name = $applyData['alipay_name'];
		$applyWallet->status = $applyData['status'];
		$applyWallet->created_at = date('Y-m-d H:i:s');
		$applyWallet->save();
	}
	public function updateWalletAccout ($out_trade_no,$walletData)
	{
		WalletAccount::where('out_trade_no',$out_trade_no)->update($walletData);
	}
}