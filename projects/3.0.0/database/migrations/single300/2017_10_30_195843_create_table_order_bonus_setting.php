<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableOrderBonusSetting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_bonus_setting',function(Blueprint $table){
			$table->increments('id');
            $table->integer('number')->unsign()->comment('接单数') ;
            $table->decimal('bonus',10,2)->unsign()->comment('奖金');
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
