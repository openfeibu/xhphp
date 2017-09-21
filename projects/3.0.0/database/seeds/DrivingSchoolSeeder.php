<?php

use Illuminate\Database\Seeder;

class DrivingSchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('driving_school')->insert([
            ['name' => '360驾校','desc' => '教练好，扣配好']，

        ]);
    }
}
