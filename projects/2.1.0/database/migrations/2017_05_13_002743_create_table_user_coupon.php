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
            $table->integer('coupon_id')->comment('优惠券ID') ;
            $table->timestamp('overdue')->comment('过期时间');
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
