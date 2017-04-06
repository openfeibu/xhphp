<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Log;
use Input;
use Event;
use Session;
use App\User;
use App\Http\Requests;
use App\Services\SMSService;
use App\Services\UserService;
use App\Services\HelpService;
use App\Services\ImageService;
use App\Services\MessageService;
use App\Events\Integral\Integrals;
use App\Services\VerifyCodeService;
use App\Http\Controllers\Controller;
use App\Services\RealnameAuthService;
use App\Services\WalletService;
use App\Services\TradeAccountService;

class UserController extends Controller
{
    protected $verifyCodeService;

    protected $smsService;

    protected $userService;

    protected $helpService;

    protected $imageService;

    protected $realnameAuthService;

    protected $messageService;

	protected $walletService;

	protected $tradeAccountService;

    function __construct(VerifyCodeService $verifyCodeService,
                         SMSService $smsService,
                         UserService $userService,
                         HelpService $helpService,
                         ImageService $imageService,
                         RealnameAuthService $realnameAuthService,
                         MessageService $messageService,
                         WalletService $walletService,
                         TradeAccountService $tradeAccountService)
    {
	    parent::__construct();
        $this->middleware('auth', ['except' => ['register', 'isMobileExist', 'login', 'resetPassword', 'getOthersInfo', 'sendRegisterSMS', 'sendResetPasswordSMS','getVerifyImageURL','pushToUsers','uploadImage','pay']]);
        $this->smsService = $smsService;
        $this->userService = $userService;
        $this->verifyCodeService = $verifyCodeService;
        $this->helpService = $helpService;
        $this->imageService = $imageService;
        $this->realnameAuthService = $realnameAuthService;
        $this->messageService = $messageService;
        $this->walletService = $walletService;
        $this->tradeAccountService = $tradeAccountService;
    }

    public function register(Request $request)
    {
        //检验请求参数
        $rule = [
            'mobile_no' => 'required|unique:user,mobile_no,NULL',
            'password' => 'required|alpha_dash',
            'sms_code' => 'required',
            'nickname' => 'required|alpha_dash|unique:user,nickname',
            'gender' => 'required|in:0,1,2',
            'enrollment_year' => 'sometimes|required|after:2000|before:' . (date('Y')+1),
            'avatar_url' => 'sometimes|required|string',
        ];
        $this->helpService->validateParameter($rule);

        //检验用户名是否保留
        $this->userService->checkNickname($request->nickname);

        //检验短信验证码
        $this->verifyCodeService->checkSMS($request->mobile_no, $request->sms_code, 'reg');

        //创建用户
        $user = [
            'mobile_no' => $request->mobile_no,
            'password' => $request->password,
            'nickname' => $request->nickname,
            'gender' => $request->gender,
            'enrollment_year' => isset($request->enrollment_year) ? $request->enrollment_year : '2016',
            'avatar_url' => isset($request->avatar_url) ? $request->avatar_url : config('app.url').'/uploads/system/avatar.png' ,
        ];
        $this->userService->createUser($user);

        throw new \App\Exceptions\Custom\RequestSuccessException();
    }
	/**
     * 上传话题图片
     */
    public function uploadImage(Request $request)
    {
        $images_url = $this->imageService->uploadImages(Input::all(), 'avatar');

        return [
            'code' => 200,
            'detail' => '请求成功',
            'url' => $images_url,
        ];
    }
    public function isMobileExist(Request $request)
    {
        //检验请求参数
        $rule = [
            'mobile_no' => 'required|unique:user,mobile_no,NULL',
        ];
        $this->helpService->validateParameter($rule);

        throw new \App\Exceptions\Custom\RequestSuccessException();
    }

    public function login(Request $request)
    {
        //检验请求参数
        $rule = [
            'mobile_no' => 'required|string|exists:user,mobile_no',
            'password' => 'required|alpha_dash',
            'verify_code' => 'sometimes|required',
            'platform' => 'required|in:and,ios,web',
            'device_token' => 'required_if:platform,and,ios',
            'push_server' => 'sometimes|required|in:xinge,xiaomi',
        ];
        $this->helpService->validateParameter($rule);

        //检验图片验证码
        $this->imageService->checkCaptchaWithInput(isset($request->verify_code) ? $request->verify_code : '');

		/* $verify_code = $request->verify_code;

		if (Session::get('milkcaptcha')){
			if (Session::get('milkcaptcha') != $verify_code) {
				throw new \App\Exceptions\Custom\RequestSuccessException(您输入验证码错误);
			}
			else{
				Session::flash('milkcaptcha', '');
			}
		} */

		//检验账号密码是否一致
        $this->userService->checkPassword($request->mobile_no, $request->password);

        //更新Token和登陆IP
        $token = $this->userService->updateLoginStatus();

        $param = [
            'device_token' => isset($request->device_token) ? $request->device_token : '',
            'platform' => $request->platform,
            'push_server' => isset($request->push_server) ? $request->push_server : 'xinge',
        ];
        //绑定用户跟device_token
        $this->userService->bindDeviceToken($param);

        //积分更新
        Event::fire(new Integrals('每日登录签到'));

        return [
            'code' => 200,
            'detail' => '请求成功',
            'token' => $token,
        ];
    }

    public function logout()
    {
        //检验请求参数
        $rule = [
            'token' => 'required',
        ];
        $this->helpService->validateParameter($rule);

        //更新token
        $this->userService->updateLoginStatus(1);

        //解除绑定用户跟device_token
        $this->userService->unbindDeviceToken();

        throw new \App\Exceptions\Custom\RequestSuccessException();
    }

    public function resetPassword(Request $request)
    {
        //检验请求参数
        $rule = [
            'mobile_no' => 'required|exists:user,mobile_no',
            'password' => 'required|alpha_dash',
            'sms_code' => 'required',
        ];
        $this->helpService->validateParameter($rule);

        //检验短信验证码
        $this->verifyCodeService->checkSMS($request->mobile_no, $request->sms_code, 'reset');

        //重置密码
        $this->userService->changePassword($request->password, $request->mobile_no);

        throw new \App\Exceptions\Custom\RequestSuccessException();
    }

    public function changePassword(Request $request)
    {
        //检验请求参数
        $rule = [
            'password' => 'required|alpha_dash',
            'new_password' => 'required|alpha_dash',
        ];
        $this->helpService->validateParameter($rule);

        //获得当前用户信息
        $user = $this->userService->getUser();
		if($request->password == $request->new_password){
			throw new \App\Exceptions\Custom\OutputServerMessageException("新密码不能与旧密码相同");
		}
        //检验账号密码是否一致
        $this->userService->checkPassword($user->mobile_no, $request->password);

        //修改账号的密码
        $this->userService->changePassword($request->new_password);

        throw new \App\Exceptions\Custom\RequestSuccessException();
    }

    public function changeUserInfo(Request $request)
    {
        //获得当前用户信息
        $user = $this->userService->getUser();

        //检验请求参数
        $rule = [
            'nickname' => 'sometimes|alpha_dash|unique:user,nickname,' . $user->uid . ',uid',
            'gender' => 'sometimes|in:0,1,2',
            'enrollment_year' => 'sometimes',
            'birth_year' => 'sometimes',
            'birth_month' => 'sometimes',
            'birth_day' => 'sometimes',
            'introduction' => 'sometimes',
            'address' => 'sometimes',
        ];
        $this->helpService->validateParameter($rule);

        //修改用户信息
        $this->userService->updateUserInfo(['nickname' => $request->nickname,
                                            'gender' => $request->gender,
                                            'enrollment_year' => $request->enrollment_year,
                                            'birth_year' => $request->birth_year,
                                            'birth_month' => $request->birth_month,
                                            'birth_day' => $request->birth_day,
                                            'introduction' => $request->introduction,
                                            'address' => $request->address]);

        throw new \App\Exceptions\Custom\RequestSuccessException();
    }

    public function realNameAuth(Request $request)
    {
        //检验是否已实名
        $this->userService->isCurrentUserRealNameAuth();

        //检验请求参数
        $rule = [
            'name' => 'required',
            'id_number' => 'required',
        ];
        $this->helpService->validateParameter($rule);
        $imgs['name'] = $request->name;
        $imgs['id_number'] = $request->id_number;

        //上传实名凭证图片
        $images_url = $this->imageService->uploadImages(Input::all(), 'realname_auth');

        // $imgs = [];
        list($imgs['pic1'], $imgs['pic2']) = explode(',', $images_url);
        //保存图片链接到数据库
        $this->realnameAuthService->saveVoucher($imgs);

        //推送纸条
        $this->messageService->SystemMessage2CurrentUser('您好，你已提交实名请求，审核结果将于7个工作日内通知你，请等待。');

        throw new \App\Exceptions\Custom\RequestSuccessException();
    }
	public function realNameAuthUploadImg (Request $request)
	{
		//检验是否已实名
        $this->userService->isCurrentUserRealNameAuth();

        //上传实名凭证图片
        $images_url = $this->imageService->uploadImages(Input::all(), 'realname_auth');

        return [
            'code' => 200,
            'detail' => '请求成功',
            'url' => $images_url,
        ];

	}
	public function h5RealNameAuth (Request $request)
	{
		//检验是否已实名
        $this->userService->isCurrentUserRealNameAuth();

		//检验请求参数
        $rule = [
            'pic1' => 'required',
            'pic2' => 'required',
            'name' => 'required',
            'id_number' => 'required',
        ];
        $this->helpService->validateParameter($rule);
		$imgs['pic1'] = $request->pic1;
        $imgs['pic2'] = $request->pic2;
        $imgs['name'] = $request->name;
		$imgs['id_number'] = $request->id_number;

        //保存图片链接到数据库
        $this->realnameAuthService->saveVoucher($imgs);

        //推送纸条
        $this->messageService->SystemMessage2CurrentUser('您好，你已提交实名请求，审核结果将于7个工作日内通知你，请等待。');

        throw new \App\Exceptions\Custom\RequestSuccessException();
	}
    public function getMyInfo(Request $request)
    {
        //获取个人信息
        $info = $this->userService->getMyInfo();

        return [
            'code' => 200,
            'detail' => '请求成功',
            'data' => $info,
        ];
    }

    public function getOthersInfo(Request $request)
    {
        //检验请求参数
        $rule = [
            'openid' => 'required',
        ];
        $this->helpService->validateParameter($rule);

        //获取他人信息
        $info = $this->userService->getOthersInfo($request->openid);

        return [
            'code' => 200,
            'detail' => '请求成功',
            'data' => $info,
        ];
    }

    public function uploadAvatarFile(Request $request)
    {
        //上传头像文件
        $images_url = $this->imageService->uploadImages(Input::all(), 'avatar');

        //更新用户头像链接
        $img_url = $this->userService->updateAvatar($images_url);

        return [
            'code' => 200,
            'detail' => '请求成功',
            'url' => $images_url,
        ];
    }

    public function sendRegisterSMS(Request $request)
    {
        //检验请求参数
        $rule = [
            'mobile_no' => 'required|unique:user,mobile_no,NULL',
        ];
        $this->helpService->validateParameter($rule);
		
        //发送短信
        $this->smsService->sendSMS2Phone($request->mobile_no, 'reg');

        throw new \App\Exceptions\Custom\RequestSuccessException();
    }

    public function sendResetPasswordSMS(Request $request)
    {
        //检验请求参数
        $rule = [
            'mobile_no' => 'required',
        ];
        $this->helpService->validateParameter($rule);

        //发送短信
        $this->smsService->sendSMS2Phone($request->mobile_no, 'reset');

        throw new \App\Exceptions\Custom\RequestSuccessException();
    }

    public function withdrawalsApply (Request $request)
    {
	    $user = $this->userService->getUser();
		$this->userService->isRealnameAuth();
	    $alipayInfo = $this->userService->getAlipayInfo($user->uid);
	    if(!$alipayInfo->is_paypassword){
		    throw new \App\Exceptions\Custom\OutputServerMessageException('请先设置支付密码');
	    }
	    if(!$alipayInfo->alipay||!$alipayInfo->alipay_name){
		    throw new \App\Exceptions\Custom\OutputServerMessageException('请先完善支付宝账号信息');
	    }
    	$rule = [
            'money' => 'required|integer|min:10',
            'pay_password' => 'required|string',
        ];

        $this->helpService->validateParameter($rule);

        if (!password_verify($request->pay_password, $user->pay_password)) {
		 	throw new \App\Exceptions\Custom\OutputServerMessageException('支付密码错误');
		}

		$service_fee = $this->helpService->applyServiceFee($request->money);

		if($user->wallet < $request->money)
        {
	        throw new \App\Exceptions\Custom\OutputServerMessageException('最多只能提取 '.floor($user->wallet).'元');
        }
		$out_trade_no = $this->helpService->buildOrderSn();
		$total_fee = $service_fee + $request->money; //出账金额
		$wallet = $user->wallet - $total_fee; //钱包余额

		$walletData = array(
			'uid' => $user->uid,
			'wallet' => $wallet,
			'fee'	=>  $total_fee ,
			'service_fee' => $service_fee,
			'out_trade_no' => $out_trade_no,
			'pay_id' => 3,
			'wallet_type' => -1,
			'trade_type' => 'Withdrawals',
			'description' => '提现',
        );
        $trade_no = 'wallet'.$out_trade_no;
        $trade = array(
        	'uid' => $user->uid,
			'out_trade_no' => $out_trade_no,
			'trade_no' => $trade_no,
			'trade_status' => 'cashing',
			'wallet_type' => -1,
			'from' => 'apply_wallet',
			'trade_type' => 'Withdrawals',
			'fee' => $total_fee,
			'service_fee' => $service_fee,
			'pay_id' => 3,
			'description' => '提现',
		);
 		$applyData = array(
 			'uid' => $user->uid,
 			'out_trade_no' => $out_trade_no,
 			'fee' => $request->money,
 			'service_fee' => $service_fee,
 			'total_fee' => $total_fee,
 			'status' => 'wait',
 			'description' => '',
 			'alipay' => $alipayInfo->alipay,
 			'alipay_name' => $alipayInfo->alipay_name,
 		);
        $this->walletService->storeApply($applyData);
        $this->walletService->updateWallet($user->uid,$wallet); //更新钱包
        $this->walletService->store($walletData); //钱包明细记录
        $this->tradeAccountService->addThradeAccount($trade); //交易记录
        return [
			'code' => 200,
			'detail' => '您的提现申请已提交，我们会尽快给您转账，请您耐心等待！'
        ];


    }
    /*我的钱包*/
    public function getWallet ()
    {
    	$user = $this->userService->getUser();
    	$alipayInfo = $this->userService->getAlipayInfo($user->uid);
    	return [
			'code' => 200,
			'data' => [
				'wallet' => $alipayInfo->wallet,
				'alipay' => $alipayInfo->alipay,
				'alipay_name' =>$alipayInfo->alipay_name,
				'is_alipay' => $alipayInfo->is_alipay,
				'is_paypassword' =>$alipayInfo->is_paypassword,
			],
    	];
    }
    /*获得支付宝账号信息*/
    public function getAlipayInfo ()
    {
    	$user = $this->userService->getUser();
    	$alipayInfo = $this->userService->getAlipayInfo($user->uid);
    	return [
			'code' => 200,
			'data' => [
				'alipay' => $alipayInfo->alipay,
				'alipay_name' =>$alipayInfo->alipay_name,
			],
    	];
    }
    /*绑定支付宝账号信息*/
    public function bindAlipay (Request $request)
    {
	    $user = $this->userService->getUser();
	    $alipayInfo = $this->userService->getAlipayInfo($user->uid);
	    if($alipayInfo->is_alipay){
		    throw new \App\Exceptions\Custom\OutputServerMessageException('已绑定过支付宝');
	    }
    	$rule = [
            'alipay' => 'required|string|max:50',
            'alipay_name' => 'required|string|max:50'
        ];
        $this->helpService->validateParameter($rule);
        $this->userService->updateAlipay($user->uid);
        throw new \App\Exceptions\Custom\RequestSuccessException();
    }
    /*修改支付宝账号信息*/
    public function changeAlipay (Request $request)
    {
	    $user = $this->userService->getUser();
	    /*$alipayInfo = $this->userService->getAlipayInfo($user->uid);
	    if(!$alipayInfo->is_alipay){
		    throw new \App\Exceptions\Custom\OutputServerMessageException('未绑定支付宝');
	    }*/
    	$rule = [
            'alipay' => 'required|string|max:50',
            'alipay_name' => 'required|string|max:50',
            'sms_code' => 'required',
        ];

        $this->helpService->validateParameter($rule);

        //检验短信验证码
        $this->verifyCodeService->checkSMS($user->mobile_no, $request->sms_code, 'changeAli');

        $this->userService->updateAlipay($user->uid);

        throw new \App\Exceptions\Custom\RequestSuccessException();

    }
    /*发送短信 -- 修改支付宝账号信息*/
    public function  sendChangeAliSMS()
    {
	    $user = $this->userService->getUser();
	    $alipayInfo = $this->userService->getAlipayInfo($user->uid);
	    /*if(!$alipayInfo->is_alipay){
		    throw new \App\Exceptions\Custom\OutputServerMessageException('未绑定支付宝');
	    }*/
	    $user = $this->userService->getUser();
    	$this->smsService->sendSMS2Phone($user->mobile_no, 'changeAli');
    	throw new \App\Exceptions\Custom\RequestSuccessException();
    }
    /*设置支付密码*/
    public function setPayPassword (Request $request)
    {
	    $user = $this->userService->getUser();
	    $alipayInfo = $this->userService->getAlipayInfo($user->uid);
	    if($alipayInfo->is_paypassword){
		    throw new \App\Exceptions\Custom\OutputServerMessageException('已设置过支付密码');
	    }
    	$rule = [
            'pay_password' => 'required|string',
        ];
        $this->helpService->validateParameter($rule);

        $pay_password = $this->helpService->handlePayPassword($request->pay_password);

		$update = $this->userService->updatePayPassword($user->uid,$pay_password);

        throw new \App\Exceptions\Custom\RequestSuccessException();
    }
    /*修改支付密码*/
    public function changePayPassword (Request $request)
    {
	    $user = $this->userService->getUser();
	    $alipayInfo = $this->userService->getAlipayInfo($user->uid);
	    if(!$alipayInfo->is_paypassword){
		    throw new \App\Exceptions\Custom\OutputServerMessageException('未设置支付密码');
	    }
	    $rule = [
            'new_paypassword' => 'required|string',
            'old_paypassword' => 'required|string',
        ];
        $this->helpService->validateParameter($rule);
        if (!password_verify($request->old_paypassword, $user->pay_password)) {
		 	throw new \App\Exceptions\Custom\OutputServerMessageException('原支付密码错误');
		}
		$pay_password = $this->helpService->handlePayPassword($request->new_paypassword);
		$this->userService->updatePayPassword($user->uid,$pay_password);
    	throw new \App\Exceptions\Custom\RequestSuccessException();
    }
    /*重置支付密码*/
    public function resetPayPassword (Request $request)
    {
	    $user = $this->userService->getUser();
	    $alipayInfo = $this->userService->getAlipayInfo($user->uid);
	    if(!$alipayInfo->is_paypassword){
		    throw new \App\Exceptions\Custom\OutputServerMessageException('未设置过支付密码');
	    }
    	$rule = [
            'pay_password' => 'required|string',
            'sms_code' => 'required',
        ];
        $this->helpService->validateParameter($rule);
        //检验短信验证码
        $this->verifyCodeService->checkSMS($user->mobile_no, $request->sms_code, 'resetPayPassword');

		$pay_password = $this->helpService->handlePayPassword($request->pay_password);

        $this->userService->updatePayPassword($user->uid,$pay_password);

        throw new \App\Exceptions\Custom\RequestSuccessException();
    }
    /*发送短信 -- 重置支付密码*/
    public function  sendResetPayPasswordSMS()
    {
		$user = $this->userService->getUser();
	    $alipayInfo = $this->userService->getAlipayInfo($user->uid);
	    if(!$alipayInfo->is_paypassword){
		    throw new \App\Exceptions\Custom\OutputServerMessageException('未设置过支付密码');
	    }
	    $user = $this->userService->getUser();
    	$this->smsService->sendSMS2Phone($user->mobile_no, 'resetPayPassword');
    	throw new \App\Exceptions\Custom\RequestSuccessException();
    }
    /*钱包明细*/
    public function walletAccount (Request $request)
    {
	    $rules = [
			'page' => 'required|integer|min:1',
	    ];
	    $this->helpService->validateParameter($rules);
    	$user = $this->userService->getUser();
    	$walletAccount = $this->walletService->getWalletAccount($user->uid);
    	if(!count($walletAccount)){
	    	//throw new \App\Exceptions\Custom\OutputServerMessageException('没有更多数据了');
	    	return [
				'code' => 200,
				'data' => [],
	    	];

    	}
    	return [
			'code' => 200,
			'data' => $walletAccount,
    	];
    }
	public function getVerifyImageURL(Request $request)
    {
        $captchaUrl = $this->imageService->generateCaptcha();
        return [
            'code' => 200,
            'url' => $captchaUrl,
        ];
	}

	public function getMobileBytoken(){
		$user = $this->userService->getUser();
		return [
			'code' => 200,
			'data' => $user->mobile_no,
    	];
	}
	public function pushToUsers ()
	{
		$users = User::get();
		foreach( $users as $key => $user )
		{
			$this->messageService->SystemMessage2SingleOne($user->uid, "偷偷告诉你们哦，现在只要更新校汇至1.1.1版本(我的->设置->关于校汇->检查更新)，发布任务不再需要实名认证呢！更多精彩等着你。",true,'系统公告','系统公告');
			sleep(0.5);
		}
	}
	public function pay ()
	{
		$pay = config('pay');
		return $pay;
	}
}
