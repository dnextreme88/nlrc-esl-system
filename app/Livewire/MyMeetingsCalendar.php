<?php

namespace App\Livewire;

use App\Enums\Roles;
use App\Models\MeetingSlot;
use App\Models\MeetingSlotsUser;
use Carbon\Carbon;
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
            $meetings_for_teacher = MeetingSlot::where('teacher_id', $user->id)
                ->whereHas('meeting_slots_users')
                ->get();

            foreach ($meetings_for_teacher as $slot) {
                $students = [];

                if (count($slot['meeting_slots_users']) > 0) {
                    foreach ($slot['meeting_slots_users'] as $student) {
                        $students[] = $student->profile_photo_url;
                    }
                }

                $meetings[] = [
                    'id' => $slot->id,
                    'title' => Carbon::parse($slot->start_time)->toUserTimezone()->format('H:i A'). ' ~ ' .Carbon::parse($slot->end_time)->toUserTimezone()->format('H:i A'),
                    'description' => count($students) > 0 ? $students : 'N/A',
                    'date' => Carbon::parse($slot->start_time)->toUserTimezone(),
                ];
            }
        } else if ($user->role->name == Roles::STUDENT->value) {
            $meetings_for_student = MeetingSlotsUser::where('student_id', $user->id)
                ->whereHas('meeting_slot')
                ->get();

            foreach ($meetings_for_student as $slot) {
                $meetings[] = [
                    'id' => $slot->meeting_slot->id,
                    'title' => Carbon::parse($slot->meeting_slot->start_time)->toUserTimezone()->format('H:i A'). ' ~ ' .Carbon::parse($slot->meeting_slot->end_time)->toUserTimezone()->format('H:i A'),
                    'description' => $slot->meeting_slot->teacher->profile_photo_url,
                    'date' => Carbon::parse($slot->meeting_slot->start_time)->toUserTimezone(),
                ];
            }
        }

        return collect($meetings);
    }
}
