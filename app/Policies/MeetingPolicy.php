<?php

namespace App\Policies;

use App\Enums\Roles;
use App\Models\Meetings\Meeting;
use App\Models\Meetings\MeetingUser;
use App\Models\User;

class MeetingPolicy
{
    // SAMPLE USAGE IN CONTROLLERS
    // if ($user->cannot('view', $current_meeting)) {
    //     abort(403);
    // }

    public function viewAny(User $user): bool
    {
        return $user->role->name == Roles::ADMIN->value;
    }

    public function view(?User $user, Meeting $meeting): bool
    {
        if (in_array($user->role->name, [Roles::HEAD_TEACHER->value, Roles::TEACHER->value])) {
            $user_has_meeting = Meeting::where('id', $meeting->id)->isTeacherId($user->id)
                ->first();
        } else if ($user->role->name == Roles::STUDENT->value) {
            $user_has_meeting = MeetingUser::where('meeting_id', $meeting->id)->isStudentId($user->id)
                ->first();
        }

        return $user_has_meeting || $user->role->name == Roles::ADMIN->value ? true : false;
    }

    public function create(User $user): bool
    {
        return in_array($user->role->name, [Roles::ADMIN->value, Roles::HEAD_TEACHER->value, Roles::TEACHER->value]);
    }

    public function update(User $user, Meeting $meeting): bool
    {
        return in_array($user->role->name, [Roles::ADMIN->value, Roles::HEAD_TEACHER->value, Roles::TEACHER->value]);
    }

    public function delete(User $user, Meeting $meeting): bool
    {
        return in_array($user->role->name, [Roles::ADMIN->value, Roles::HEAD_TEACHER->value, Roles::TEACHER->value]);
    }

    public function restore(User $user, Meeting $meeting): bool
    {
        return $user->role->name == Roles::ADMIN->value;
    }

    public function forceDelete(User $user, Meeting $meeting): bool
    {
        return $user->role->name == Roles::ADMIN->value;
    }
}
