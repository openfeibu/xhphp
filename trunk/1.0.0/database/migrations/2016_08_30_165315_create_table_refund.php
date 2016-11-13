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
	        $table->string('batch_no',50)->comment('�˿����κ�');
	        $table->int('batch_num')->commnet('ԭ�˿���');
	        $table->int('success_num')->comment('�ɹ��˿���');
	        $table->text('detail_data')->comment('ԭ�˿�����');
	        $table->text('result_details')->comment('�ɹ��˿�����');
	        $table->string('refund_status',20)->comment('�˿�״̬��δ�ύ��wait���ɹ���success');
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
