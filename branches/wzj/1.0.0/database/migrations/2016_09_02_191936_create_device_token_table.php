<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeviceTokenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('device_token', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('uid');
            $table->string('platform', 10);
            $table->string('push_server', 10)->default('xinge')->comment('推送服务商');
            $table->string('device_token');
            $table->softDeletes();
            $table->timestamps();

            $table->unique('device_token');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('device_token');
    }
}
