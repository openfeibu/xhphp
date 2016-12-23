<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;

class VerifyCode extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'verify_code';

    public function user()
    {
    	return $this->belongsTo('App\User', 'user_id');
    }

    /**
     * 获取手机号码在XX时间前至今的请求短信发送数量
     *
     * @param  string $mobile_no 手机号码
     *
     * @return integer            短信发送数量
     */
    public static function getSMSCount($mobile_no)
    {
        $verifyCodeCount = DB::table('verify_code')
                                ->where('mobile_no', $mobile_no)
                                ->where('created_at', '>=', DB::raw('(select date_sub(now(), interval 30 MINUTE))'))
                                ->count();
        return $verifyCodeCount;
    }

    /**
     * 检测短信发送是否频繁
     *
     * @param  string  $mobile_no 手机号码
     *
     * @return boolean            true频繁，false正常
     */
    public static function isTooManySendSMS($mobile_no)
    {
        if (self::getSMSCount($mobile_no) >= 5) {
            return true;
        } else {
            return false;
        }
    }
}