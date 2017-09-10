<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTelecomEnrollment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telecom_enrollment',function(Blueprint $table){
			$table->increments('enroll_id');
            $table->integer('uid')->unsigned()->comment('用户ID') ;
            $table->integer('time_id')->unsigned()->comment('报名id') ;
            $table->string('name',50)->comment('姓名') ;
            $table->date('date')->comment('报名日期');
            $table->time('time_start')->comment('报名时间开始');
            $table->time('time_end')->comment('报名时间结束');
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
        Schema::drop('telecom_enrollment');
    }
}
