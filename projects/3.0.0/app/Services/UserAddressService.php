<?php
	
namespace App\Services;

use Validator;
use Illuminate\Http\Request;
use App\Repositories\UserAddressRepository;
use App\UserAddress;

class UserAddressService{
	
	protected $request;

	
	protected $userAddressRepository;

	function __construct(Request $request,
                         UserAddressRepository $userAddressRepository)
	{
        $this->request = $request;
		$this->userAddressRepository = $userAddressRepository;
	}
	public function store(array $userAddress)
	{
		if($userAddress['is_default'] == 1){
			$this->update(['uid' => $userAddress['uid']],['is_default' => 0]);
		}
		return $this->userAddressRepository->store($userAddress);
	}
	public function update($where,$update)
	{
		$this->userAddressRepository->update($where,$update);
	}
	public function getUserAddress($where = array())
	{
		$user_address = $this->userAddressRepository->getUserAddress($where);
		
		return $user_address;
	}
	public function getUserAddresses($where = array())
	{
		$user_address = $this->userAddressRepository->getUserAddresses($where);
		
		return $user_address;
	}
	public function destroy($where)
	{
		return $this->userAddressRepository->destroy($where);
	}
}	
