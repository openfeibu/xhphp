<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRealNameAuthTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('real_name_auth', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('uid');
            $table->text('pic1');
            $table->text('pic2');
            $table->string('name')->comment('姓名');
            $table->string('ID_Number')->comment('身份证号');
            $table->string('status')->default('reviewing')->comment('状态:reviewing审核中,passed审核通过,invalid审核不通过');
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
        Schema::drop('real_name_auth');
    }
}
