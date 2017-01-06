<?php

namespace App\Services;

use Event;
use Session;
use Validator;
use Illuminate\Http\Request;
use App\Events\Integral\Integrals;
use App\Repositories\UserRepository;

class UserService
{
    protected $request;

	protected $userRepository;

	function __construct(Request $request,
                         UserRepository $userRepository)
	{
        $this->request = $request;
		$this->userRepository = $userRepository;
	}

    /**
     * token检验
     */
    public function tokenAuth($token)
    {
        Event::fire(new Integrals('每日登录签到'));
        return $this->userRepository->tokenAuth($token);
    }

    /**
     * 获取当前用户信息
     */
    public function getUser()
    {
        return $this->userRepository->getUser();
    }
	
	/**
     * 根据用户ID获取该用户信息
     */
    public function getUserByToken($token)
    {
        return $this->userRepository->getUserByToken($token);
    }
    /**
     * 根据用户ID获取该用户信息
     */
    public function getUserByUserID($user_id)
    {
        return $this->userRepository->getUserByUserID($user_id);
    }

    /**
     * 获取指定用户的device_token
     */
    public function getDeviceTokenByUserID($user_id)
    {
        return $this->userRepository->getDeviceTokenByUserID($user_id);
    }

    /**
     * 获取当前用户的devive_token
     */
    public function getCurrentUserDeviceToken()
    {
        $user_id = $this->getUser()->uid;
        return $this->getDeviceTokenByUserID($user_id);
    }

    /**
     * 检验手机号码是否已注册
     */
    public function isMobileExist($mobile_no)
    {
        $user = $this->userRepository->findMobileNo($mobile_no);
        if ($user) {
            throw new \App\Exceptions\Custom\PhoneNumRegisteredException();
        }
        return false;
    }

    /**
     * 检验手机号码是否未注册
     */
    public function isMobileNonexistent($mobile_no)
    {
        $user = $this->userRepository->findMobileNo($mobile_no);
        if (!$user) {
            throw new \App\Exceptions\Custom\PhoneNumUnregisteredException();
        }
        return false;
    }

    /**
     * 检验用户昵称是否可用
     */
    public function checkNickname($nickname)
    {
        $result = $this->userRepository->checkNickname($nickname);
        if ($result) {
            throw new \App\Exceptions\Custom\OutputServerMessageException('用户名已存在');
        }
        return true;
    }

    /**
     * 创建用户
     */
    public function createUser(array $user)
    {
        $this->userRepository->createUser($user['mobile_no'],
                                          $user['password'],
                                          $user['nickname'],
                                          $user['gender'],
                                          $user['enrollment_year'],
                                          $user['avatar_url']);
        return true;
    }

    /**
     * 检验账号密码是否一致
     */
    public function checkPassword($mobile_no, $password)
    {
        $user = $this->userRepository->checkPassword($mobile_no, $password);
        if (!$user) {
            //失败次数加一
            $this->incrementLoginAttempts();
            throw new \App\Exceptions\Custom\UserPasswordIncorrectException();
        } else {
            //失败次数统计归零
            $this->initLoginAttempts();
        }
        //检验是否封号
        $this->isUserBanned($user);

        return $user;
    }

	/**
     * 登陆失败次数加一
     */
    public function incrementLoginAttempts()
    {
        $oldCount = Session::get('login.failure', 0) + 1;
        Session::put('login.failure', $oldCount);
        $this->isTooManyLoginAttempts();
        return $oldCount;
    }

    /**
     * 用户是否多次尝试登陆失败（要求用户填写图片验证码）
     */
    public function isTooManyLoginAttempts()
    {
        if (Session::get('login.failure', 0) >= 3) {
            Session::put('captcha', rand());
            throw new \App\Exceptions\Custom\CaptchaImageIncorrectException();
        }
        return false;
    }

    /**
     * 登陆失败次数统计归零
     */
    public function initLoginAttempts()
    {
        Session::forget('login.failure');
    }

    /**
     * 检验是否封号
     */
    public function isUserBanned($user = '')
    {
        if ($user == '') {
            $user = $this->userRepository->getUser();
        }
        if ($user->ban_flag) {
            //封号
            throw new \App\Exceptions\Custom\UserBanningException();
        }
    }

    /**
     * 更新Token和登陆IP
     */
    public function updateLoginStatus($logout = 0)
    {
        try {
            return $this->userRepository->updateLoginStatus($logout);
        } catch (Exception $e) {
            throw new \App\Exceptions\Custom\RequestFailedException();
        }
    }

    /**
     * 1)修改密码
     * 2)重置密码
     */
    public function changePassword($new_password, $mobile_no = '')
    {
        try {
            $param = [];
            if ($mobile_no != '') {
                $param['mobile_no'] = $mobile_no;
            }
            $param['password'] = $new_password;
            $this->userRepository->changePassword($param);

            return true;
        } catch (Exception $e) {
            throw new \App\Exceptions\Custom\RequestFailedException();
        }
    }

    /**
     * 修改用户信息
     */
    public function updateUserInfo(array $param)
    {
        try {
            $this->userRepository->updateUserInfo($param);
            return true;
        } catch (Exception $e) {
            throw new \App\Exceptions\Custom\RequestFailedException();
        }
    }

    /**
     * 检验用户是否已经实名认证
     */
    public function isCurrentUserRealNameAuth()
    {
        $user = $this->userRepository->getUser();
        if ($user->userInfo->realname) {
            throw new \App\Exceptions\Custom\OutputServerMessageException('你已经实名了');
        } elseif ($user->realnameAuth) {
            throw new \App\Exceptions\Custom\OutputServerMessageException('你已提交实名请求。');
        }
        return true;
    }

    /**
     * 获取个人信息
     */
    public function getMyInfo()
    {
        return $this->userRepository->getMyInfo();
    }

    /**
     * 获取他人信息
     */
    public function getOthersInfo($openid)
    {
        return $this->userRepository->getOthersInfoByOpenid($openid);
    }

    /**
     * 更新用户头像链接
     */
    public function updateAvatar($avatar_url)
    {
        $this->userRepository->updateUserInfo(['avatar_url' => $avatar_url]);
    }

    /**
     * 检验是否已实名
     */
    public function isRealnameAuth()
    {
        $user = $this->userRepository->getUser()->userInfo;
        if (!$user->realname) {
            throw new \App\Exceptions\Custom\OutputServerMessageException('请先到个人中心实名后方可发/接任务！');
        }
        return true;
    }

    public function getAlipayInfo ($uid)
    {
    	return $this->userRepository->getAlipayInfo($uid);
    }
    public function updateAlipay ($uid)
    {
    	return $this->userRepository->updateAlipay($uid);
    }
    public function updatePayPassword ($uid,$pay_password)
    {
    	return $this->userRepository->updatePayPassword($uid,$pay_password);
    }

    /**
     * 绑定用户跟device_token
     */
    public function bindDeviceToken(array $param)
    {
        if ($param['platform'] == 'web') {
            return true;
        }
       
        return $this->userRepository->bindDeviceToken($param);
    }

    /**
     * 解除绑定用户跟device_token
     */
    public function unbindDeviceToken()
    {
        return $this->userRepository->unbindDeviceToken();
    }

    public function getRealUids()
    {
    	return $this->userRepository->getRealUids();
    }
}