<?php

namespace App\Listeners\Integral;

use DB;
use App\Integral;
use App\Integral_history;
use App\Events\Integral\Integrals;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\MessageService;
use App\Services\UserService;
use App\Repositories\IntegralRepository;

class IntegralsListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    protected $messageService;

    protected $userService;

    protected $integralRepository;

    function __construct(MessageService $messageService,
                        UserService $userService,
                        IntegralRepository $integralRepository)
    {
        $this->messageService = $messageService;
        $this->userService = $userService;
        $this->integralRepository = $integralRepository;
    }

    public function action_type($action)
    {
        if ($action=="完善我的信息") {
            return $integral_id = 1;
        } else if($action=="推荐给好友") {
            return $integral_id = 2;
        } else if($action=="发布任务") {
            return $integral_id = 3;
        } else if($action=="完成任务") {
            return $integral_id = 4;
        } else if($action=="每日登录签到") {
            return $integral_id = 5;
        } else if($action=="实名认证") {
            return $integral_id = 6;
        } else if($action=="被投诉且属实") {
            return  $integral_id = 7;
        } else if($action=="取消任务") {
            return  $integral_id = 8;
        }
    }
    /**
     * Handle the event.
     *
     * @param  LoginIntegral  $event
     * @return void
     */
    public function handle(Integrals $event)
    {
        $user = $this->userService->getUser();
        $action = $event->action;
        $integral_id = $this->action_type($action);
        if($user->today_integral < 10){
            $first_logins = Integral_history::where('uid',$user->uid)
                                                ->where('integral_id',$integral_id)
                                                ->get();

            if($integral_id == 5){
                foreach ($first_logins as $key => $first_login) {
                    $time = date("Y-m-d",strtotime($first_login->created_at));
                    if($time == date("Y-m-d")){
                        return true;
                    }
                }
            }elseif($integral_id == 2){
                $share_count = 0;
                foreach ($first_logins as $key => $first_login) {
                    $time = date("Y-m-d",strtotime($first_login->created_at));
                    if($time == date("Y-m-d")){
                        $share_count += 1;
                        if($share_count>2){
                            return true;
                        } 
                    }
                }
            }

            try {
                DB::beginTransaction();

                $integral = Integral::where('id',$integral_id)->first();
                $user_integral = $user->integral + $integral->score;
                if(($user->today_integral + $integral->score)>=10){
                    $today_integral = 10;
                    $user_integral = $user->integral+(10-$user->today_integral);
                } else {
                    $today_integral = $user->today_integral + $integral->score;
                }

                $user->integral = $user_integral;
                $user->today_integral = $today_integral;
                $user->save();

                //根据用户行为加对应的积分
                $this->integralRepository->add_history($user->uid,$integral->id);

                $content = $action."加".$integral->score."积分";
                $this->messageService->SystemMessage2SingleOne($user->uid, $content);


                DB::commit();
                return true;
            } catch (Exception $e) {
                DB::rollBack();
                return false;
            }
        }
    }
}
