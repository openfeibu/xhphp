<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTopicTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topic', function (Blueprint $table) {
            $table->increments('tid');
            $table->integer('uid')->comment('作者ID');
            $table->string('type', 50)->comment('类型');
            $table->text('content')->comment('内容');
            $table->text('img')->default('')->comment('图片');
            $table->integer('view_num')->default(1)->comment('话题阅读量');
            $table->integer('comment_num')->default(0)->comment('话题评论量');
            $table->integer('favourites_count')->default(0)->comment('点赞量');
            $table->tinyInteger('is_top')->default(0)->comment('置顶：0普通，1置顶');
            $table->tinyInteger('admin_deleted')->default(0)->comment('是否被管理员删除：0否，1是');
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
        Schema::drop('topic');
    }
}
