<?php

namespace App\Repositories;

use DB;
use Log;
use Route;
use Session;
use App\User;
use App\UserInfo;
use App\VerifyCode;
use App\DeviceToken;
use Illuminate\Http\Request;

class UserRepository
{

	protected static $user;

	protected $request;

	function __construct(Request $request)
	{
		$this->request = $request;
		$route_name = Route::currentRouteName();
		$round_arr = round_route();
		if (!self::$user and !empty($request->token) && !in_array($route_name,$round_arr)) {
			//token检验
			self::$user = $this->tokenAuth($request->token);
			if (!self::$user) {
				//token 无效
            	throw new \App\Exceptions\Custom\UserUnauthorizedException();
			}
			self::$user->update(['last_visit' => dtime()]);
		}
	}

	/**
	 * token检验
	 */
	public function tokenAuth($token)
	{
		if (!self::$user) {
			self::$user = User::where('token', $token)->first();
		}
		if (!self::$user) {
			//token 无效
        	throw new \App\Exceptions\Custom\UserUnauthorizedException();
		}
        if (self::$user->ban_flag) {
            throw new \App\Exceptions\Custom\UserBanningException();
        }
		return self::$user;
	}
	/*
		token 校验（不获取缓存）
	*/
	public function getUserTokenAuth()
	{
		$user = User::where('token', $this->request->token)->first();
		if (!$user) {
			//token 无效
        	throw new \App\Exceptions\Custom\UserUnauthorizedException();
		}
        if ($user->ban_flag) {
            throw new \App\Exceptions\Custom\UserBanningException();
        }
		return $user;
	}
	/**
	 * 获得当前用户信息
	 */
	public function getUser()
	{
		self::$user = $this->tokenAuth($this->request->token);
		return self::$user;
	}

	/**
	 * 根据用户ID获取该用户信息
	 */
	public function getUserByUserID($user_id,$custom = '*')
	{
	  	return User::select(DB::raw($custom))->find($user_id);
	}
	/**
	 * 根据用户token获取该用户信息
	 */
	public function getUserByToken($token = '')
	{
	  	self::$user = $user = User::where('token', $token)->first();

		if($user){
			$user->last_visit = dtime();
			$user->save();
		}
	  	return $user;
	}

	public function getBussiness ()
	{
		return User::where('uid', 85)->first(['uid','wallet','mobile_no','nickname','avatar_url','created_at']);
	}
	/**
	 * 获取指定用户的device_token
	 */
	public function getDeviceTokenByUserID($user_id)
	{
	    return DeviceToken::select(DB::raw('device_token.uid, device_token.platform, device_token.push_server, device_token.device_token'))
	    				  ->join('user', 'device_token.uid', '=', 'user.uid')
	    				  ->where('device_token.uid', '=', $user_id)
	    				  ->orderBy('device_token.created_at', 'desc')
	    				  ->first();
	}

	/**
	 * 查找手机号码
	 */
	public function findMobileNo($mobile_no)
	{
		return User::where('mobile_no', $mobile_no)->first();
	}

	/**
	 * 检验用户昵称是否可用
	 */
	public function checkNickname($nickname)
	{
		return DB::table('reserved_nickname')
				 ->select('nickname')
				 ->where('nickname', 'like', '%'.$nickname.'%')
				 ->first();
	}

	/**
	 * 创建用户
	 */
	public function createUser($mobile_no, $password, $nickname, $gender, $enrollment_year,$avatar_url)
	{
		DB::beginTransaction();
		try {
			$u = new User;
	        $u->setConnection('write');
	        $u->openid = DB::raw('md5(UUID())');
	        $u->mobile_no = $mobile_no;
	        $u->password = $password;
	        $u->nickname = $nickname;
			$u->college_id = '1';
	        $u->avatar_url = $avatar_url;
	        $u->created_ip = $this->request->ip();
	        $u->save();

	        self::$user = $u = User::where('mobile_no', $mobile_no)->first();

	        $userInfo = new UserInfo;
	        $userInfo->setConnection('write');
	        $userInfo->uid = $u->uid;
	        $userInfo->gender = $gender;
	        $userInfo->enrollment_year = $enrollment_year;
	        $userInfo->save();

	        DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			throw new \App\Exceptions\Custom\RequestFailedException();
		}
	}

	/**
	 * 检验账号密码是否一致
	 */
	public function checkPassword($mobile_no, $password)
	{
		self::$user = User::where('mobile_no', $mobile_no)->where('password', $password)->first();
		return self::$user;
	}

	/**
	 * 更新Token和登陆IP
	 */
	public function updateLoginStatus($logout = 0)
	{
		self::$user->setConnection('write');
        self::$user->token = $logout ? '' : DB::raw('(select UUID())');
        self::$user->last_ip = $this->request->ip();
        self::$user->last_login = dtime();
        self::$user->last_visit = dtime();
        self::$user->save();

        return User::find(self::$user->uid)->token;
	}

	/**
     * 1)修改密码
     * 2)重置密码
	 */
	public function changePassword(array $param)
	{
		return User::where('mobile_no', isset($param['mobile_no']) ? $param['mobile_no'] : self::$user->mobile_no)
				   ->update(['password' => $param['password'],
				   			 'token' => '']);
		}

	/**
	 * 更新用户信息
	 */
	public function updateUserInfo(array $info)
	{
		$updateUser = false;
		$updateUserInfo = false;
		$userArray = ['mobile_no', 'nickname', 'avatar_url'];
		$userInfoArray = ['gender', 'college_id', 'student_id', 'enrollment_year', 'birth_year', 'birth_month', 'birth_day', 'introduction', 'realname', 'address'];

		$user = self::$user;
		foreach ($userArray as $key => $value) {
			if (!empty($info[$value])) {
				$user->$value = $info[$value];
				$updateUser = true;
			}
		}
		!$updateUser or $user->save();

		foreach ($userInfoArray as $key => $value) {
			if (!empty($info[$value])) {
				$user->userInfo->$value = $info[$value];
				$updateUserInfo = true;
			}
		}
		!$updateUserInfo or $user->userInfo->save();

	}

	/**
	 * 获取当前用户的信息
	 */
	public function getMyInfo()
	{
		return User::select(DB::raw('user.uid,user.openid, user.mobile_no, user.nickname, user.avatar_url, user.integral,user.wallet, user_info.gender, user_info.college_id, college.name as college,
									 user_info.enrollment_year, user_info.birth_year, user_info.birth_month,
									 user_info.birth_day, user_info.favourites_count, user_info.introduction, user_info.realname, user_info.address,user_info.alipay,user_info.alipay_name,
									 if(user_info.alipay<>"",1,0) as is_alipay,
									 if(user.pay_password<>"",1,0) as is_paypassword,
									 if(association.level between 1 and 3,1,if(association_review.uid,2,0)) as is_cheif, if(association.level between 1 and 3,association.aid,0) as association_id,
									 if(shop.shop_status=1,1,0) as is_merchant,
									 if(user_info.realname<>"",1,if(real_name_auth.id>0,2,0)) as is_auth,
									 user.integral, user.today_integral, if(user.integral>0,max(level.level),0) as level,
									 if(upgrade_level.integral>0,upgrade_level.integral-user.integral,0) as upgrade'))
				   ->join('user_info', 'user.uid', '=', 'user_info.uid')
				   ->leftJoin('real_name_auth', 'user.uid', '=', 'real_name_auth.uid')
				   ->leftJoin('college', 'user_info.college_id', '=', 'college.cid')
				   ->leftJoin('association_member as association', 'user.uid', '=', 'association.uid')
				   ->leftJoin('shop', 'user.uid', '=', 'shop.uid')
				   ->leftJoin('association_review', 'user.uid', '=', 'association_review.uid')
				   ->leftJoin('level', 'user.integral', '>=', 'level.integral')
				   ->leftJoin('level as upgrade_level', 'user.integral', '<', 'upgrade_level.integral')
				   ->where('user.uid', self::$user->uid)
				   ->first();
	}

	/**
	 * 获取他人信息
	 */
	public function getOthersInfoByOpenid($openid)
	{
		return User::select(DB::raw('user.uid,user.nickname, user.avatar_url, user.integral, user_info.gender, user_info.college_id, college.name as college,
									 user_info.enrollment_year, user_info.birth_year, user_info.birth_month,
									 user_info.birth_day, user_info.favourites_count, user_info.introduction,
									 if(shop.shop_status=1,1,0) as is_merchant'))
				   ->join('user_info', 'user.uid', '=', 'user_info.uid')
				   ->leftJoin('college', 'user_info.college_id', '=', 'college.cid')
				   ->leftJoin('shop', 'user.uid', '=', 'shop.uid')
				   ->where('user.openid', $openid)
				   ->first();
	}

	public function getAlipayInfo ($uid)
	{
		return User::select(DB::raw('user.nickname,user.uid,user.wallet,user_info.alipay,user_info.alipay_name,if(alipay<>"",1,0) as is_alipay, if(user.pay_password<>"",1,0) as is_paypassword '))
					->join('user_info', 'user.uid', '=', 'user_info.uid')
					->where('user.uid', $uid)
					->first();
	}
	public function updateAlipay ($uid)
	{
		return UserInfo::where('uid',$uid)->update(['alipay'=>$this->request->alipay,'alipay_name'=>$this->request->alipay_name]);
	}
	public function updatePayPassword ($uid,$pay_password)
	{
		return User::where('uid',$uid)->update(['pay_password'=>$pay_password]);
	}

	/**
	 * 绑定用户跟device_token
	 */
	public function bindDeviceToken(array $param)
	{
		$dt = new DeviceToken;
		$dt->uid = self::$user->uid;
		$dt->platform = $param['platform'];
		$dt->push_server = $param['push_server'];
		$dt->device_token = $param['device_token'];
		$dt->save();
	}

	/**
	 * 解除绑定用户跟device_token
	 */
	public function unbindDeviceToken()
	{
		$dt = $this->getUser()->deviceToken();
		return $dt->delete();
	}

	public function getRealUids()
	{
		return UserInfo::where('realname','<>','')->lists('uid')->toArray();
	}
}
