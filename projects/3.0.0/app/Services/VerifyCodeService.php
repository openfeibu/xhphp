<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Repositories\VerifyCodeRepository;

class VerifyCodeService
{

	protected $request;

	protected $verifyCodeRepository;

	function __construct(Request $request,
						 VerifyCodeRepository $verifyCodeRepository)
	{
		$this->request = $request;
		$this->verifyCodeRepository = $verifyCodeRepository;
	}



	/**
	 * 检查手机号码跟短信验证码是否一致
	 */
	public function checkSMS($phone, $sms, $usage)
	{
		//频率限制


		$verifyCode = $this->verifyCodeRepository->checkSMS($phone, $sms, $usage);
		if (!$verifyCode) {
			throw new \App\Exceptions\Custom\CaptchaSMSIncorrectException();
		}
		$this->verifyCodeRepository->changSMSUsed();
	}

}
