<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserAddress extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_address', function (Blueprint $table) {  
        	$table->increments('address_id');
        	$table->integer('uid');
        	$table->string('consignee',60)->comment('收货人姓名');
        	$table->string('mobile',20)->comment('收货人手机');
        	$table->string('address')->comment('收货地址');
        	$table->tinyInteger('is_default')->comment('是否默认');
        	$table->timestamps();
        	$table->index('uid');       	
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
    }
}
