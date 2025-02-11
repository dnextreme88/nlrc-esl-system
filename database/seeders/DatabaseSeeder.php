<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ProgressionLevelSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            MeetingSlotsSeeder::class,
        ]);
    }
}
