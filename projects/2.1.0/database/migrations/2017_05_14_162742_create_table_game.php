<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableGame extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game',function(Blueprint $table){
			$table->increments('id');
            $table->string('name')->comment('活动名称') ;
            $table->string('title')->comment('活动名称') ;
            $table->tinyInteger('status')->comment('状态：0：关闭，1：开启');
            $table->timestamp('starttime')->comment('开始时间');
            $table->timestamp('endtime')->comment('结束时间');
            $table->integer('count')->comment('参与人数量');
            $table->integer('num')->comment('参与数量');
            $table->timestamps();
            $table->softDeletes();
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('game');
    }
}
