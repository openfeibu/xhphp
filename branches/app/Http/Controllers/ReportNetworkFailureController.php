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
	
	public function network_login($account,$password,$help){
		$rule = [
            'token' => 'required',
        ];
        $help->validateParameter($rule);

        $curl = curl_init();
        $cookie = tempnam('./temp', md5($account.time()));
		$this->cookie = $cookie;
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://211.66.88.6/xyw/logincheck.asp",
            CURLOPT_USERAGENT => "Mozilla/5.0 (compatible; Baiduspider/2.0; +http://211.66.88.6/xyw)",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 10, 	
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "login_name=" . $account . "&login_password=" . $password,
            CURLOPT_COOKIEJAR => $this->cookie,
            CURLOPT_HTTPHEADER => array(
              	"content-type: application/x-www-form-urlencoded",
               'CLIENT-IP:208.165.188.175', 
               'X-FORWARDED-FOR:208.165.188.175',
            ),
        ));

        $response = curl_exec($curl);
        $result = curl_getinfo($curl);
		return $result;
	}
	
	public function checkPassword(Request $request, HelpService $help,UserService $user){
		$rule = [
            'account' => 'required',
            'password' => 'required',
        ];
        $help->validateParameter($rule);
		
		$result = $this->network_login($request->account,$request->password,$help);
		if ($result['http_code'] == 302 && !empty($result['redirect_url'])) {
			$uid = $user->getUser()->uid;
			$networkReport = NetworkReport::where('uid',$uid)
											->orderBy('id','desc')
											->first();
			if($networkReport){
				$networkReport->account = $request->account;
				$networkReport->password = \Crypt::encrypt($request->password);
				$networkReport->ip = $request->ip();
				$networkReport->save();
			}else{
				$nr = new NetworkReport;
				$nr->uid = $uid;
				$nr->account = $request->account;
				$nr->password = \Crypt::encrypt($request->password);
				$nr->ip = $request->ip();
				$nr->save();
			}
			return [
                'code' => 200,
                'detail' => '账号密码正确',
				'data' => $request->account
            ];
		}else if(empty($result['content_type']) && $result['http_code'] == 0){
			return [
				'code' => 503,
				'detail' => '服务器连接超时',
			];
		}else{
			return [
                'code' => 400,
                'detail' => '账号或密码输入错误，请重新填写',
            ];
		}
	}
	
	public function checkByToken(UserService $user,HelpService $help){
		$rule = [
            'token' => 'required',
        ];
        $help->validateParameter($rule);
		
		$uid = $user->getUser()->uid;
		$networkReport = NetworkReport::where('uid',$uid)
										->whereNotNull('account')
										->orderBy('id','desc')
										->first();	
		if($networkReport){
			$result = $this->network_login($networkReport->account,\Crypt::decrypt($networkReport->password),$help);
			if ($result['http_code'] === 302 and !empty($result['redirect_url'])) {
				return [
					'code' => 200,
					'detail' => '账号密码正确',
					'data' => $networkReport->account
				];
			}else if(empty($result['content_type']) && $result['http_code'] == 0){
				return [
					'code' => 503,
					'detail' => '服务器连接超时',
				];
			}else{
				return [
					'code' => 400,
					'detail' => '账号或密码输入错误，请重新填写',
				];
			}
		}else{
			return [
                'code' => 401,
                'detail' => '还未绑定学号',
            ];
		}
	}
	
	public function reportByToken(Request $request,HelpService $help,UserService $user){
		$rule = [
            'miaoshu' => 'required',
        ];
        $help->validateParameter($rule);
		
		$checkByToken = $this->checkByToken($user,$help);
		if($checkByToken['code'] == 200){
			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL => "http://211.66.88.6/xyw/gzfw.asp?Myaction=gzdj",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 10,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => "miaoshu=" . $request->miaoshu . "&guzhang=1",
				CURLOPT_COOKIEFILE => $this->cookie,
				CURLOPT_HTTPHEADER => array(
				  "content-type: application/x-www-form-urlencoded"
				),
			));

			$response = curl_exec($curl);
			$result = curl_getinfo($curl);
			$response_utf8 = mb_convert_encoding($response, "UTF-8", "gb2312"); 

			if (strstr($response_utf8, '您上次提交的故障尚未处理，请耐心等待网管员上门解决')) {
				return [
					'code' => 403,
					'detail' => '您上次提交的故障尚未处理，请耐心等待网管员上门解决！',
				];
			}
			
			if (strstr($response, 'alert(')) {
				return [
					'code' => 404,
					'detail' => '你尚未开通校园网',
				];
			}
			if ($result['http_code'] == 200) {
				return [
					'code' => 200,
					'detail' => '报修成功',
				];
			} else {
				return [
					'code' => 500,
					'detail' => '系统升级中，请稍后再试',
				];
			}
		}else if(empty($result['content_type']) && $result['http_code'] == 0){
			return [
				'code' => 503,
				'detail' => '服务器连接超时',
			];
		}else if($checkByToken['code'] == 400){
			return [
				'code' => 400,
				'detail' => '账号或密码输入错误，请重新填写',
			];
		}else if($checkByToken['code'] == 401){
			return [
                'code' => 401,
                'detail' => '还未绑定学号 ',
            ];
		}		
	}
	
	/* public function Unbinding(Request $request,HelpService $help,UserService $user){
		$rule = [
            'token' => 'required',
        ];
        $help->validateParameter($rule);
		
		$uid = $user->getUser()->uid;
		$networkReport = NetworkReport::where('uid',$uid)
									->orderBy('id','desc')
									->whereNotNull('account')
									->where('account','<>',"")
									->first();
		
		if($networkReport){
			$networkReport->account = null;
			$networkReport->password = null;
			$networkReport->ip = $request->ip();
			if($networkReport->save()){
				return [
					'code' => 200,
					'detail' => '解绑成功',
				];
			}else{
				return [
					'code' => 403,
					'detail' => '解绑失败，请重新尝试',
				];
			}
		}else{
			return [
                'code' => 401,
                'detail' => '还未绑定学号',
            ];
		}
	} */
}

