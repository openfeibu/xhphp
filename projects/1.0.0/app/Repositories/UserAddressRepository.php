<?php

namespace App\Repositories;

use DB;
use Session;
use App\User;
use App\UserAddress;
use Illuminate\Http\Request;

class UserAddressRepository
{
	protected $request;
	
	function __construct(Request $request )
	{
		$this->request = $request;
	}
	public function store($userAddress)
	{
		try {
			return UserAddress::create($userAddress);
        } catch (Exception $e) {
        	throw new \App\Exceptions\Custom\RequestFailedException('无法创建收货地址');
        }
	}
	public function update ($where = array(),$update = array())
	{
		return UserAddress::where($where)->update($update);
	}
	public function getUserAddress($where)
	{
		return UserAddress::where($where)->orderBy('is_default','desc')->first();
	}
	public function getUserAddresses($where)
	{
		return UserAddress::where($where)->orderBy('address_id','desc')->get();
	}
	public function destroy($where)
	{
		return UserAddress::where($where)->delete();
	}
}