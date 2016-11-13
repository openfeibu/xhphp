<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message', function (Blueprint $table) {
            $table->increments('mid');
            $table->string('uid_receiver', 32)->comment('收信人ID');
            $table->string('aid_sender', 32)->comment('发信人ID');
            $table->string('type', 20)->comment('类型');
            $table->string('name', 20)->comment('名字');
            $table->string('content')->comment('内容');
            $table->softDeletes();
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
        Schema::drop('message');
    }
}
