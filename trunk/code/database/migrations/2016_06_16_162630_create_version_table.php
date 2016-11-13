<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVersionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('version', function (Blueprint $table) {
            $table->increments('id');
            $table->string('platform', 50)->comment('and:安卓，ios:苹果，pc:电脑，other:其他');
            $table->integer('code')->comment('版本号');
            $table->string('name')->comment('版本名');
            $table->text('detail')->comment('更新说明');
            $table->string('download')->comment('下载链接');
            $table->tinyInteger('compulsion')->comment('是否强制检查更新');
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
        Schema::drop('version');
    }
}
