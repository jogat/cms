<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

//use \Database\Seeders\UserRolesAccessSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        UserRolesAccessSeeder::run();
        PostSeeder::run();
        AudienceRuleSeeder::run();
        UserMetaSeeder::run();
    }
}
