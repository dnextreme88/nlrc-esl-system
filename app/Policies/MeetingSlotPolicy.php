<?php

namespace App\Policies;

use App\Enums\Roles;
use App\Models\MeetingSlot;
use App\Models\MeetingSlotsUser;
use App\Models\User;

class MeetingSlotPolicy
{
    // SAMPLE USAGE IN CONTROLLERS
    // if ($user->cannot('view', $current_meeting_slot)) {
    //     abort(403);
    // }

    public function viewAny(User $user): bool
    {
        return $user->role->name == Roles::ADMIN->value;
    }

    public function view(?User $user, MeetingSlot $meeting_slot): bool
    {
        if (in_array($user->role->name, [Roles::HEAD_TEACHER->value, Roles::TEACHER->value])) {
            $user_has_meeting = MeetingSlot::where('id', $meeting_slot->id)->where('teacher_id', $user->id)
                ->first();
        } else if ($user->role->name == Roles::STUDENT->value) {
            $user_has_meeting = MeetingSlotsUser::where('meeting_slot_id', $meeting_slot->id)->where('student_id', $user->id)
                ->first();
        }

        return $user_has_meeting || $user->role->name == Roles::ADMIN->value ? true : false;
    }

    public function create(User $user): bool
    {
        return in_array($user->role->name, [Roles::ADMIN->value, Roles::HEAD_TEACHER->value, Roles::TEACHER->value]);
    }

    public function update(User $user, MeetingSlot $meeting_slot): bool
    {
        return in_array($user->role->name, [Roles::ADMIN->value, Roles::HEAD_TEACHER->value, Roles::TEACHER->value]);
    }

    public function delete(User $user, MeetingSlot $meeting_slot): bool
    {
        return in_array($user->role->name, [Roles::ADMIN->value, Roles::HEAD_TEACHER->value, Roles::TEACHER->value]);
    }

    public function restore(User $user, MeetingSlot $meeting_slot): bool
    {
        return $user->role->name == Roles::ADMIN->value;
    }

    public function forceDelete(User $user, MeetingSlot $meeting_slot): bool
    {
        return $user->role->name == Roles::ADMIN->value;
    }
}
