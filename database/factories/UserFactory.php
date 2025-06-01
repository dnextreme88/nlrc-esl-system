<?php

namespace Database\Factories;

use App\Enums\Genders;
use App\Enums\Roles;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;
    public array $sample_timezones = ['Asia/Colombo', 'Asia/Manila', 'UTC'];

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $gender = fake()->randomElement(array_column(Genders::cases(), 'value'));
        $first_name = fake()->firstName($gender);
        $last_name = fake()->lastName();

        return [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => str_replace(' ', '', strtolower(trim($first_name))). '.' .str_replace(' ', '', strtolower(trim($last_name))). '@test.com',
            'email_verified_at' => now(),
            'date_of_birth' => fake()->dateTimeBetween('-50 years', '-20 years')->format('Y-m-d'),
            'gender' => $gender,
            'timezone' => fake()->randomElement($this->sample_timezones),
            'profile_photo_path' => null,
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    public function admins(): static
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => Role::isAdmin()->first()
                ->id,
            'timezone' => 'UTC',
        ]);
    }

    public function teachers(): static
    {
        $teacher_roles = [Roles::HEAD_TEACHER->value, Roles::TEACHER->value];

        return $this->state(fn (array $attributes) => [
            'role_id' => Role::where('name', fake()->randomElement($teacher_roles))->first()
                ->id,
            'timezone' => 'Asia/Colombo',
        ]);
    }

    public function students(): static
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => Role::isStudent()->first()
                ->id,
            'timezone' => 'Asia/Manila',
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
