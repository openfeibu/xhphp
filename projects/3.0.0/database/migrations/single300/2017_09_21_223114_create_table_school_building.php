<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSchoolBuilding extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_building',function(Blueprint $table){
			$table->increments('building_id')->unsigned();
            $table->integer('campus_id')->unsigned();
            $table->char('building_no','20')->comment('栋号');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('school_building');
    }
}
