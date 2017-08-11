<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_info', function (Blueprint $table) {
            $table->increments('id');
            $table->string('mid', 500)->comment('设备ID');
            $table->integer('user_id')->default(0)->comment('用户ID');
            $table->string('version', 50)->comment('版本');
            $table->tinyInteger('platform')->comment('and:安卓，ios:苹果，pc:电脑，other:其他');
            $table->string('os', 50)->comment('系统版本');
            $table->string('brand', 50)->comment('时间品牌');
            $table->timestamps();

            $table->unique('mid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('client_info');
    }
}
