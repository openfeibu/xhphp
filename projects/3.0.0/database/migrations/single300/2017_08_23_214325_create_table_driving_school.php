<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDrivingSchool extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driving_school',function(Blueprint $table){
			$table->increments('ds_id');
            $table->string('logo_url')->comment('logo') ;
            $table->string('img_url')->comment('驾校图片') ;
            $table->string('name',10)->comment('驾校名称') ;
            $table->string('desc',255)->comment('描述');
            $table->text('content')->comment('详情');
            $table->string('tell',15)->comment('电话');
            $table->integer('uid')->unsigned() ;
            $table->timestamps();
		});
        Schema::create('driving_school_product',function(Blueprint $table){
			$table->increments('product_id');
            $table->string('name',10)->comment('名称') ;
            $table->string('desc',255)->comment('描述');
            $table->integer('price')->comment('价格');
            $table->integer('original_price')->comment('原价');
            $table->integer('ds_id')->unsigned() ;
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
        Schema::drop('driving_school');
        Schema::drop('driving_school_product');
    }
}
