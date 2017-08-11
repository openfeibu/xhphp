<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatTableLossCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loss_category',function(Blueprint $table){
			$table->increments('cat_id');
            $table->string('cat_name','255')->comment('联系方式') ;
            $table->tinyInteger('sort')->default('0')->comment('排序');
            $table->index(['cat_id']);
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('loss_category');
    }
}
