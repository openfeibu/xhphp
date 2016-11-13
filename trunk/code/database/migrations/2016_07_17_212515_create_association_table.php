<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssociationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('association', function (Blueprint $table) {
            $table->increments('aid');
            $table->string('aname', 100)->comment('社团名称');
            $table->string('avatar_url', 255)->default('')->comment('头像链接');
			$table->string('background_url', 255)->default('')->comment('背景图片');
            $table->integer('member_number')->comment('社团人数');
            $table->string('introduction')->default('这个家伙很懒，什么都没留下。')->comment('社团简介');
			$table->string('leader', 50)->comment('社长名称');
			$table->string('label', 100)->comment('社团标签');
            $table->string('superior', 50)->comment('上级部门');
            $table->timestamps();

            $table->unique('aname');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('association');
    }
}
