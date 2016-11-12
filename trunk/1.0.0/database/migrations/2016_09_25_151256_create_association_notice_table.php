<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssociationNoticeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('association_notice', function (Blueprint $table) {        
            $table->increments('id');
			$table->integer('aid');
			$table->integer('uid');
			$table->text('notice')->comment("社团公告");
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
        //
    }
}
