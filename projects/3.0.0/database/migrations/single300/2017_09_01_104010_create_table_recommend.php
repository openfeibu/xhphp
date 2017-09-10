<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableRecommend extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recommend',function(Blueprint $table){
			$table->increments('id');
            $table->string('img')->comment('图片') ;
            $table->string('url')->comment('链接') ;
            $table->string('and_url')->comment('安卓链接') ;
            $table->string('name')->comment('名称');
            $table->tinyInteger('sort')->comment('排序');
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
        Schema::drop('recommend');
    }
}
