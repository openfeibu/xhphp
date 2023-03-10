<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTelecomEnrollmentCount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telecom_enrollment_count',function(Blueprint $table){
			$table->increments('cid')->unsigned();
            $table->date('date')->comment('预约日期');
            $table->integer('count')->unsigned()->comment('已报名人数');
            $table->integer('campus_id')->unsigned()->comment('校区id');
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
        Schema::drop('telecom_enrollment_count');
    }
}
