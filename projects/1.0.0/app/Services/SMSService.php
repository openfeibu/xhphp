<?php

namespace App\Services;

use Log;
use Session;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Repositories\VerifyCodeRepository;
use App\Helper\alidayu\top\TopClient as TopClient;
use App\Helper\alidayu\top\request\AlibabaAliqinFcSmsNumSendRequest as AlibabaAliqinFcSmsNumSendRequest;

class SMSService
{

	protected $request;

	protected $verifyCodeRepository;

	protected $userRepository;

	function __construct(Request $request,
						 VerifyCodeRepository $verifyCodeRepository,
						 UserRepository $userRepository)
	{
		$this->request = $request;
		$this->verifyCodeRepository = $verifyCodeRepository;
		$this->userRepository = $userRepository;
	}

	/**
	 * 发送短信验证码到手机号码
	 */
	public function sendSMS2Phone($mobile_no, $usage)
	{
        //todo 频率限制
		if (Session::get('SMSLimit') and (time() - Session::get('SMSLimit')) <= 180) {
			throw new \App\Exceptions\Custom\RequestTooFrequentException();
		}

		switch ($usage) {
			case 'reg':
				$user = $this->userRepository->findMobileNo($mobile_no);
				if ($user) {
      			    throw new \App\Exceptions\Custom\PhoneNumRegisteredException();
				}
				break;

			case 'reset':
				$user = $this->userRepository->findMobileNo($mobile_no);
				if (!$user) {
      			    throw new \App\Exceptions\Custom\PhoneNumUnregisteredException();
				}
				break;
			case 'changeAli':
				$user = $this->userRepository->findMobileNo($mobile_no);
				if (!$user) {
      			    throw new \App\Exceptions\Custom\PhoneNumUnregisteredException();
				}
				break;
			case 'resetPayPassword':
				$user = $this->userRepository->findMobileNo($mobile_no);
				if (!$user) {
      			    throw new \App\Exceptions\Custom\PhoneNumUnregisteredException();
				}
				break;
			default:
				dd('未知$usage：' . $usage);
				break;
		}

		$random = rand(1000, 9999);
		//发送短信
        $result = $this->sendSMS($mobile_no, $random, config('sms.'.$usage));

        //将手机及其对应的短信验证码保存到数据库
        $this->saveVerifyCode($mobile_no, $random, $usage, $result);

        if (!$result) {
        	throw new \App\Exceptions\Custom\RequestFailedException('短信发送失败');
        }
        Session::put('SMSLimit', time());
	}

	/**
	 * 将手机及其对应的短信验证码保存到数据库
	 */
	public function saveVerifyCode($mobile_no, $random, $usage, $result)
	{
		try {
			$this->verifyCodeRepository->saveVerifyCode($mobile_no, $random, $usage, $result);
		} catch (Exception $e) {
			throw new \App\Exceptions\Custom\RequestFailedException();
		}
	}

	/**
	 * 发送短信验证码
	 */
	public function sendSMS($mobile_no, $code, $sms_template_code)
	{
		require app_path() . '\Helper\alidayu\TopSdk.php';
		$c = new TopClient;
		$req = new AlibabaAliqinFcSmsNumSendRequest;
		$req->setSmsType("normal");
		$req->setSmsFreeSignName(config('sms.signName'));
		$req->setSmsParam("{\"code\":\"$code\",\"product\":\"校汇\"}");
		$req->setRecNum($mobile_no);
		$req->setSmsTemplateCode($sms_template_code);
		$resp = $c->execute($req);
		if (!isset($resp->result->err_code) or $resp->result->err_code !== '0') {
			Log::error('----------------------------------------------------------------');
			Log::error('短信发送故障，收到阿里大于的错误信息：' . serialize($resp));
			Log::error('----------------------------------------------------------------');
		}
		return true;
	}
}