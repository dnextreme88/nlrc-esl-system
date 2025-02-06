<?php

namespace Database\Seeders;

use App\Enums\Roles;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::insert([
            [
                'name' => Roles::ADMIN->value,
                'description' => 'Users with administrator privileges',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => Roles::HEAD_TEACHER->value,
                'description' => 'Users that conduct mock assessments',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => Roles::STUDENT->value,
                'description' => 'Users who will take up modules and assessments',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => Roles::TEACHER->value,
                'description' => 'Users that interact with the students but cannot conduct mock assessments',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
