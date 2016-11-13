<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // $this->call(UserTableSeeder::class);
        $this->call(SystemAssociationSeeder::class);
        $this->call(SystemReservedUserSeeder::class);
        $this->call(TopicTypeSeeder::class);

        Model::reguard();
    }
}
