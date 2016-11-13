<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTelecomRealName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       	Schema::create('telecom_real_name', function (Blueprint $table) {
	       	$table->increments('real_id');
	       	$table->integer('uid');
	       	$table->string('telecom_phone',20)->comment('电信手机');
	       	$table->string('telecom_iccid',6)->comment('电信卡iccid最后六位数');
	       	$table->string('telecom_outOrderNumber',20)->comment('常用电话');
	       	$table->index('uid');
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
        Schema::drop('telecom_real_name');
    }
}
