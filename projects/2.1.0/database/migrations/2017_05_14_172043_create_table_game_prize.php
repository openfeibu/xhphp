<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableGamePrize extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_coupon_prize',function(Blueprint $table){
			$table->increments('prize_id');
            $table->integer('coupon_id')->comment('优惠券ID') ;
            $table->integer('prize_value')->comment('概率');
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
