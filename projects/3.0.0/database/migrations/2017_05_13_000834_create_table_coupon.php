<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCoupon extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupon',function(Blueprint $table){
			$table->increments('coupon_id');
            $table->string('coupon_code')->comment('优惠码');
			$table->integer('price')->comment('价格') ;
            $table->integer('min_price')->comment('最低使用价格') ;
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
        Schema::drop('coupon');
    }
}
