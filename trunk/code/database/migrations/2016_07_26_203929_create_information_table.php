<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInformationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('information', function (Blueprint $table) {
            $table->increments('iid');
            $table->integer('aid')->comment('社团ID');
            $table->integer('uid')->comment('发布者ID');
            $table->string('title', 100)->comment('资讯标题');
            $table->text('content')->comment('资讯内容');
            $table->integer('view_num')->default(1)->comment('阅读量');
            $table->text('img_url')->comment('图片');
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
        Schema::dropIfExists('information');
    }
}
