<?php

namespace App;

use DB;
use Illuminate\Http\Request;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    protected $primaryKey = 'uid';


    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password','pay_password','remember_token'];

    public function verifyCode()
    {
        return $this->hasMany('App\VerifyCode', 'mobile_no', 'mobile_no');
    }

    public function userInfo()
    {
        return $this->hasOne('App\UserInfo', 'uid', 'uid');
    }

    public function order()
    {
        return $this->hasMany('App\Order', 'uid', 'owner_id');
    }

    public function associationReview()
    {
        return $this->hasOne('App\AssociationReview', 'uid', 'uid');
    }

    public function realnameAuth()
    {
        return $this->hasOne('App\RealnameAuth', 'uid', 'uid');
    }

    public function deviceToken()
    {
        return $this->hasOne('App\DeviceToken', 'uid', 'uid');
    }

    #todo 删除
    /**
     * token是否登陆
     *
     * @param  string  $token 令牌
     *
     * @return boolean        true已登陆，false未登陆
     */
    public static function isLogin($token)
    {
        $user = DB::table('user')->where('token', $token)->where('ban_flag', 0)->first();
        if ($user) {
            return $user;
        } else {
            return false;
        }
    }

    #todo 删除
    /**
     * token是否登陆且为某个社团的负责人
     *
     * @param  string  $token 令牌
     *
     * @return boolean        true已登陆且为某个社团的负责人，false未登陆或非某个社团的负责人
     */
    public static function isPrincipal($token)
    {
        $user = DB::table('user')
                  ->join('association_member', 'user.uid', '=', 'association_member.uid')
                  ->where('user.token', $token)
                  ->where('user.ban_flag', 0)
                  ->whereIn('association_member.level', [1,2,3])
                  ->first();
        if ($user) {
            return $user;
        } else {
            return false;
        }
    }
}
