<?php

namespace App\Services;

use Log;
use Session;
use Illuminate\Http\Request;
use App\Services\MessageService;
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
						 UserRepository $userRepository,
						 MessageService $messageService)
	{
		$this->request = $request;
		$this->verifyCodeRepository = $verifyCodeRepository;
		$this->userRepository = $userRepository;
		$this->messageService = $messageService;
	}

	/**
	 * 发送短信验证码到手机号码
	 */
	public function sendSMS2Phone($mobile_no, $usage)
	{
        //todo 频率限制
		$se_time = time() - Session::get($mobile_no.'SMSLimit');
		if (Session::get($mobile_no.'SMSLimit') and ($se_time <= 60)) {
			throw new \App\Exceptions\Custom\RequestTooFrequentException((60-$se_time).'秒');
		}

		switch ($usage) {
			case 'reg':
				$user = $this->userRepository->findMobileNo($mobile_no);
				if ($user && $user->password) {
      			    throw new \App\Exceptions\Custom\PhoneNumRegisteredException();
				}
				break;
			case 'reg_verify':
				$user = $this->userRepository->findMobileNo($mobile_no);
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
        //$result = $this->sendSMS($mobile_no, 'verify',['code' => $random,'sms_template_code' => config('sms.'.$usage)]);
		$result = ture;
        //将手机及其对应的短信验证码保存到数据库
        $this->saveVerifyCode($mobile_no, $random, $usage, $result);

        if (!$result) {
        	throw new \App\Exceptions\Custom\RequestFailedException('短信发送失败');
        }
        Session::put($mobile_no.'SMSLimit', time());
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
	 /*
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
	}*/
	/**
	 * 发送短信验证码
	 */

	public function sendCommonSMS($mobile_no, $sms_template_code)
	{
		/*
		require app_path() . DIRECTORY_SEPARATOR.'Helper'.DIRECTORY_SEPARATOR.'alidayu'.DIRECTORY_SEPARATOR.'TopSdk.php';
		$c = new TopClient;
		$req = new AlibabaAliqinFcSmsNumSendRequest;
		$req->setSmsType("normal");
		$req->setSmsFreeSignName(config('sms.signName'));
		$req->setSmsParam("{\"product\":\"校汇\"}");
		$req->setRecNum($mobile_no);
		$req->setSmsTemplateCode($sms_template_code);
		$resp = $c->execute($req);
		if (!isset($resp->result->err_code) or $resp->result->err_code !== '0') {
			Log::error('----------------------------------------------------------------');
			Log::error('短信发送故障，收到阿里大于的错误信息：' . serialize($resp));
			Log::error('----------------------------------------------------------------');
		}*/
		return true;
	}
	public function sendSMS($mobile_no,$type = 'verify',$data = [])
	{
		require app_path() . DIRECTORY_SEPARATOR.'Helper'.DIRECTORY_SEPARATOR.'alidayu'.DIRECTORY_SEPARATOR.'TopSdk.php';
		$c = new TopClient;
		$req = new AlibabaAliqinFcSmsNumSendRequest;
		$req->setSmsType("normal");
		$req->setSmsFreeSignName(config('sms.signName'));
		$req->setRecNum($mobile_no);
		$req->setSmsTemplateCode($data['sms_template_code']);
		switch ($type) {
			case 'verify':
				$code = $data['code'];
				$req->setSmsParam("{\"code\":\"$code\",\"product\":\"校汇\"}");
				break;
			case 'order_info':
				$req->setSmsParam("{\"product\":\"校汇\"}");
				$content = trans('common.sms.'.$type) ;
				$this->messageService->SystemMessage2SingleOne($data['uid'], $content, $push = false, $type = '新订单提醒', $name = '新订单提醒');
				break;
			case 'illegal_task':
				$name = $data['name'];
				$title = $data['title'];
				$req->setSmsParam("{\"product\":\"校汇\",\"name\":\"$name\",\"title\":\"$title\"}");
				break;
			case 'pick_code':
				$code = $data['code'];
				$req->setSmsParam("{\"pick_code\":\"$code\",\"product\":\"校汇\"}");
				$content = sprintf(trans('common.sms.'.$type),$code) ;
				$this->messageService->SystemMessage2SingleOne($data['uid'], $content, $push = false, $type = '取货码', $name = '取货码');
				break;
			default:
				// code...
				break;
		}
		$resp = $c->execute($req);
		if (!isset($resp->result->err_code) or $resp->result->err_code !== '0') {
			throw new \App\Exceptions\Custom\RequestFailedException('短信发送失败');
			Log::error('----------------------------------------------------------------');
			Log::error('短信发送故障，收到阿里大于的错误信息：' . serialize($resp));
			Log::error('----------------------------------------------------------------');
		}
		return true;
	}
}
