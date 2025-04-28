<?php

namespace Database\Factories;

use App\Enums\AssessmentTypes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssessmentFactory extends Factory
{
    public function definition(): array
    {
        $random_title = implode(' ', fake()->words(fake()->randomDigitNotNull()));
        $assessment_types = array_column(AssessmentTypes::cases(), 'value');

        return [
            'title' => ucfirst($random_title),
            'type' => $assessment_types[array_rand($assessment_types)],
            'description' => fake()->sentence(fake()->randomDigitNotNull()),
            'is_active' => fake()->boolean(75),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
