<?php

namespace Database\Seeders;

use App\Enums\Roles;
use App\Models\Meetings\Meeting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MeetingSeeder extends Seeder
{
    public function run(): void
    {
        Meeting::factory(12)->create()
            ->each(function ($meeting, $index) {
                $random_number = rand(2, 30);

                if ($random_number % 3 == 0) { // Divisible by 3
                    $random_student = User::whereHas('role', fn ($query) => $query->where('name', Roles::STUDENT->value))
                        ->inRandomOrder()
                        ->first();

                    $meeting->meeting_users()->attach($meeting->id, [
                        'student_id' => $random_student->id,
                        'created_at' => Carbon::now()->addMinutes(5),
                        'updated_at' => Carbon::now()->addMinutes(5),
                    ]);

                    if ($random_number % 2 == 0) { // Add another student if random number is divisible by 2
                        $another_random_student = User::whereNot('id', $random_student->id)
                            ->whereHas('role', fn ($query) => $query->where('name', Roles::STUDENT->value))
                            ->inRandomOrder()
                            ->first();

                        $meeting->meeting_users()->attach($meeting->id, [
                            'student_id' => $another_random_student->id,
                            'created_at' => Carbon::now()->addMinutes(10),
                            'updated_at' => Carbon::now()->addMinutes(10),
                        ]);
                    }

                    $meeting->is_opened = 1;
                    $meeting->save();
                }
            });
    }
}
