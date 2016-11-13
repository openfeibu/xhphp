<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableWalletAccount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallet_account',function(Blueprint $table){
			$table->increments('id');			
			$table->integer('uid')->unsigned() ;
			$table->string('out_trade_no',50)->comment('交易订单号');
			$table->tinyInteger('wallet_type')->comment('1.收入；-1支出；');			
			$table->decimal('fee',10,2)->comment('交易金额（到账，出账）');
			$table->decimal('service_fee',10,2)->comment('已收服务费');
			$table->decimal('wallet',10,2)->comment('钱包余额');	
			$table->string('trade_type',20)->comment('交易类型: ReleaseTask:发布任务;AcceptTask:接受任务;CancelTask:取消任务;Withdrawals:提现;Shopping:购物; ');	
			$table->string('description')->comment('备注');
			$table->timestamps();
			$table->index('uid');
			$table->index('out_trade_no');
			$table->index('wallet_type');
			
	    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wallet_account');
    }
}
