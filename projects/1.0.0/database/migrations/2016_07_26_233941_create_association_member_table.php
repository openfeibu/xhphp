<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssociationMemberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('association_member', function (Blueprint $table) {
            $table->increments('amid');
            $table->integer('aid')->comment('社团ID');
            $table->integer('uid')->comment('用户ID');
            $table->tinyInteger('level')->default(0)->comment('等级:0成员,1会长,2-3副会长');
			$table->timestamp('notice_view_at')->comment('最后一次阅读公告时间');
			$table->timestamp('member_view_at')->comment('最后一次阅读审核成员时间');
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
        Schema::drop('association_member');
    }
}
