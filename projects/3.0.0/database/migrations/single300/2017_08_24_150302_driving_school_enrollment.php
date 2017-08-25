<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DrivingSchoolEnrollment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('driving_school_enrollment',function(Blueprint $table){
            $table->increments('enroll_id');
            $table->string('name',10)->comment('名称') ;
            $table->string('mobile',20)->comment('手机');
            $table->string('content')->default('')->comment('留言内容');
            $table->enum('status',['succ','canceled'])->default('succ')->comment('取消状态');
            $table->integer('uid')->unsigned();
            $table->integer('ds_id')->unsigned() ;
            $table->integer('product_id')->unsigned() ;
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
        Schema::drop('driving_school_enrollment');
    }
}
