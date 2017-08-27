<?php

namespace App\Services;

use Validator;
use DB;
use Log;
use App\TradeAccount;
use App\ShippingConfig;
use Illuminate\Http\Request;

class HelpService
{

	public $request;

	function __construct(Request $request)
	{
		$this->request = $request;
	}

	/**
	 * 检验请求参数
	 */
	public function validateParameter($rules)
	{
		$validator = Validator::make($this->request->all(), $rules);
        if ($validator->fails()) {
    		throw new \App\Exceptions\Custom\OutputServerMessageException($validator->errors()->first());
        } else {
        	return true;
        }
	}
	public function validateData ($value,$custom)
	{
		if(preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$value)){
			throw new \App\Exceptions\Custom\OutputServerMessageException($custom."含非法参数");
		}
		return true;
	}
    public function buildOrderSn($prefix = ''){

        $out_trade_no = $prefix.'XH'.date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        if(TradeAccount::where('out_trade_no',$out_trade_no)->first()){
	        $out_trade_no = buildOrderSn($prefix);
        }
        return $out_trade_no;
    }

    public function serviceFee ($total_fee)
    {
    	return ($total_fee * 0.02) > 0.1 ? ($total_fee * 0.02) : 0.1;
    }
    public function applyServiceFee ($fee)
    {
    	return 0;
    }
    public function moneyHandle ($money,$type,$prefix = false ,$suffix = false)
    {
	    $money = $money ;
	    if($prefix){
		    $money = "￥".$money;
	    }else if($suffix){
		     $money .= "元";
	    }
    	if($type == 1){
	    	$money = '+'.$money;
    	}else if($type == -1){
	    	$money = '-'.$money;
    	}
    	return $money;
    }
    public function do_hash($psw) {
	    $salt = 'xiaohuifdsafagfdgv43532ju76jM';
	    return md5($psw . $salt);
	}
	public function handlePayPassword ($pay_password)
	{
		//$options = [
		// 'salt' => custom_function_for_salt(),
		// 'cost' => 2
		//];
		$hash = password_hash($pay_password, PASSWORD_BCRYPT);
		return $hash;
	}
	public function handleRealName ($file_contents)
	{
		$contents = str_replace('(','',trim($file_contents));
		$contents = str_replace(')','',$file_contents);
		$contents = json_decode($file_contents);
		return $contents;
	}
	public function wp_is_mobile() {
	    static $is_mobile;

	    if ( isset($is_mobile) )
	        return $is_mobile;

	    if ( empty($_SERVER['HTTP_USER_AGENT']) ) {
	        $is_mobile = false;
	    } elseif ( strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false
	        || strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false
	        || strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/') !== false
	        || strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false
	        || strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false
	        || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false
	        || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mobi') !== false ) {
	            $is_mobile = true;
	    } else {
	        $is_mobile = false;
	    }

	    return $is_mobile;
	}
	public function is_wap(){
	    if(isset($_SERVER['HTTP_VIA'])) return TRUE;
	    if(isset($_SERVER['HTTP_X_NOKIA_CONNECTION_MODE'])) return TRUE;
	    if(isset($_SERVER['HTTP_X_UP_CALLING_LINE_ID'])) return TRUE;
	    if(strpos(strtoupper($_SERVER['HTTP_ACCEPT']), 'VND.WAP.WML') > 0) return TRUE;
	    $http_user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? trim($_SERVER['HTTP_USER_AGENT']) : '';
	    if($http_user_agent == '') return TRUE;
	    $mobile_os = array('Google Wireless Transcoder', 'Windows CE', 'WindowsCE', 'Symbian', 'Android', 'armv6l', 'armv5', 'Mobile', 'CentOS', 'mowser', 'AvantGo', 'Opera Mobi', 'J2ME/MIDP', 'Smartphone', 'Go.Web', 'Palm', 'iPAQ');
	    $mobile_token = array('Profile/MIDP', 'Configuration/CLDC-', '160×160', '176×220', '240×240', '240×320', '320×240', 'UP.Browser', 'UP.Link', 'SymbianOS', 'PalmOS', 'PocketPC', 'SonyEricsson', 'Nokia', 'BlackBerry', 'Vodafone', 'BenQ', 'Novarra-Vision', 'Iris', 'NetFront', 'HTC_', 'Xda_', 'SAMSUNG-SGH', 'Wapaka', 'DoCoMo', 'iPhone', 'iPod');
	    $flag_os = $flag_token = FALSE;
	    foreach($mobile_os as $val){
	        if(strpos($http_user_agent, $val) > 0){ $flag_os = TRUE; break; }
	    }
	    foreach($mobile_token as $val){
	        if(strpos($http_user_agent, $val) > 0){ $flag_token = TRUE; break; }
	    }
	    if($flag_os || $flag_token) return TRUE;
	    return FALSE;
	}

	/**
	* desription 压缩图片
	* @param sting $imgsrc 图片路径
	* @param string $imgdst 压缩后保存路径
	*/
	public function image_png_size_add($imgsrc,$imgdst){
	  	list($width,$height,$type)=getimagesize($imgsrc);
	  	$new_width = ($width>600?600:$width)*0.9;
	  	$new_height =($height>600?600:$height)*0.9;
	  	switch($type){
	    	case 1:
	      		$giftype=$this->check_gifcartoon($imgsrc);
	      		if($giftype){
	        		$image_wp=imagecreatetruecolor($new_width, $new_height);
	        		$image = imagecreatefromgif($imgsrc);
	        		imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
	        		imagegif($image_wp, $imgdst,75);
	        		imagedestroy($image_wp);
	      		}
	      		break;
	    	case 2:
				$image_wp=imagecreatetruecolor($new_width, $new_height);
				$image = imagecreatefromjpeg($imgsrc);
				imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
				imagejpeg($image_wp, $imgdst,75);
				imagedestroy($image_wp);
				break;
			case 3:
				$image_wp=imagecreatetruecolor($new_width, $new_height);
				$image = imagecreatefrompng($imgsrc);
				imagesavealpha($image,true);
				imagealphablending($image_wp,false);
           		imagesavealpha($image_wp,true);
				imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
				imagepng($image_wp, $imgdst);
				imagedestroy($image_wp);
				break;
	  	}

	}
	/**
	* desription 判断是否gif动画
	* @param sting $image_file图片路径
	* @return boolean t 是 f 否
	*/
	public function check_gifcartoon($image_file){
	  $fp = fopen($image_file,'rb');
	  $image_head = fread($fp,1024);
	  fclose($fp);
	  return true;
	}
	public function isVaildImage($files)
	{
		$error = '';

		foreach($files as $key => $file)
		{
			$name = $file->getClientOriginalName();
			if(!$file->isValid())
			{
				$error.= $name.$file->getErrorMessage().';';
			}
			if(!in_array( strtolower($file->extension()),config('common.img_type'))){
				$error.= $name."类型错误;";
			}
			if($file->getClientSize() > config('common.img_size')){
				$img_size = config('common.img_size')/1024;
				$error.= $name.'超过'.$img_size.'M';
			}
		}
		if($error)
		{
			throw new \App\Exceptions\Custom\OutputServerMessageException($error);
		}
	}
	public function telecomCheckReal ($fields)
	{
		$ch = curl_init();

 		$url = config('common.real_name_url');

        curl_setopt ($ch, CURLOPT_URL, $url);

        curl_setopt ($ch, CURLOPT_POST, 1);

 		$fields_string = http_build_query ( $fields, '&' );

        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 5);

 		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; Baiduspider/2.0; +http://www.baidu.com/search/spider.html)");

        curl_setopt($ch, CURLOPT_HEADER, false);

        $file_contents = curl_exec($ch);

        curl_close($ch);

		$file_contents = str_replace('(','',trim($file_contents));

		$file_contents = str_replace(')','',$file_contents);

		$file_contents = json_decode($file_contents);

		/*if($file_contents->resultCode == 'CONFIRM_SUCCESS'){
			$realData = array(
				'uid' => $user->uid,
				'telecom_phone' => $request->phone,
				'telecom_iccid' => $request->iccid,
				'telecom_outOrderNumber' => $request->outOrderNumber,
			);
	        $this->telecomService->storeRealName($realData);
	        return [
		    	'code' => 200,
		    	'detail' => '已实名',
		    ];
		}
		if($file_contents->resultCode == 'SUCCESS'){
			return [
		    	'code' => 8401,
		    	'url' => isset($file_contents->shortLinkUrl)? $file_contents->shortLinkUrl:'',
				'detail' => '对应订单已存在，等待天猫实名认证！',
		    ];
		}
		if($file_contents->resultCode){
			return [
				'code' => 8401,
				'url' => isset($file_contents->shortLinkUrl)? $file_contents->shortLinkUrl:'',
				'detail' => $file_contents->resultMessage,
			];
		}*/

		return $file_contents;
	}
	public function shopServiceFee ($fee,$rate)
	{
		return $fee * $rate;
	}
	/*
		获取买家应该担负的任务费
	*/
	public function getBuyerShippingFee($weight,$fee)
	{
		$shipping_fee = 0;

		$shipping_config = DB::table('shipping_config')->where('min','<=',$fee)->where('max','>=',$fee)->first();
		if($shipping_config)
		{
			if($shipping_config->payer == 'buyer')
			{
				$shipping_fee += $shipping_config->shipping_fee;
			}
			if($weight > $shipping_config->weight)
			{
				$out_weight = $weight - $shipping_config->weight;
				$out_weight_fee = ceil($out_weight) * $shipping_config->outweight;
				$shipping_fee = $out_weight_fee + $shipping_fee;
			}
		}
		return $shipping_fee;
	}
	public function getSellerShippingFee($weight,$fee)
	{
		$shipping_fee = 0;

		$shipping_config = DB::table('shipping_config')->where('min','<=',$fee)->where('max','>=',$fee)->first();
		if($shipping_config)
		{
			if($shipping_config->payer == 'seller')
			{
				$shipping_fee += $shipping_config->shipping_fee;
			}
		}
		return $shipping_fee;
	}
	/*初始化*/
	public function zhima_initialize($bodys)
	{
		$host = config('zhima.host');
		$path = config('zhima.initialize_path');
		$method = "POST";
		$appcode = config('zhima.appcode');
		$headers = array();
		array_push($headers, "Authorization:APPCODE " . $appcode);
		//根据API的要求，定义相对应的Content-Type
		array_push($headers, "Content-Type".":"."application/x-www-form-urlencoded; charset=UTF-8");
		$querys = "";
		$bodys = http_build_query($bodys);
		$url = $host . $path;

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_FAILONERROR, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, false);
		if (1 == strpos("$".$host, "https://"))
		{
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		}
		curl_setopt($curl, CURLOPT_POSTFIELDS, $bodys);
		$data = curl_exec($curl);
		Log::debug($data);
		$data = json_decode($data,true);
        if(!$data['success'])
        {
			throw new \App\Exceptions\Custom\OutputServerMessageException($data['message']);
        }
		return $data;
	}
	/*认证*/
	public function zhima_certify($bodys)
	{
		$host = config('zhima.host');
		$path = config('zhima.certify_path');
		$method = "POST";
		$appcode = config('zhima.appcode');
		$headers = array();
		array_push($headers, "Authorization:APPCODE " . $appcode);
		//根据API的要求，定义相对应的Content-Type
		array_push($headers, "Content-Type".":"."application/x-www-form-urlencoded; charset=UTF-8");
		$querys = "";
		$bodys = http_build_query($bodys);
		$url = $host . $path;

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_FAILONERROR, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, false);
		if (1 == strpos("$".$host, "https://"))
		{
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		}
		curl_setopt($curl, CURLOPT_POSTFIELDS, $bodys);
		$data = curl_exec($curl);
		Log::debug($data);
		$data = json_decode($data,true);
        if(!$data['success'])
        {
			throw new \App\Exceptions\Custom\OutputServerMessageException($data['message']);
        }
		return $data;
	}
	public function zhima_query($bodys)
	{
		$host = config('zhima.host');
		$path = config('zhima.query_path');
	    $method = "POST";
	   	$appcode = config('zhima.appcode');
	    $headers = array();
	    array_push($headers, "Authorization:APPCODE " . $appcode);
	    //根据API的要求，定义相对应的Content-Type
	    array_push($headers, "Content-Type".":"."application/x-www-form-urlencoded; charset=UTF-8");
	    $querys = "";
	    $bodys = http_build_query($bodys);
	    $url = $host . $path;

	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
	    curl_setopt($curl, CURLOPT_URL, $url);
	    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	    curl_setopt($curl, CURLOPT_FAILONERROR, false);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($curl, CURLOPT_HEADER, false);
	    if (1 == strpos("$".$host, "https://"))
	    {
	        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	    }
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $bodys);
		$data = curl_exec($curl);
		Log::debug($data);
		$data = json_decode($data,true);
		if(!$data['success'])
        {
			throw new \App\Exceptions\Custom\OutputServerMessageException($data['message']);
        }
		return $data;
	}

}
