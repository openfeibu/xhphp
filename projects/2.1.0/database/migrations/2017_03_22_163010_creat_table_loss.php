<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatTableLoss extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loss',function(Blueprint $table){
			$table->increments('loss_id');
			$table->integer('uid')->unsigned() ;
			$table->integer('college_id')->default('1')->unsigned()->comment('学校id') ;
            $table->string('mobile','20')->comment('联系方式') ;
			$table->string('content','255')->comment('内容');
            $table->string('img','255')->comment('图片');
            $table->string('thumb','255')->comment('缩略图');
            $table->char('type','10')->comment('类型：lose：丢失；found:寻找');
            $table->integer('cat_id')->comment('分类id');
			$table->timestamps();
            $table->softDeletes();
            $table->index(['uid','loss_id']);
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('loss');
    }
}
