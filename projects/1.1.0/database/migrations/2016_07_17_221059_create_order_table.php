<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order', function (Blueprint $table) {
            $table->increments('oid');
            $table->string('order_sn',50);
            $table->integer('owner_id')->comment('发单人ID');
            $table->integer('courier_id')->comment('接单人ID');
            $table->decimal('fee',10,0)->comment('小费');
            $table->string('alt_phone')->comment('联系方式');
            $table->string('description')->comment('订单描述');
            $table->string('destination')->comment('送达地点');
            $table->decimal('goods_fee',10,2)->comment('物品价格,选填');
            $table->decimal('service_fee',10,2)->comment('服务费,系统计算生成');
            $table->decimal('total_fee',10,2)->comment('总价');
            $table->string('type',20)->comment('类型:personal:个人，business:商家');
            $table->tinyInteger('pay_id')->comment('支付方式：1.支付宝,2,微信,3,余额');
            $table->tinyInteger('admin_deleted')->default(0)->comment('是否被管理员删除：0否，1是');
            $table->string('status')->default('waitpay')->comment('订单状态:waitpay,待支付,new可被接单,cancelling申请取消,cancelled已取消,accepted已被接单,finish接单人完成,completed发单人结算');
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
        Schema::drop('order');
    }
}
