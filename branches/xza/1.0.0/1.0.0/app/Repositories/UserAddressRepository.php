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
}