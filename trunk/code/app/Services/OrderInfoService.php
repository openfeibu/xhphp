<?php

namespace App\Services;

use Log;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Repositories\OrderInfoRepository;

class OrderInfoService
{
	protected $request;

    protected $orderInfoRepository;

    protected $userRepository;

	function __construct(Request $request,
						 OrderInfoRepository $orderInfoRepository,
						 UserRepository $userRepository)
	{
		$this->request = $request;
        $this->orderInfoRepository = $orderInfoRepository;
        $this->userRepository = $userRepository;
	}
	
}