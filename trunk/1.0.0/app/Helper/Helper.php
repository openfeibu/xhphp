<?php
namespace App\Helper;

use Log;
use App\Helper\alidayu\top\TopClient as TopClient;
use App\Helper\alidayu\top\request\AlibabaAliqinFcSmsNumSendRequest as AlibabaAliqinFcSmsNumSendRequest;

class Helper {
	public static function sendSMS($mobile_no, $code, $sms_template_code)
	{
		require app_path() . '\Helper\alidayu\TopSdk.php';
		$c = new TopClient;
		$req = new AlibabaAliqinFcSmsNumSendRequest;
		$req->setSmsType("normal");
		$req->setSmsFreeSignName("飞步校园");
		$req->setSmsParam("{\"code\":\"$code\",\"product\":\"飞步\"}");
		$req->setRecNum($mobile_no);
		// $req->setSmsTemplateCode("SMS_10840890");
		$req->setSmsTemplateCode($sms_template_code);
		$resp = $c->execute($req);

		if (!isset($resp->result->err_code) or $resp->result->err_code !== 0) {
			Log::error('----------------------------------------------------------------');
			Log::error('短信发送故障，收到阿里大于的错误信息：' . serialize($resp));
			Log::error('----------------------------------------------------------------');
			return 0;
		}
		return $resp->result->success;
	}
}