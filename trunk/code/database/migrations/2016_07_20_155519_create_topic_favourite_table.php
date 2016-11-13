<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTopicFavouriteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topic_favourite', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('uid')->comment('用户ID');
            $table->integer('tid')->default(0)->comment('话题ID');
            $table->integer('tcid')->default(0)->comment('话题评论ID');
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
        Schema::drop('topic_favourite');
    }
}
