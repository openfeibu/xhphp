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
            $table->integer('count') ->comment('已报名人数');
            $table->integer('time_id') ->comment('时间段ID');
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
        //
    }
}
