<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        Module::factory()->count(3)
            ->state(new Sequence(
                [
                    'proficiency_id' => 1,
                    'name' => 'Introduction to English Sounds and Pronunciation',
                    'slug' => Str::slug('Introduction to English Sounds and Pronunciation', '-'),
                    'description' => 'Module 1',
                ],
                [
                    'proficiency_id' => 2,
                    'name' => 'Basic Greetings & Common Phrases',
                    'slug' => Str::slug('Basic Greetings & Common Phrases', '-'),
                    'description' => 'Module 2',
                ],
                [
                    'proficiency_id' => 3,
                    'name' => 'Simple Sentence Structures (Subject-Verb-Object)',
                    'slug' => Str::slug('Simple Sentence Structures (Subject-Verb-Object)', '-'),
                    'description' => 'Module 3',
                ],
            ))
            ->create();
    }
}
