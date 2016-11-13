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
	public function store (array $userAddress)
	{
		$this->userAddressRepository->store($userAddress);
	}
}	
