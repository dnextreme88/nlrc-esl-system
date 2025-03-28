<?php

namespace Database\Seeders;

use App\Enums\Proficiencies;
use App\Models\Proficiency;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ProficiencySeeder extends Seeder
{
    public function run(): void
    {
        // Based on the Common European Framework of Reference (CEFR)
        Proficiency::insert([
            [
                'level_code' => Proficiencies::levelCode(Proficiencies::BEGINNER),
                'name' => Proficiencies::BEGINNER->value,
                'description' => Proficiencies::levelDescription(Proficiencies::BEGINNER),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'level_code' => Proficiencies::levelCode(Proficiencies::PRE_INTERMEDIATE),
                'name' => Proficiencies::PRE_INTERMEDIATE->value,
                'description' => Proficiencies::levelDescription(Proficiencies::PRE_INTERMEDIATE),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'level_code' => Proficiencies::levelCode(Proficiencies::INTERMEDIATE),
                'name' => Proficiencies::INTERMEDIATE->value,
                'description' => Proficiencies::levelDescription(Proficiencies::INTERMEDIATE),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'level_code' => Proficiencies::levelCode(Proficiencies::UPPER_INTERMEDIATE),
                'name' => Proficiencies::UPPER_INTERMEDIATE->value,
                'description' => Proficiencies::levelDescription(Proficiencies::UPPER_INTERMEDIATE),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'level_code' => Proficiencies::levelCode(Proficiencies::ADVANCED),
                'name' => Proficiencies::ADVANCED->value,
                'description' => Proficiencies::levelDescription(Proficiencies::ADVANCED),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'level_code' => Proficiencies::levelCode(Proficiencies::MASTERY),
                'name' => Proficiencies::MASTERY->value,
                'description' => Proficiencies::levelDescription(Proficiencies::MASTERY),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
