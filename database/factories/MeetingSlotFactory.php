<?php

namespace Database\Factories;

use App\Enums\MeetingStatuses;
use App\Enums\Roles;
use App\Helpers\Helpers;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Uuid;

class MeetingSlotFactory extends Factory
{
    public function definition(): array
    {
        $time_slots = Helpers::populate_time_slots('H:i:s');

        $random_teacher = User::whereHas('role', fn ($query) => $query->where('name', Roles::HEAD_TEACHER->value)
            ->orWhere('name', Roles::TEACHER->value)
        )
            ->inRandomOrder()
            ->first();
        $random_time = fake()->randomElement($time_slots);
        $random_date = fake()->dateTimeBetween('-1 week', '+7 days')->format('Y-m-d');

        if ($random_date < Carbon::today()->format('Y-m-d')) {
            $random_status = fake()->randomElement(array_column(
                array_filter(MeetingStatuses::cases(), fn ($case) => $case !== MeetingStatuses::PENDING), 'value')
            );
        } else {
            $random_status = MeetingStatuses::PENDING->value;
        }

        return [
            'teacher_id' => $random_teacher->id,
            'meeting_uuid' => Uuid::uuid4()->toString(),
            'meeting_date' => $random_date,
            'start_time' => $random_date. ' ' .$random_time['start_time'],
            'end_time' => $random_date. ' ' .$random_time['end_time'],
            'status' => $random_status,
        ];
    }
}
