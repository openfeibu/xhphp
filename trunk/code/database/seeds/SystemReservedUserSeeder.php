<?php

use Illuminate\Database\Seeder;

class SystemReservedUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user')->insert([
        	'uid' => 1,
        	'openid' => md5(DB::raw('(select UUID())')),
        	'mobile_no' => '8888888888',
        	'password' => '88888888',
        	'token' => DB::raw('(select UUID())'),
        	'nickname' => '系统',
        	'avatar_url' => '',
        	'created_ip' => '8.8.8.8',
            'created_at' => '2016-01-01 00:00:00',
        	]);
    }
}
