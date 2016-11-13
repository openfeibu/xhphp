<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVerifyCodeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('verify_code', function (Blueprint $table) {
            $table->increments('id');
            $table->string('mobile_no', 20)->comment('手机号码');
            $table->string('verify_code', 10)->comment('验证码');
            $table->string('usage', 50)->comment('用途:reg注册，forget找回密码，reset重置密码');
            $table->tinyInteger('is_send')->comment('是否成功发送：0失败，1成功');
            $table->tinyInteger('is_used')->default(0)->comment('是否已使用：0否，1是');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('verify_code');
    }
}
