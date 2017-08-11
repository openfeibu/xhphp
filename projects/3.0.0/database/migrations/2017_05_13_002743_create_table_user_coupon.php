<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserCoupon extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_coupon',function(Blueprint $table){
			$table->increments('user_coupon_id');
			$table->integer('uid')->comment('用户ID') ;
            $table->date('receive')->comment('领取时间');
            $table->date('overdue')->comment('过期时间');
            $table->integer('price')->comment('价格') ;
            $table->integer('min_price')->comment('最低使用价格') ;
            $table->enum('status',['used','unused'])->comment('使用状态');
            $table->timestamps();
            $table->index('uid');
            $table->index('coupon_id');
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
        Schema::drop('user_coupon');
    }
}
