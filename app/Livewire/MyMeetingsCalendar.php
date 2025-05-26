<?php

namespace App\Livewire;

use App\Enums\Roles;
use App\Helpers\Helpers;
use App\Models\Meetings\Meeting;
use App\Models\Meetings\MeetingUser;
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
            $meetings_for_teacher = Meeting::isTeacherId($user->id)->whereHas('meeting_users')
                ->get();

            foreach ($meetings_for_teacher as $slot) {
                $students = [];

                if (count($slot['meeting_users']) > 0) {
                    foreach ($slot['meeting_users'] as $student) {
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
                    'meeting' => $slot,
                ];
            }
        } else if ($user->role->name == Roles::STUDENT->value) {
            $meetings_for_student = MeetingUser::isStudentId($user->id)->whereHas('meeting')
                ->get();

            foreach ($meetings_for_student as $slot) {
                $meeting_start_time = Helpers::parse_time_to_user_timezone($slot->meeting->start_time);
                $meeting_end_time = Helpers::parse_time_to_user_timezone($slot->meeting->end_time);

                $meetings[] = [
                    'id' => $slot->meeting->id,
                    'title' => $meeting_start_time->format('h:i A'). ' ~ ' .$meeting_end_time->format('h:i A'),
                    'description' => $slot->meeting->teacher->profile_photo_url,
                    'date' => $meeting_start_time,
                    'meeting' => $slot->meeting,
                ];
            }
        }

        return collect($meetings);
    }
}
