<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTelecomPackage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telecom_package', function (Blueprint $table) {
	        $table->increments('package_id');
	        $table->smallInteger('sort')->comment('排序');
	        $table->string('package_name',50)->comment("套餐名称");
	        $table->string('package_detail')->comment('套餐详情');
	        $table->decimal('package_price',10,2)->comment('套餐价格');
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
        Schema::drop('telecom_package');
    }
}
