<?php

use Illuminate\Database\Seeder;

class TopicTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('topic_type')->insert([
            ['type' => '随心写'],
            ['type' => '新鲜事'],
            ['type' => '一起约'],
            ['type' => '帮帮忙'],
            ['type' => '吐吐槽'],
            ['type' => '问一下'],
        ]);
    }
}
