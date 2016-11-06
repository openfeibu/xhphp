<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccusationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('accusation', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('oid')->comment('订单的id');
            $table->integer('complainant_id')->comment('投诉用户的id');
            $table->string('content',200)->comment('投诉内容');
            $table->string('type',30)->comment('用户联系方式');
            $table->string('state',30)->default('审核中')->comment('投诉的状态');
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
        Schema::drop('accusation');
    }
}
