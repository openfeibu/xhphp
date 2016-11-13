<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TradeAccount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      	Schema::create('trade_account', function (Blueprint $table) {
          	$table->increments('id');
          	$table->integer('uid');    
          	$table->string('out_trade_no',50)->comment('交易订单号');
          	$table->string('trade_no',50)->comment('支付交易号');          	      	
          	$table->string('from',30)->comment('来源表');
          	$table->string('trade_status',20)->comment('交易状态 : success：支付成功；refunding：退款中；refunded：已退款 ；income：已存入钱包;cashing:提现中；cashed:已提现');
          	$table->string('trade_type',20)->comment('交易类型: ReleaseTask:发布任务;AcceptTask:接受任务;CancelTask:取消任务;Withdrawals:提现;Shopping:购物;TelecomOrder:电信套餐 ');
          	$table->tinyInteger('pay_id')->comment('支付方式id');
          	$table->decimal('fee',10,2)->comment('交易金额');
          	$table->decimal('service_fee',10,2)->comment('已收服务费');
          	$table->string('description')->comment('备注');
          	$table->tinyInteger('wallet_type')->comment('1.收入；-1支出；');
          	$table->index('uid');
          	$table->index('out_trade_no');
          	$table->index('trade_no');
          	$table->index('pay_id');
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
        Schema::dropIfExists('trade_account');
    }
}
