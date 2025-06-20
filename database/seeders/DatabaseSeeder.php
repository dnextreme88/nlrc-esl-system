<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ProficiencySeeder::class,
            RoleSeeder::class,
            ModuleSeeder::class,
            UnitSeeder::class,
            UserSeeder::class,
            MeetingSlotSeeder::class,
            AssessmentSeeder::class,
            AnnouncementSeeder::class,
        ]);
    }
}
