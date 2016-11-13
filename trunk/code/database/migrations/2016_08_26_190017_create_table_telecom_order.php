<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTelecomOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telecom_order', function (Blueprint $table) {        
	        $table->increments('id');
	        $table->integer('uid');
	        $table->string('telecom_trade_no',50)->comment("交易订单号");
	        $table->string('trade_no',50)->comment('支付交易号');	       	
	       	$table->decimal('fee',10,2)->comment('金额');
	       	$table->string('telecom_phone',20)->comment('电信手机号码');
	       	$table->string('telecom_outOrderNumber',20)->comment('常用电话');
	       	$table->char('idcard',18)->comment('身份证');
	       	$table->string('name',20)->comment('姓名');
	       	$table->string('major')->comment('专业');
	       	$table->string('dormitory_no',20)->comment('宿舍号');
	       	$table->string('student_id')->comment('学号');
	       	$table->tinyInteger('pay_status')->unsigned()->comment('支付状态.0,未付款;1,已付款;2,已退款;');
	       	$table->integer('package_id');
	       	$table->string('package_name');
	       	$table->index('package_id');
	       	$table->index('uid');
	       	$table->index('trade_no');
	       	$table->index('telecom_trade_no');
	       	$table->index('pay_status');
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
        Schema::drop('telecom_order');
    }
}
