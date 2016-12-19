<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableGoodsCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_category',function(Blueprint $table){
			$table->increments('cat_id');
			$table->integer('uid')->unsigned() ;
			$table->integer('shop_id')->unsigned() ;
            $table->string('cat_name')->comment('·ÖÀàÃû³Æ');
            $table->integer('parent_id')->default(0)->comment('¸¸ID');
            $table->tinyInteger('sort')->default(50)->comment('ÅÅÐò');
            $table->index(['uid','shop_id','parent_id']);
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('goods_category');
    }
}
