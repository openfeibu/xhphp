<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableRefund extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alipay_refund', function (Blueprint $table) {        
	        $table->increments('refund_id');
	        $table->string('batch_no',50)->comment('退款批次号');
	        $table->int('batch_num')->commnet('原退款数');
	        $table->int('success_num')->comment('成功退款数');
	        $table->text('detail_data')->comment('原退款数据');
	        $table->text('result_details')->comment('成功退款数据');
	        $table->string('refund_status',20)->comment('退款状态：未提交：wait，成功：success');
	        $table->index('batch_no');
	        $table->index('refund_status');
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
        Schema::drop('alipay_refund');
    }
}
