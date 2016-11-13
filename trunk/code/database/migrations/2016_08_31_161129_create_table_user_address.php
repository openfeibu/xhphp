<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserAddress extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_address', function (Blueprint $table) {  
        	$table->increments('address_id');
        	$table->integer('uid');
        	$table->string('consignee',60)->comment('�ջ�������');
        	$table->string('mobile',20)->comment('�ջ����ֻ�');
        	$table->string('address')->comment('�ջ���ַ');
        	$table->tinyInteger('is_default')->comment('�Ƿ�Ĭ��');
        	$table->timestamps();
        	$table->index('uid');       	
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
