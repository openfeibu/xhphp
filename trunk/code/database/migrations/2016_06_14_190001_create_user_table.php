<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
            $table->increments('uid');
            $table->string('openid', 32)->comment('公开ID');
            $table->string('mobile_no', 20)->comment('手机号码');
            $table->string('password', 100)->comment('密码');
            $table->string('token', 100)->comment('令牌');
            $table->string('nickname', 50)->comment('昵称');
            $table->string('avatar_url', 255)->default('')->comment('头像链接');
            $table->string('created_ip', 15)->comment('注册IP');
            $table->string('last_ip', 15)->default('0.0.0.0')->comment('上次登陆IP');
            $table->tinyInteger('ban_flag')->default(0)->comment('1:封号；0:正常');
            $table->integer('integral')->default(0)->comment('用户的总积分');
            $table->integer('today_integral')->default(0)->comment('用户今日的积分');
            $table->decimal('wallet',10,2)->default(0)->comment('钱包');
            $table->timestamps();

            $table->unique(['mobile_no', 'token', 'nickname']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user');
    }
}
