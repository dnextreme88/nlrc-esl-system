<?php

namespace Database\Seeders;

use App\Enums\ProgressionLevels;
use App\Models\ProgressionLevel;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ProgressionLevelSeeder extends Seeder
{
    public function run(): void
    {
        ProgressionLevel::insert([
            [
                'level' => 1,
                'name' => ProgressionLevels::BEGINNER->value,
                'description' => 'First level of progression',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'level' => 2,
                'name' => ProgressionLevels::INTERMEDIATE->value,
                'description' => 'Middle level of progression',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'level' => 3,
                'name' => ProgressionLevels::ADVANCED->value,
                'description' => 'Last level of progression',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
