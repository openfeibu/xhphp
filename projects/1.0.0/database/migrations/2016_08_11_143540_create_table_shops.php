<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableShops extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop',function(Blueprint $table){
			$table->increments('shop_id');
			$table->integer('uid')->unsigned() ;
            $table->string('shop_name',120)->unique()->comment('店名');
			$table->string('shop_img');
            $table->text('description');
            $table->string('address');
            $table->integer('college_id')->default(0)->comment('所在学校ID');
            $table->tinyInteger('shop_type')->default(1)->comment('1.学生.2.商家');
            $table->tinyInteger('shop_status')->default(0)->comment('0.待审核;1.正常;2.审核不通过;3.关闭');
            $table->integer('shop_favorite_count')->default(0)->comment('收藏数');
            $table->integer('shop_click_count')->default(0)->comment('浏览数');
            $table->tinyInteger('top')->default(0)->comment('是否置顶');
            $table->decimal('shipping_fee',10,2)->comment('运费');
            $table->decimal('min_goods_amount',10,2)->comment('满多少免运费');
            $table->timestamps();
			$table->foreign('uid')->references('id')->on('user')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->index(['uid','shop_name','college_id']);
		});
		Schema::create('goods',function(Blueprint $table){
			$table->increments('goods_id');
			$table->integer('shop_id')->unsigned();
			$table->integer('cat_id')->unsigned();
			$table->integer('uid')->unsigned();
			$table->string('goods_name',120);
			$table->string('goods_sn',60);
			$table->decimal('goods_price',10,2)->unsigned();
			$table->integer('goods_click_count')->default(0)->unsigned()->comment('浏览量');
			$table->integer('goods_sale_count')->default(0)->unsigned()->comment('销量');
			$table->smallInteger('goods_number')->unsigned()->comment('库存');
			$table->text('goods_desc')->comment('商品描述');
			$table->string('goods_img')->comment('商品图片');
			$table->string('goods_thumb')->comment('商品图片');
			$table->tinyInteger('is_on_sale')->default('1')->unsigned()->comment('在售');
			$table->tinyInteger('top')->default(0)->comment('是否置顶');
			$table->foreign('shop_id')->references('shop_id')->on('shop')
                ->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('uid')->references('id')->on('user')
                ->onUpdate('cascade')->onDelete('cascade');
			$table->timestamps();
			$table->index('goods_name');
			$table->index('goods_sn');
		});
		Schema::create('cart',function(Blueprint $table){
			$table->increments('cart_id');
			$table->integer('uid')->unsigned() ;
			$table->integer('goods_id')->unsigned();
			$table->integer('shop_id')->unsigned();
			$table->string('goods_name',120);
			$table->decimal('goods_price',10,2)->unsigned();
			$table->smallInteger('goods_number')->default(1)->unsigned();
			$table->timestamps();
			$table->foreign('uid')->references('id')->on('user')
                ->onUpdate('cascade')->onDelete('cascade');
			$table->index('goods_id');
			$table->index('shop_id');
	    });
		Schema::create('order_info',function(Blueprint $table){
			$table->increments('order_id');
			$table->string('order_sn',50)->unique();
			$table->integer('uid')->unsigned();
			$table->integer('shop_id')->unsigned();
			$table->tinyInteger('order_status')->unsigned()->comment('作何操作.0，未确认；1，已确认；2，已取消；3，无效；4，退货；');
			$table->tinyInteger('pay_status')->unsigned()->comment('支付状态.0,未付款;1,已付款;2,已退款;');
			$table->tinyInteger('shipping_status')->unsigned()->comment('发货状态; 0未发货; 1已发货  2已取消  3备货中');
			$table->string('consignee',60)->comment('收货姓名');
			$table->string('address')->comment('收货地址');
			$table->string('mobile',60)->comment('联系电话');
			$table->string('email',60)->comment('邮箱');
			$table->string('postscript')->comment('留言');
			$table->tinyInteger('pay_id')->unsigned()->comment('支付方式id');
			$table->string('pay_name',60)->comment('支付名称');
			$table->timestamp('pay_time')->comment('支付时间');
			$table->decimal('goods_amount',10,2)->comment('商品总金额');
			$table->decimal('shipping_fee',10,2)->comment('配送费用');
			$table->decimal('insure_fee',10,2)->comment('保价费用');
			$table->string('to_buyer')->comment('商家给买家留言');
			$table->timestamps();		
			$table->index('uid');
			$table->index('shop_id');
			$table->index('order_status');
			$table->index('pay_status');
			$table->index('pay_id');
			
		});
		Schema::create('order_goods',function(Blueprint $table){
			$table->increments('id');
			$table->integer('order_id')->unsigned();
			$table->integer('goods_id')->unsigned();
			$table->string('goods_sn',60);
			$table->string('goods_name',60);
			$table->integer('goods_number')->unsigned();		
			$table->decimal('goods_price',10,2);
			$table->foreign('order_id')->references('order_id')->on('order_info')
                ->onUpdate('cascade')->onDelete('cascade');
			$table->index('order_id');
			$table->index('goods_id');
			$table->index('goods_name');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop');
        Schema::dropIfExists('shop_good');
		Schema::dropIfExists('goods');
		Schema::dropIfExists('cart');
		Schema::dropIfExists('order_info');
		Schema::dropIfExists('order_goods');
    }
}
