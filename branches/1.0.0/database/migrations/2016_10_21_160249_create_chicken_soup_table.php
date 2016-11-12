<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChickenSoupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chicken_soup', function (Blueprint $table) {
            $table->increments('csid');
            $table->integer('uid')->default(0)->comment('发鸡汤用户id,默认为0，即系统');
            $table->stirng('title')->comment('鸡汤标题');
			$table->stirng('background_url')->comment('背景图片');
			$table->text('content')->comment("鸡汤内容");
			$table->integer('status')->default(0)->comment('鸡汤状态，0为正在审核中，1为审核通过，2为审核失败');
			$table->integer('view_num')->default(0);
			$table->softDeletes();
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
