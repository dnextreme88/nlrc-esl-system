<?php

namespace App\Livewire;

use App\Enums\Roles;
use App\Helpers\Helpers;
use App\Models\MeetingSlot;
use App\Models\MeetingSlotsUser;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Omnia\LivewireCalendar\LivewireCalendar;

class MyMeetingsCalendar extends LivewireCalendar
{
    public function events(): Collection
    {
        $meetings = [];
        $user = Auth::user();

        if (in_array($user->role->name, [Roles::HEAD_TEACHER->value, Roles::TEACHER->value])) {
            $meetings_for_teacher = MeetingSlot::isTeacherId($user->id)->whereHas('meeting_slots_users')
                ->get();

            foreach ($meetings_for_teacher as $slot) {
                $students = [];

                if (count($slot['meeting_slots_users']) > 0) {
                    foreach ($slot['meeting_slots_users'] as $student) {
                        $students[] = $student->profile_photo_url;
                    }
                }

                $meeting_start_time = Helpers::parse_time_to_user_timezone($slot->start_time);
                $meeting_end_time = Helpers::parse_time_to_user_timezone($slot->end_time);

                $meetings[] = [
                    'id' => $slot->id,
                    'title' => $meeting_start_time->format('h:i A'). ' ~ ' .$meeting_end_time->format('h:i A'),
                    'description' => count($students) > 0 ? $students : 'N/A',
                    'date' => $meeting_start_time,
                    'meeting_slot' => $slot,
                ];
            }
        } else if ($user->role->name == Roles::STUDENT->value) {
            $meetings_for_student = MeetingSlotsUser::isStudentId($user->id)->whereHas('meeting_slot')
                ->get();

            foreach ($meetings_for_student as $slot) {
                $meeting_start_time = Helpers::parse_time_to_user_timezone($slot->meeting_slot->start_time);
                $meeting_end_time = Helpers::parse_time_to_user_timezone($slot->meeting_slot->end_time);

                $meetings[] = [
                    'id' => $slot->meeting_slot->id,
                    'title' => $meeting_start_time->format('h:i A'). ' ~ ' .$meeting_end_time->format('h:i A'),
                    'description' => $slot->meeting_slot->teacher->profile_photo_url,
                    'date' => $meeting_start_time,
                    'meeting_slot' => $slot->meeting_slot,
                ];
            }
        }

        return collect($meetings);
    }
}
