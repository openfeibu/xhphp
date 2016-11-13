<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePapersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('papers', function (Blueprint $table) {        
            $table->increments('id');
            $table->string('type',50)->comment('文案类型');  
            $table->text('papars')->comment("文案");
            $table->string('papars',100)->comment("文案url");
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
        //
    }
}
