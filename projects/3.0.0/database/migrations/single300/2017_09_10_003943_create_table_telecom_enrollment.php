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
            $table->string('name',50)->comment('姓名') ;
            $table->date('date')->comment('报名日期');
            $table->char('dormitory_number',20)->comment('宿舍号') ;
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
