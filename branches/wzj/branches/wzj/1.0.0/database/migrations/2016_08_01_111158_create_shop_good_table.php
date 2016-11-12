<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopGoodTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Schema::create('shop_good', function (Blueprint $table) {
        //    $table->increments('gid');
        //    $table->integer('sid')->comment('店铺ID');
        //    $table->string('good_name')->comment('商品名称');
        //    $table->text('good_introduction')->comment('商品介绍');
        //    $table->integer('good_price')->comment('商品价格');
        //    $table->text('good_image')->comment('商品图片');
        //    $table->integer('good_sales')->default(0)->comment('销售量');
        //    $table->integer('good_inventory')->comment('库存量');
        //    $table->string('good_status')->default('onSell')->comment('商品状态:onSell上架,offSell下架');
        //    $table->tinyInteger('del_flag')->default(0)->comment('删除：0正常，1删除');
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
        Schema::dropIfExists('shop_good');
    }
}
