<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\NetworkReport;
use App\Http\Requests;
use App\Services\HelpService;
use App\Services\UserService;
use App\Http\Controllers\Controller;

class ReportNetworkFailureController extends Controller
{	
	protected $cookie;

    function __construct()
    {
        $this->middleware('auth');
    }
	
	public function common($url,$postDate){
		$curl = curl_init();
        $cookie = tempnam('./temp', md5(time()));
		$this->cookie = $cookie;
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 10, 	
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $postDate,
            CURLOPT_COOKIEJAR => $this->cookie,
            CURLOPT_HTTPHEADER => array(
              	"content-type: application/x-www-form-urlencoded",
               'CLIENT-IP:208.165.188.175', 
               'X-FORWARDED-FOR:208.165.188.175',
            ),
            CURLOPT_USERAGENT => "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)",
        ));

        return $response = curl_exec($curl);
	}
	
	public function checkPassword(Request $request, HelpService $help){
		$rule = [
            'account' => 'required',
            'password' => 'required',
            'token' => 'required',
        ];
        $help->validateParameter($rule);
		
		$url = "http://211.66.88.153/network/checkPassword.php";
		$postDate = "account=" . $request->account . "&password=" . $request->password. "&token=" . $request->token;
		$response = $this->common($url,$postDate);
		
		if($response == 404){
			return [
				'code' => 404,
	            'detail' => '学号不存在',
			];
		}elseif($response == 400){
			return [
				'code' => 400,
	            'detail' => '该用户不是校园网用户',
			];
		}elseif($response == 100){
			return [
				'code' => 100,
	            'detail' => '密码错误',
			];
		}elseif($response == 405){
			return [
				'code' => 405,
	            'detail' => 'token无效',
			];
		}elseif(explode('-',$response)[0] == 200){
			return [
				'code' => 200,
	            'detail' => '请求成功',
	            'data' => explode('-',$response)[1] 
			];
		}else{
			return [
				'code' => 500,
	            'detail' => '未知错误',
			];
		}
	}
	
	public function checkByToken(HelpService $help,Request $request,UserService $user){
		$rule = [
            'token' => 'required',
        ];
        $help->validateParameter($rule);
		
		$url = "http://211.66.88.153/network/loginByToken.php";
		$postDate = "token=" . $request->token;
		$response = $this->common($url,$postDate);
		if($response == 200){
			$uid = $user->getUser()->uid;
			$networkReport = NetworkReport::where('uid',$uid)
											->first();
			return [
				'code' => 200,
	            'detail' => '请求成功',
	            'data' => $networkReport->account
			];
		}elseif($response == 405){
			return [
				'code' => 405,
	            'detail' => 'token无效',
			];
		}elseif($response == 403){
			return [
				'code' => 403,
	            'detail' => '还未绑定学号',
			];
		}else{
			return [
				'code' => 500,
	            'detail' => '未知错误',
			];
		}
	}
	
	public function reportByToken(Request $request,HelpService $help,UserService $user){
		$rule = [
            'miaoshu' => 'required',
            'token' => 'required',
        ];
        $help->validateParameter($rule);
		
		$url = "http://211.66.88.153/network/reportByToken.php";
		$postDate = "miaoshu=" . $request->miaoshu . "&token=" . $request->token;
		$response = $this->common($url,$postDate);
		
		if($response == 404){
			return [
				'code' => 404,
	            'detail' => '学号不存在',
			];
		}elseif($response == 400){
			return [
				'code' => 400,
	            'detail' => '该用户不是校园网用户',
			];
		}elseif($response == 100){
			return [
				'code' => 100,
	            'detail' => '密码错误',
			];
		}elseif($response == 405){
			return [
				'code' => 405,
	            'detail' => 'token无效',
			];
		}elseif($response == 200){
			return [
				'code' => 200,
	            'detail' => '请求成功',
			];
		}else{
			return [
				'code' => 500,
	            'detail' => '未知错误',
			];
		}	
	}

	public function reportList(Request $request,HelpService $help){
		$rule = [
            'miaoshu' => 'required',
            'token' => 'required',
        ];
        $help->validateParameter($rule);
		
		$url = "http://211.66.88.153/network/reportList.php";
		$postDate = "miaoshu=" . $request->miaoshu . "&token=" . $request->token;
		$response = $this->common($url,$postDate);
		$response = mb_convert_encoding($response, "UTF-8", "GBK");
		$rs = explode("Array",$response);
		$need=$this->_cut( '(' , ')' , $rs[2]);
		$rs = explode("=>",$need);
		// $need=$this->_cut( '(' , ')' , $rs);
		// $rs = explode("Array",$response);
		// $rs = explode("=>",$rs[1]);
		// print_r($need);
		print_r($rs);
	}
	public  function _cut($begin,$end,$str){
            $b = mb_strpos($str,$begin) + mb_strlen($begin);
            $e = mb_strpos($str,$end) - $b;

            return mb_substr($str,$b,$e);
    }

}
