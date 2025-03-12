<?php

namespace Database\Seeders;

use App\Enums\Roles;
use App\Models\MeetingSlot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MeetingSlotSeeder extends Seeder
{
    public function run(): void
    {
        MeetingSlot::factory(12)->create()
            ->each(function ($meeting_slot, $index) {
                // Logic to select random students reserved in slots
                $random_number = rand(2, 30);

                if ($random_number % 3 == 0) { // Divisible by 3
                    $random_student = User::whereHas('role', fn ($query) => $query->where('name', Roles::STUDENT->value))
                        ->inRandomOrder()
                        ->first();

                    $meeting_slot->meeting_slots_users()->attach($meeting_slot->id, [
                        'student_id' => $random_student->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);

                    if ($random_number % 2 == 0) { // Add another student if random number is divisible by 2
                        $another_random_student = User::whereNot('id', $random_student->id)
                            ->whereHas('role', fn ($query) => $query->where('name', Roles::STUDENT->value))
                            ->inRandomOrder()
                            ->first();

                        $meeting_slot->meeting_slots_users()->attach($meeting_slot->id, [
                            'student_id' => $another_random_student->id,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]);
                    }

                    $meeting_slot->is_opened = 1;
                    $meeting_slot->save();
                }
            });
    }
}
