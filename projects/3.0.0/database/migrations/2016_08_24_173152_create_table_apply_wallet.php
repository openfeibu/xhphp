<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableApplyWallet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apply_wallet',function(Blueprint $table){
			$table->increments('apply_id');
			$table->integer('uid')->unsigned() ;
			$table->string('out_trade_no',50)->comment('交易订单号');
			$table->string('alipay',50)->default('')->comment('支付宝账号');
            $table->string('alipay_name',50)->default('')->comment('支付宝姓名');
			$table->decimal('fee',10,2)->comment('提现金额');
			$table->decimal('service_fee',10,2)->comment('手续费');
			$table->decimal('total_fee',10,2)->comment('总费用');
			$table->string('status',20)->comment('状态:success:已转账；wait:待操作；failed：申请失败');
            $table->enum('type',['common','quick'])->default('common')->comment('普通提现');
			$table->string('descirption')->comment('备注');
			$table->timestamps();
			$table->index('uid');
			$table->index('out_trade_no');

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
