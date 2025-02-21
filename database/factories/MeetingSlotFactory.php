<?php

namespace Database\Factories;

use App\Enums\Roles;
use App\Helpers\Helpers;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MeetingSlotFactory extends Factory
{
    public function definition(): array
    {
        $time_slots = Helpers::populate_time_slots();

        $random_teacher = User::whereHas('role', fn ($query) => $query->where('name', Roles::HEAD_TEACHER->value)
            ->orWhere('name', Roles::TEACHER->value)
        )
            ->inRandomOrder()
            ->first();
        $random_time = fake()->randomElement($time_slots);

        return [
            'teacher_id' => $random_teacher->id,
            'meeting_date' => fake()->dateTimeBetween('now', '+7 days')->format('Y-m-d'),
            'start_time' => $random_time['start_time'],
            'end_time' => $random_time['end_time'],
            'is_reserved' => fake()->boolean(100),
        ];
    }
}
