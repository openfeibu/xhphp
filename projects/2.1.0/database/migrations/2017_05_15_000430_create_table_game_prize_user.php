<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableGamePrizeUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_prize_user',function(Blueprint $table){
			$table->increments('id');
            $table->integer('uid')->comment('用户ID') ;
            $table->integer('game_id')->comment('活动ID') ;
            $table->string('prize_name')->comment('奖品名称');
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
        //
    }
}
