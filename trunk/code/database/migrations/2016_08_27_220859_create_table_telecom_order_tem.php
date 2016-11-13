<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTelecomOrderTem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telecom_order_tem', function (Blueprint $table) {        
	        $table->increments('id');
	        $table->integer('uid');
	        $table->string('telecom_trade_no',50)->comment("���׶�����");
	        $table->string('trade_no',50)->comment('֧�����׺�');	       	
	       	$table->decimal('fee',10,2)->comment('���');
	       	$table->string('telecom_phone',20)->comment('�����ֻ�����');
	       	$table->string('telecom_outOrderNumber',20)->comment('���õ绰');
	       	$table->char('idcard',18)->comment('���֤');
	       	$table->string('name',20)->comment('����');
	       	$table->string('major')->comment('רҵ');
	       	$table->string('dormitory_no',20)->comment('�����');
	       	$table->string('student_id')->comment('ѧ��');
	       	$table->tinyInteger('pay_status')->unsigned()->comment('֧��״̬.0,δ����;1,�Ѹ���;2,���˿�;');
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
        Schema::drop('telecom_order_tem');
    }
}
