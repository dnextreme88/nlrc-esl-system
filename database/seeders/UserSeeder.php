<?php

namespace Database\Seeders;

use App\Enums\Genders;
use App\Enums\Roles;
use App\Models\Proficiency;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $genders = array_column(Genders::cases(), 'value');
        $sample_timezones = ['Asia/Colombo', 'Asia/Manila', 'UTC'];
        $default_pass = Hash::make('password');

        User::insert([
            [
                'role_id' => Role::where('name', Roles::ADMIN->value)->first()->id,
                'first_name' => Roles::ADMIN->value,
                'last_name' => 'User',
                'email' => 'admin@test.com',
                'date_of_birth' => Carbon::today()->subYears(rand(18, 35))->subMonths(rand(0, 12))->subDays(rand(1, 28)),
                'gender' => $genders[array_rand($genders)],
                'timezone' => fake()->randomElement($sample_timezones),
                'password' => $default_pass,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'role_id' => Role::where('name', Roles::HEAD_TEACHER->value)->first()->id,
                'first_name' => Roles::HEAD_TEACHER->value,
                'last_name' => 'User',
                'email' => 'head@test.com',
                'date_of_birth' => Carbon::today()->subYears(rand(18, 35))->subMonths(rand(0, 12))->subDays(rand(1, 28)),
                'gender' => $genders[array_rand($genders)],
                'timezone' => fake()->randomElement($sample_timezones),
                'password' => $default_pass,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'role_id' => Role::where('name', Roles::TEACHER->value)->first()->id,
                'first_name' => Roles::TEACHER->value,
                'last_name' => 'User',
                'email' => 'teacher@test.com',
                'date_of_birth' => Carbon::today()->subYears(rand(18, 35))->subMonths(rand(0, 12))->subDays(rand(1, 28)),
                'gender' => $genders[array_rand($genders)],
                'timezone' => fake()->randomElement($sample_timezones),
                'password' => $default_pass,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);

        User::factory(3)->students()
            ->create()
            ->each(function ($user, $index) {
                // Logic to populate proficiencies of students
                $first_proficiency_id = Proficiency::select(['id'])->first()
                    ->id;
                $last_proficiency_id = Proficiency::select(['id'])->latest()
                    ->first()
                    ->id;

                $random_number = rand($first_proficiency_id, $last_proficiency_id);

                $user->proficiencies_users()->attach($user->id, [
                    'proficiency_id' => $random_number,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                if ($random_number < 5) { // Add another proficiency if the first one isn't a C1/C2 mastery yet
                    $another_random_proficiency = Proficiency::where('id', '>', $random_number)->inRandomOrder()
                        ->first();

                    $user->proficiencies_users()->attach($user->id, [
                        'proficiency_id' => $another_random_proficiency->id,
                        'created_at' => Carbon::now()->addDays(28),
                        'updated_at' => Carbon::now()->addDays(28),
                    ]);
                }
            });
    }
}
