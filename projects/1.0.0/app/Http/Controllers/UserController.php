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
        //??????????????????
        $rule = [
            'mobile_no' => 'required|unique:user,mobile_no,NULL',
            'password' => 'required|alpha_dash',
            'sms_code' => 'required',
            'nickname' => 'required|alpha_dash|unique:user,nickname',
            'gender' => 'required|in:0,1,2',
            'enrollment_year' => 'required|after:2000|before:' . (date('Y')+1),
            'avatar_url' => 'sometimes|required|string',
        ];
        $this->helpService->validateParameter($rule);

        //???????????????????????????
        $this->userService->checkNickname($request->nickname);

        //?????????????????????
        $this->verifyCodeService->checkSMS($request->mobile_no, $request->sms_code, 'reg');

        //????????????
        $user = [
            'mobile_no' => $request->mobile_no,
            'password' => $request->password,
            'nickname' => $request->nickname,
            'gender' => $request->gender,
            'enrollment_year' => $request->enrollment_year,
            'avatar_url' => isset($request->avatar_url) ? $request->avatar_url : config('app.url').'/uploads/system/avatar.png' ,
        ];
        $this->userService->createUser($user);

        throw new \App\Exceptions\Custom\RequestSuccessException();
    }
	/**
     * ??????????????????
     */
    public function uploadImage(Request $request)
    {
        $images_url = $this->imageService->uploadImages(Input::all(), 'avatar');

        return [
            'code' => 200,
            'detail' => '????????????',
            'url' => $images_url,
        ];
    }
    public function isMobileExist(Request $request)
    {
        //??????????????????
        $rule = [
            'mobile_no' => 'required|unique:user,mobile_no,NULL',
        ];
        $this->helpService->validateParameter($rule);

        throw new \App\Exceptions\Custom\RequestSuccessException();
    }

    public function login(Request $request)
    {
        //??????????????????
        $rule = [
            'mobile_no' => 'required|string|exists:user,mobile_no',
            'password' => 'required|alpha_dash',
            'verify_code' => 'sometimes|required',
            'platform' => 'required|in:and,ios,web',
            'device_token' => 'required_if:platform,and,ios',
            'push_server' => 'sometimes|required|in:xinge,xiaomi',
        ];
        $this->helpService->validateParameter($rule);

        //?????????????????????
        $this->imageService->checkCaptchaWithInput(isset($request->verify_code) ? $request->verify_code : '');

		/* $verify_code = $request->verify_code;

		if (Session::get('milkcaptcha')){
			if (Session::get('milkcaptcha') != $verify_code) {
				throw new \App\Exceptions\Custom\RequestSuccessException(????????????????????????);
			}
			else{
				Session::flash('milkcaptcha', '');
			}
		} */

		//??????????????????????????????
        $this->userService->checkPassword($request->mobile_no, $request->password);

        //??????Token?????????IP
        $token = $this->userService->updateLoginStatus();

        $param = [
            'device_token' => isset($request->device_token) ? $request->device_token : '',
            'platform' => $request->platform,
            'push_server' => isset($request->push_server) ? $request->push_server : 'xinge',
        ];
        //???????????????device_token
        $this->userService->bindDeviceToken($param);

        //????????????
        Event::fire(new Integrals('??????????????????'));

        return [
            'code' => 200,
            'detail' => '????????????',
            'token' => $token,
        ];
    }

    public function logout()
    {
        //??????????????????
        $rule = [
            'token' => 'required',
        ];
        $this->helpService->validateParameter($rule);

        //??????token
        $this->userService->updateLoginStatus(1);

        //?????????????????????device_token
        $this->userService->unbindDeviceToken();

        throw new \App\Exceptions\Custom\RequestSuccessException();
    }

    public function resetPassword(Request $request)
    {
        //??????????????????
        $rule = [
            'mobile_no' => 'required|exists:user,mobile_no',
            'password' => 'required|alpha_dash',
            'sms_code' => 'required',
        ];
        $this->helpService->validateParameter($rule);

        //?????????????????????
        $this->verifyCodeService->checkSMS($request->mobile_no, $request->sms_code, 'reset');

        //????????????
        $this->userService->changePassword($request->password, $request->mobile_no);

        throw new \App\Exceptions\Custom\RequestSuccessException();
    }

    public function changePassword(Request $request)
    {
        //??????????????????
        $rule = [
            'password' => 'required|alpha_dash',
            'new_password' => 'required|alpha_dash',
        ];
        $this->helpService->validateParameter($rule);

        //????????????????????????
        $user = $this->userService->getUser();

        //??????????????????????????????
        $this->userService->checkPassword($user->mobile_no, $request->password);

        //?????????????????????
        $this->userService->changePassword($request->new_password);

        throw new \App\Exceptions\Custom\RequestSuccessException();
    }

    public function changeUserInfo(Request $request)
    {
        //????????????????????????
        $user = $this->userService->getUser();

        //??????????????????
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

        //??????????????????
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
        //?????????????????????
        $this->userService->isCurrentUserRealNameAuth();

        //??????????????????
        $rule = [
            'name' => 'required',
            'id_number' => 'required',
        ];
        $this->helpService->validateParameter($rule);
        $imgs['name'] = $request->name;
        $imgs['id_number'] = $request->id_number;

        //????????????????????????
        $images_url = $this->imageService->uploadImages(Input::all(), 'realname_auth');

        // $imgs = [];
        list($imgs['pic1'], $imgs['pic2']) = explode(',', $images_url);
        //??????????????????????????????
        $this->realnameAuthService->saveVoucher($imgs);

        //????????????
        $this->messageService->SystemMessage2CurrentUser('??????????????????????????????????????????????????????7???????????????????????????????????????');

        throw new \App\Exceptions\Custom\RequestSuccessException();
    }
	public function realNameAuthUploadImg (Request $request)
	{
		//?????????????????????
        $this->userService->isCurrentUserRealNameAuth();

        //????????????????????????
        $images_url = $this->imageService->uploadImages(Input::all(), 'realname_auth');

        return [
            'code' => 200,
            'detail' => '????????????',
            'url' => $images_url,
        ];

	}
	public function h5RealNameAuth (Request $request)
	{
		//?????????????????????
        $this->userService->isCurrentUserRealNameAuth();

		//??????????????????
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

        //??????????????????????????????
        $this->realnameAuthService->saveVoucher($imgs);

        //????????????
        $this->messageService->SystemMessage2CurrentUser('??????????????????????????????????????????????????????7???????????????????????????????????????');

        throw new \App\Exceptions\Custom\RequestSuccessException();
	}
    public function getMyInfo(Request $request)
    {
        //??????????????????
        $info = $this->userService->getMyInfo();

        return [
            'code' => 200,
            'detail' => '????????????',
            'data' => $info,
        ];
    }

    public function getOthersInfo(Request $request)
    {
        //??????????????????
        $rule = [
            'openid' => 'required',
        ];
        $this->helpService->validateParameter($rule);

        //??????????????????
        $info = $this->userService->getOthersInfo($request->openid);

        return [
            'code' => 200,
            'detail' => '????????????',
            'data' => $info,
        ];
    }

    public function uploadAvatarFile(Request $request)
    {
        //??????????????????
        $images_url = $this->imageService->uploadImages(Input::all(), 'avatar');

        //????????????????????????
        $img_url = $this->userService->updateAvatar($images_url);

        return [
            'code' => 200,
            'detail' => '????????????',
            'url' => $images_url,
        ];
    }

    public function sendRegisterSMS(Request $request)
    {
        //??????????????????
        $rule = [
            'mobile_no' => 'required',
        ];
        $this->helpService->validateParameter($rule);

        //????????????
        $this->smsService->sendSMS2Phone($request->mobile_no, 'reg');

        throw new \App\Exceptions\Custom\RequestSuccessException();
    }

    public function sendResetPasswordSMS(Request $request)
    {
        //??????????????????
        $rule = [
            'mobile_no' => 'required',
        ];
        $this->helpService->validateParameter($rule);

        //????????????
        $this->smsService->sendSMS2Phone($request->mobile_no, 'reset');

        throw new \App\Exceptions\Custom\RequestSuccessException();
    }

    public function withdrawalsApply (Request $request)
    {
	    $user = $this->userService->getUser();
	    $alipayInfo = $this->userService->getAlipayInfo($user->uid);
	    if(!$alipayInfo->is_paypassword){
		    throw new \App\Exceptions\Custom\OutputServerMessageException('????????????????????????');
	    }
	    if(!$alipayInfo->alipay||!$alipayInfo->alipay_name){
		    throw new \App\Exceptions\Custom\OutputServerMessageException('?????????????????????????????????');
	    }
    	$rule = [
            'money' => 'required|integer|min:10',
            'pay_password' => 'required|string',
        ];
        if (!password_verify($request->pay_password, $user->pay_password)) {
		 	throw new \App\Exceptions\Custom\OutputServerMessageException('??????????????????');
		}
        $this->helpService->validateParameter($rule);

		$service_fee = $this->helpService->applyServiceFee($request->money);

		if($user->wallet < $request->money)
        {
	        throw new \App\Exceptions\Custom\OutputServerMessageException('?????????????????? '.floor($user->wallet).'???');
        }
		$out_trade_no = $this->helpService->buildOrderSn();
		$total_fee = $service_fee + $request->money; //????????????
		$wallet = $user->wallet - $total_fee; //????????????

		$walletData = array(
			'uid' => $user->uid,
			'wallet' => $wallet,
			'fee'	=>  $total_fee ,
			'service_fee' => $service_fee,
			'out_trade_no' => $out_trade_no,
			'pay_id' => 3,
			'wallet_type' => -1,
			'trade_type' => 'Withdrawals',
			'description' => '??????',
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
			'description' => '??????',
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
        $this->walletService->updateWallet($user->uid,$wallet); //????????????
        $this->walletService->store($walletData); //??????????????????
        $this->tradeAccountService->addThradeAccount($trade); //????????????
        return [
			'code' => 200,
			'detail' => '?????????????????????????????????????????????????????????????????????????????????'
        ];


    }
    /*????????????*/
    public function getWallet ()
    {
    	$user = $this->userService->getUser();
    	$alipayInfo = $this->userService->getAlipayInfo($user->uid);
    	return [
			'code' => 200,
			'data' => [
				'wallet' => $alipayInfo->wallet,
				'alipay' => $alipayInfo->alipay,
				'is_alipay' => $alipayInfo->is_alipay,
				'is_paypassword' =>$alipayInfo->is_paypassword,
			],
    	];
    }
    /*???????????????????????????*/
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
    /*???????????????????????????*/
    public function bindAlipay (Request $request)
    {
	    $user = $this->userService->getUser();
	    $alipayInfo = $this->userService->getAlipayInfo($user->uid);
	    if($alipayInfo->is_alipay){
		    throw new \App\Exceptions\Custom\OutputServerMessageException('?????????????????????');
	    }
    	$rule = [
            'alipay' => 'required|string|max:50',
            'alipay_name' => 'required|string|max:50'
        ];
        $this->helpService->validateParameter($rule);
        $this->userService->updateAlipay($user->uid);
        throw new \App\Exceptions\Custom\RequestSuccessException();
    }
    /*???????????????????????????*/
    public function changeAlipay (Request $request)
    {
	    $user = $this->userService->getUser();
	    $alipayInfo = $this->userService->getAlipayInfo($user->uid);
	    if(!$alipayInfo->is_alipay){
		    throw new \App\Exceptions\Custom\OutputServerMessageException('??????????????????');
	    }
    	$rule = [
            'alipay' => 'required|string|max:50',
            'alipay_name' => 'required|string|max:50',
            'sms_code' => 'required',
        ];

        $this->helpService->validateParameter($rule);

        //?????????????????????
        $this->verifyCodeService->checkSMS($user->mobile_no, $request->sms_code, 'changeAli');

        $this->userService->updateAlipay($user->uid);

        throw new \App\Exceptions\Custom\RequestSuccessException();

    }
    /*???????????? -- ???????????????????????????*/
    public function  sendChangeAliSMS()
    {
	    $user = $this->userService->getUser();
	    $alipayInfo = $this->userService->getAlipayInfo($user->uid);
	    if(!$alipayInfo->is_alipay){
		    throw new \App\Exceptions\Custom\OutputServerMessageException('??????????????????');
	    }
	    $user = $this->userService->getUser();
    	$this->smsService->sendSMS2Phone($user->mobile_no, 'changeAli');
    	throw new \App\Exceptions\Custom\RequestSuccessException();
    }
    /*??????????????????*/
    public function setPayPassword (Request $request)
    {
	    $user = $this->userService->getUser();
	    $alipayInfo = $this->userService->getAlipayInfo($user->uid);
	    if($alipayInfo->is_paypassword){
		    throw new \App\Exceptions\Custom\OutputServerMessageException('????????????????????????');
	    }
    	$rule = [
            'pay_password' => 'required|string',
        ];
        $this->helpService->validateParameter($rule);

        $pay_password = $this->helpService->handlePayPassword($request->pay_password);

		$update = $this->userService->updatePayPassword($user->uid,$pay_password);

        throw new \App\Exceptions\Custom\RequestSuccessException();
    }
    /*??????????????????*/
    public function changePayPassword (Request $request)
    {
	    $user = $this->userService->getUser();
	    $alipayInfo = $this->userService->getAlipayInfo($user->uid);
	    if(!$alipayInfo->is_paypassword){
		    throw new \App\Exceptions\Custom\OutputServerMessageException('?????????????????????');
	    }
	    $rule = [
            'new_paypassword' => 'required|string',
            'old_paypassword' => 'required|string',
        ];
        $this->helpService->validateParameter($rule);
        if (!password_verify($request->old_paypassword, $user->pay_password)) {
		 	throw new \App\Exceptions\Custom\OutputServerMessageException('?????????????????????');
		}
		$pay_password = $this->helpService->handlePayPassword($request->new_paypassword);
		$this->userService->updatePayPassword($user->uid,$pay_password);
    	throw new \App\Exceptions\Custom\RequestSuccessException();
    }
    /*??????????????????*/
    public function resetPayPassword (Request $request)
    {
	    $user = $this->userService->getUser();
	    $alipayInfo = $this->userService->getAlipayInfo($user->uid);
	    if(!$alipayInfo->is_paypassword){
		    throw new \App\Exceptions\Custom\OutputServerMessageException('????????????????????????');
	    }
    	$rule = [
            'pay_password' => 'required|string',
            'sms_code' => 'required',
        ];
        $this->helpService->validateParameter($rule);
        //?????????????????????
        $this->verifyCodeService->checkSMS($user->mobile_no, $request->sms_code, 'resetPayPassword');

		$pay_password = $this->helpService->handlePayPassword($request->pay_password);

        $this->userService->updatePayPassword($user->uid,$pay_password);

        throw new \App\Exceptions\Custom\RequestSuccessException();
    }
    /*???????????? -- ??????????????????*/
    public function  sendResetPayPasswordSMS()
    {
		$user = $this->userService->getUser();
	    $alipayInfo = $this->userService->getAlipayInfo($user->uid);
	    if(!$alipayInfo->is_paypassword){
		    throw new \App\Exceptions\Custom\OutputServerMessageException('????????????????????????');
	    }
	    $user = $this->userService->getUser();
    	$this->smsService->sendSMS2Phone($user->mobile_no, 'resetPayPassword');
    	throw new \App\Exceptions\Custom\RequestSuccessException();
    }
    /*????????????*/
    public function walletAccount (Request $request)
    {
	    $rules = [
			'page' => 'required|integer|min:1',
	    ];
	    $this->helpService->validateParameter($rules);
    	$user = $this->userService->getUser();
    	$walletAccount = $this->walletService->getWalletAccount($user->uid);
    	if(!count($walletAccount)){
	    	//throw new \App\Exceptions\Custom\OutputServerMessageException('?????????????????????');
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
			$this->messageService->SystemMessage2SingleOne($user->uid, "???????????????????????????????????????????????????1.1.1??????(??????->??????->????????????->????????????)?????????????????????????????????????????????????????????????????????",true,'????????????','????????????');
			sleep(0.5);
		}
	}
	public function pay ()
	{
		$pay = config('pay');
		return $pay;
	}
}
