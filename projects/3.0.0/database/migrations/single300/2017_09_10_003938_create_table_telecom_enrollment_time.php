<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTelecomEnrollmentTime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telecom_enrollment_time',function(Blueprint $table){
			$table->increments('time_id')->unsigned();
            $table->time('time_start')->comment('报名时间开始');
            $table->time('time_end')->comment('报名时间结束');
            $table->integer('count') ->comment('可报名人数');
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
        Schema::drop('telecom_enrollment_time');
    }
}
