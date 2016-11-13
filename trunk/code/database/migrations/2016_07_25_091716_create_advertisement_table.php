<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdvertisementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advertisement', function (Blueprint $table) {
            $table->increments('adid');
            $table->string('ad_url')->comment('广告链接');
            $table->string('ad_image_url')->comment('广告图片链接');
            $table->string('title',60)->comment('标题');
            $table->string('description')->comment('广告简介');
            $table->tinyInteger('rank')->default(99)->comment('排名');
            $table->softDeletes();
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
        Schema::drop('advertisement');
    }
}
