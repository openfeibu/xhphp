<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableShippingConfig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipping_config',function(Blueprint $table){
			$table->increments('cid');
			$table->integer('min')->comment('最低消费') ;
            $table->integer('max')->comment('最高消费') ;
			$table->float('weight','10','6')->comment('限制重量');
            $table->decimal('outweight','10','2')->comment('超出 元/千克');
            $table->decimal('shipping_fee','10','2')->comment('配送费');
            $table->enum('payer', ['seller', 'buyer'])->comment('支付者(seller：卖家，buyer：买家)');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('shipping_config');
    }
}
