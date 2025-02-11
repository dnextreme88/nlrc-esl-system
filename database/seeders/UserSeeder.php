<?php

namespace Database\Seeders;

use App\Enums\Genders;
use App\Enums\Roles;
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
        $default_pass = Hash::make('password');

        User::insert([
            [
                'role_id' => Role::where('name', Roles::ADMIN->value)->first()->id,
                'first_name' => Roles::ADMIN->value,
                'last_name' => 'User',
                'email' => 'admin@test.com',
                'date_of_birth' => Carbon::today()->subYears(rand(18, 35))->subMonths(rand(0, 12))->subDays(rand(1, 28)),
                'gender' => $genders[array_rand($genders)],
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
                'password' => $default_pass,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'role_id' => Role::where('name', Roles::STUDENT->value)->first()->id,
                'first_name' => Roles::STUDENT->value. ' 1',
                'last_name' => 'User',
                'email' => 'student@test.com',
                'date_of_birth' => Carbon::today()->subYears(rand(18, 35))->subMonths(rand(0, 12))->subDays(rand(1, 28)),
                'gender' => $genders[array_rand($genders)],
                'password' => $default_pass,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'role_id' => Role::where('name', Roles::STUDENT->value)->first()->id,
                'first_name' => Roles::STUDENT->value. ' 2',
                'last_name' => 'User',
                'email' => 'student2@test.com',
                'date_of_birth' => Carbon::today()->subYears(rand(18, 35))->subMonths(rand(0, 12))->subDays(rand(1, 28)),
                'gender' => $genders[array_rand($genders)],
                'password' => $default_pass,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'role_id' => Role::where('name', Roles::STUDENT->value)->first()->id,
                'first_name' => Roles::STUDENT->value. ' 3',
                'last_name' => 'User',
                'email' => 'student3@test.com',
                'date_of_birth' => Carbon::today()->subYears(rand(18, 35))->subMonths(rand(0, 12))->subDays(rand(1, 28)),
                'gender' => $genders[array_rand($genders)],
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
                'password' => $default_pass,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
