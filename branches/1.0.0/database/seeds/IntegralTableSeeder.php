<?php

use Illuminate\Database\Seeder;

class IntegralTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('integral')->insert(
             array(
                 array(
                    'obtain_type' => '完善我的信息',
                    'score' => +2
                  ),
                  array(
                    'obtain_type' => '推荐给好友',
                    'score' => +1
                  ),
                  array(
                    'obtain_type' => '发布任务',
                    'score' => +2
                  ),
                  array(
                    'obtain_type' => '完成任务',
                    'score' => +2
                  ),
                  array(
                    'obtain_type' => '每日登录签到',
                    'score' => +1
                  ),
                  array(
                    'obtain_type' => '实名认证',
                    'score' => +5
                  ),
                  array(
                    'obtain_type' => '取消任务',
                    'score' => -2
                  ),
                  array(
                    'obtain_type' => '被投诉且属实',
                    'score' => -5
                  ),
                  array(
                    'obtain_type' => '恶意刷单、接单',
                    'score' => '限制发、接任务2天'
                  ),
                  array(
                    'obtain_type' => '涉及发布黄赌毒等',
                    'score' => '积分清零+封号（时间视情况而定）'
                  ),
                  array(
                    'obtain_type' => '发布广告任务或图片',
                    'score' => '积分清零+封号（时间视情况而定）'
                  ),
             ));
    }
}
