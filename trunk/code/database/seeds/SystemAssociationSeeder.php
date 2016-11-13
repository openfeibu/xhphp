<?php

use Illuminate\Database\Seeder;

class SystemAssociationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('association')->insert([
        	'aid' => 0,
        	'aname' => '系统',
        	'avatar_url' => 'http://xhplus.feibu.info/fb/images/logo.png',
        	'member_number' => 0,
        	'superior' => 0,
        	'created_at' => '2016-09-01 00:00:00'
        	]);
    }
}
