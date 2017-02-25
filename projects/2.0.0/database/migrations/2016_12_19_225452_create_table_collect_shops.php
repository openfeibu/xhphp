<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCollectShops extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('collect_shops',function(Blueprint $table){
			$table->increments('id');
			$table->integer('uid')->unsigned() ;
			$table->integer('shop_id')->unsigned() ;
			$table->timestamps();
            $table->index(['uid','shop_id']);
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
