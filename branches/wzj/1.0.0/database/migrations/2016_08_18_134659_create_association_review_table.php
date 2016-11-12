<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssociationReviewTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('association_review', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('uid');
            $table->integer('aid')->default(0);
			$table->text('causes')->comment('申请理由');
            $table->string('ar_username')->comment('申请人名字');
			$table->string('profession',50)->comment('专业');
			$table->string('mobile_no',20)->comment('常用手机号码');
            $table->string('status', 20)->default('checking')->comment('状态:checking审核中,passed审核通过,rejected审核不通过');
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
        Schema::drop('association_review');
    }
}
