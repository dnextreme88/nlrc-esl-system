<?php

namespace App\Policies;

use App\Helpers\Helpers;
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
        $is_admin_role = Helpers::is_admin_role();

        return $is_admin_role;
    }

    public function view(?User $user, Meeting $meeting): bool
    {
        $is_admin_role = Helpers::is_admin_role();
        $is_student_role = Helpers::is_student_role();
        $is_teacher_role = Helpers::is_teacher_role();

        if ($is_teacher_role) {
            $user_has_meeting = Meeting::where('id', $meeting->id)->isTeacherId($user->id)
                ->first();
        } else if ($is_student_role) {
            $user_has_meeting = MeetingUser::where('meeting_id', $meeting->id)->isStudentId($user->id)
                ->first();
        }

        return $user_has_meeting || $is_admin_role ? true : false;
    }

    public function create(User $user): bool
    {
        $is_admin_role = Helpers::is_admin_role();
        $is_teacher_role = Helpers::is_teacher_role();

        return $is_admin_role || $is_teacher_role;
    }

    public function update(User $user, Meeting $meeting): bool
    {
        $is_admin_role = Helpers::is_admin_role();
        $is_teacher_role = Helpers::is_teacher_role();

        return $is_admin_role || $is_teacher_role;
    }

    public function delete(User $user, Meeting $meeting): bool
    {
        $is_admin_role = Helpers::is_admin_role();
        $is_teacher_role = Helpers::is_teacher_role();

        return $is_admin_role || $is_teacher_role;
    }

    public function restore(User $user, Meeting $meeting): bool
    {
        $is_admin_role = Helpers::is_admin_role();

        return $is_admin_role;
    }

    public function forceDelete(User $user, Meeting $meeting): bool
    {
        $is_admin_role = Helpers::is_admin_role();

        return $is_admin_role;
    }
}
