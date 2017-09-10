<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableZhimaCertify extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zhima_certify',function(Blueprint $table){
			$table->increments('id');
            $table->integer('uid')->comment('用户ID') ;
            $table->string('cert_name')->comment('姓名') ;
            $table->string('cert_no')->comment('身份证号码');
            $table->string('bizNo')->comment('认证号');
            $table->enum('status',['succ','certifying','certified'])->default('certifying')->comment('认证情况');
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
        Schema::drop('zhima_certify');
    }
}
