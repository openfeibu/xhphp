<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTopicCommentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topic_comment', function($table) {
            $table->increments('tcid');
            $table->integer('tid')->comment('话题ID');
            $table->integer('uid')->comment('用户ID');
            $table->integer('cid')->default(0)->comment('被评论的评论ID：0评论话题，非0评论他人的评论');
            $table->string('cid_username')->default('')->comment('被评论的用户的用户名');
            $table->text('content')->comment('内容');
            $table->integer('favourites_count')->default(0)->comment('点赞量');
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
        Schema::drop('topic_comment');
    }
}
