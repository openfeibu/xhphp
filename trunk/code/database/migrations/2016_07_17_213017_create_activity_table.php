<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity', function (Blueprint $table) {
            $table->increments('actid');
            $table->integer('aid')->comment('社团ID');
            $table->integer('uid')->comment('用户ID');
            $table->string('title', 100)->comment('活动标题');
            $table->text('content')->comment('活动内容');
            $table->timestamp('start_time')->comment('活动开始时间');
            $table->timestamp('end_time')->comment('活动结束时间');
            $table->string('place', 100)->comment('活动举办地点');
            $table->integer('view_num')->default(1)->comment('阅读量');
            $table->text('img_url')->default('')->comment('图片');
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
        Schema::drop('activity');
    }
}
