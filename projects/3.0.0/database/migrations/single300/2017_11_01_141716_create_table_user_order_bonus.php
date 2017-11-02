<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserOrderBonus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_order_bonus',function(Blueprint $table){
			$table->increments('id');
            $table->integer('uid')->unsign()->comment('用户id') ;
            $table->integer('number')->unsign()->comment('接单数');
            $table->decimal('bonus',10,2)->unsign()->comment('奖金');
            $table->date('date')->comment('日期');
            $table->enum('status',['0','1'])->comment('状态，0:未结算，1.已结算');
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
        Schema::drop('order_bonus_setting');
    }
}
