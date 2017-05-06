<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetworkReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('network_report', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('uid')->default('');
            $table->stirng('account')->comment('学号');
            $table->string('password')->comment('密码');
            $table->tinyInteger('remember')->default(0)->comment('是否绑定');
            $table->string('ip', 15)->comment('IP地址');
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
        Schema::drop('network_report');
    }
}
