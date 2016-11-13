<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFavouriteHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('favourite_history', function (Blueprint $table) {
            $table->increments('fid');
            $table->integer('uid')->comment('点赞用户ID');
            $table->integer('to_uid')->comment('被点赞用户ID');
            $table->tinyInteger('del_flag')->default(0)->comment('删除：0正常，1删除');
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
        Schema::drop('favourite_history');
    }
}
