<?php

use Illuminate\Database\Seeder;

class LevelTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('level')->insert(
             array(
                 array(
                  'level' => '0',
                  'integral' => 0
                ),
                array(
                  'level' => '1',
                  'integral' => 10
                ),
                array(
                  'level' => '2',
                  'integral' => 20
                ),
                array(
                  'level' => '3',
                  'integral' => 35
                ),
                array(
                  'level' => '4',
                  'integral' => 55
                ),
                array(
                  'level' => '5',
                  'integral' => 80
                ),
                array(
                  'level' => '6',
                  'integral' => 110
                ),
                array(
                  'level' => '7',
                  'integral' => 145
                ),
                array(
                  'level' => '8',
                  'integral' => 185
                ),
                array(
                  'level' => '9',
                  'integral' => 235
                ),
                array(
                  'level' => '10',
                  'integral' => 300
                ),
             ));
    }
}
