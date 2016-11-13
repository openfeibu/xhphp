<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Schema::create('shop', function (Blueprint $table) {
        //    $table->increments('sid');
        //    $table->integer('uid')->comment('店主ID');
        //    $table->string('shop_name', 50)->comment('商店名');
        //    $table->text('shop_image')->comment('商店LOGO图');
        //    $table->string('shop_intruduction')->comment('店铺介绍');
        //    $table->integer('shop_favorite')->default(0)->comment('收藏数');
        //    $table->integer('shop_view_num')->default(0)->comment('浏览数');
        //    $table->string('shop_tag')->nullable()->comment('店铺标签');
        //    $table->tinyInteger('ban_flag')->default(0)->comment('封店:0正常,1封店');
        //    $table->timestamps();
        //});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop');
    }
}
