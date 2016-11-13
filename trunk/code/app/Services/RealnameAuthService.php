<?php

namespace App\Services;

use Log;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Repositories\RealnameAuthRepository;

class RealnameAuthService
{

	protected $request;

	protected $realnameAuthRepository;

	protected $userRepository;

	function __construct(Request $request,
						 RealnameAuthRepository $realnameAuthRepository,
						 UserRepository $userRepository)
	{
		$this->request = $request;
		$this->realnameAuthRepository = $realnameAuthRepository;
		$this->userRepository = $userRepository;
	}

	/**
	 * 保存身份证照片凭证到数据库
	 */
	public function saveVoucher(array $param)
	{
		$param['uid'] = $this->userRepository->getUser()->uid;
		$this->realnameAuthRepository->saveVoucher($param);
	}

}