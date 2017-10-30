<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableOrderBonus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_bonus',function(Blueprint $table){
			$table->increments('id');
            $table->integer('uid')->unsign()->comment('用户ID') ;
            $table->integer('number')->unsign()->comment('接单数') ;
            $table->decimal('bonus',10,2)->unsign()->comment('奖金');
            $table->date('date')->comment('日期');
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
        Schema::drop('order_bonus');
    }
}
