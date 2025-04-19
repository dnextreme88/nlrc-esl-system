<?php

namespace Database\Factories;

use App\Enums\Roles;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnnouncementFactory extends Factory
{
    public function definition(): array
    {
        $random_admin = User::whereHas('role', fn ($query) => $query->where('name', Roles::ADMIN->value))->inRandomOrder()
            ->first();

        $random_title = implode(' ', fake()->words(fake()->randomDigitNotNull()));

        return [
            'user_id' => $random_admin->id,
            'title' => ucfirst($random_title),
            'description' => fake()->sentence(fake()->randomDigitNotNull()),
            'tags' => implode(',', fake()->words(3)),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
