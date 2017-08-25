<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableEducation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('education',function(Blueprint $table){
			$table->increments('edu_id');
            $table->string('logo_url')->comment('logo') ;
            $table->string('img_url')->comment('驾校图片') ;
            $table->string('name',10)->comment('驾校名称') ;
            $table->string('desc',255)->comment('描述');
            $table->text('content')->comment('详情');
            $table->string('tell',15)->comment('电话');
            $table->integer('uid')->unsigned() ;
            $table->timestamps();
		});
        Schema::create('education_product',function(Blueprint $table){
			$table->increments('product_id');
            $table->string('name',10)->comment('名称') ;
            $table->string('desc',255)->comment('描述');
            $table->integer('price')->comment('价格');
            $table->integer('original_price')->comment('原价');
            $table->integer('edu_id')->unsigned() ;
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
        Schema::drop('education');
        Schema::drop('education_product');
    }
}
