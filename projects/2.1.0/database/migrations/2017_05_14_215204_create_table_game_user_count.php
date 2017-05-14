<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableGameUserCount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_user_count',function(Blueprint $table){
			$table->increments('guc_id');
            $table->integer('uid')->comment('用户ID') ;
            $table->integer('game_id')->comment('活动ID');
            $table->integer('num')->comment('参与次数');
            $table->integer('count')->comment('总参与次数');
            $table->timestamp('lasttime')->comment('上次参与时间');
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
