<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCollegeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('college', function (Blueprint $table) {
            $table->increments('cid');
            $table->string('name')->comment('校名');
            $table->string('location')->comment('所属地区');
            $table->string('address')->comment('学校地址');
            $table->integer('established_at')->comment('创办时间（年）');
            $table->tinyInteger('del_flag')->default(0)->comment('删除：0正常，1删除');
            $table->timestamps();

            $table->unique('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('college');
    }
}
