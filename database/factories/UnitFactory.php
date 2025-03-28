<?php

namespace Database\Factories;

use App\Models\Module;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UnitFactory extends Factory
{
    public function definition(): array
    {
        $random_module = Module::select(['id'])->inRandomOrder()
            ->first();

        $random_name = implode(' ', fake()->words(fake()->randomDigitNotNull()));

        return [
            'module_id' => $random_module->id,
            'name' => ucfirst($random_name),
            'slug' => Str::slug($random_name, '-'),
            'description' => fake()->sentence(fake()->randomDigitNotNull()),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
