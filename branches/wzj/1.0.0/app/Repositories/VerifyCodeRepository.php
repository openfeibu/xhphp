<?php

namespace App\Repositories;

use DB;
use App\VerifyCode;
use Illuminate\Http\Request;

class VerifyCodeRepository
{
	protected static $verifyCode;

	protected $request;

	function __construct(Request $request)
	{
		$this->request = $request;
	}

	/**
	 * //将手机及其对应的短信验证码保存到数据库
	 */
	public function saveVerifyCode($mobile_no, $random, $usage, $result)
	{
		$verifyCode = new VerifyCode;
        $verifyCode->setConnection('write');
        $verifyCode->mobile_no = $mobile_no;
        $verifyCode->usage = $usage;
        $verifyCode->verify_code = $random;
        $verifyCode->is_send = $result ? 1 : 0;
        $verifyCode->save();
	}

	/**
	 * 检查手机号码跟短信验证码是否一致
	 */
	public function checkSMS($phone, $sms, $usage)
	{
		$verifyCode = VerifyCode::where('mobile_no', $phone)
		                        ->where('verify_code', $sms)
		                        ->where('usage', $usage)
		                        ->where('is_used', 0)
		                        ->where('created_at', '>=', DB::raw('(select date_sub(now(), interval 30 MINUTE))'))
		                        ->orderBy('created_at', 'desc')
		                        ->first();
		if (!$verifyCode) {
			throw new \App\Exceptions\Custom\CaptchaSMSIncorrectException();
		}
		self::$verifyCode = $verifyCode;
 		return self::$verifyCode;
	}

	public function changSMSUsed()
	{
		self::$verifyCode->setConnection('write');
		self::$verifyCode->is_used = 1;
		self::$verifyCode->save();
	}
}