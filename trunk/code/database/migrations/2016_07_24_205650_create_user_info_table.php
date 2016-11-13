<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_info', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('uid')->comment('用户ID');
            $table->tinyInteger('gender')->default(0)->comment('性别:0保密,1男,2女');
            $table->tinyInteger('college_id')->default(0)->comment('所在学校ID');
            $table->string('student_id')->default('')->comment('学号');
            $table->integer('enrollment_year')->comment('入学年份');
            $table->integer('birth_year')->comment('生日（年）');
            $table->integer('birth_month')->comment('生日（月）');
            $table->integer('birth_day')->comment('生日（日）');
            $table->integer('favourites_count')->default(0)->comment('点赞数');
            $table->string('introduction')->default('这个家伙很懒，什么都没留下。')->comment('个人简介');
            $table->string('realname', 50)->default('')->comment('真实姓名');
            $table->string('ID_Number', 18)->default('')->comment('身份证号码');
            $table->string('alipay',50)->default('')->comment('支付宝账号');
            $table->string('alipay_name',50)->default('')->comment('支付宝姓名');$table->tinyInteger('is_author')->default(0)->comment('是否是小编，1为小编，0为读者');      
            $table->text('pic1')->default('')->comment('身份证照片1');
            $table->text('pic2')->default('')->comment('身份证照片2');
            $table->text('address')->default('')->comment('地址');
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
        Schema::drop('user_info');
    }
}
