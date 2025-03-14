<?php

namespace Database\Factories;

use App\Models\Proficiency;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ModuleFactory extends Factory
{
    public function definition(): array
    {
        $first_proficiency_id = Proficiency::select(['id'])->first()
            ->id;
        $last_proficiency_id = Proficiency::select(['id'])->latest()
            ->first()
            ->id;
        $random_number = rand($first_proficiency_id, $last_proficiency_id);

        $random_name = implode(' ', fake()->words(fake()->randomDigitNotNull()));

        return [
            'proficiency_id' => $random_number,
            'name' => ucfirst($random_name),
            'slug' => Str::slug($random_name, '-'),
            'description' => fake()->sentence(fake()->randomDigitNotNull()),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
