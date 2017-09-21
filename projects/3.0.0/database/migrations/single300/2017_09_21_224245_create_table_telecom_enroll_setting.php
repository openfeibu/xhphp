<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTelecomEnrollSetting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telecom_enroll_setting',function(Blueprint $table){
			$table->increments('setting_id')->unsigned();
            $table->integer('campus_id')->unsigned();
            $table->integer('count')->unsigned()->comment('数量');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('telecom_enroll_setting');
    }
}
