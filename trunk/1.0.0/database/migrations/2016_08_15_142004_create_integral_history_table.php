<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIntegralHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('integral_history', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('uid')->comment('用户ID');
            $table->integer('integral_id')->comment('获取积分的类型');
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
        Schema::drop('integral_history');
    }
}
