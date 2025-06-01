<?php

namespace App\Policies;

use App\Helpers\Helpers;
use App\Models\Announcement;
use App\Models\User;

class AnnouncementPolicy
{
    // SAMPLE USAGE IN CONTROLLERS
    // if ($user->cannot('view', $current_announcement)) {
    //     abort(403);
    // }

    public function viewAny(User $user): bool
    {
        $is_admin_role = Helpers::is_admin_role();

        return $is_admin_role;
    }

    public function view(?User $user, Announcement $announcement): bool
    {
        $is_admin_role = Helpers::is_admin_role();

        $user_has_announcement = $user->notifications()
            ->where('type', 'announcement-sent')
            ->where('data->announcement_id', $announcement->id)
            ->first();

        return $is_admin_role || $user_has_announcement ? true : false;
    }

    public function create(User $user): bool
    {
        $is_admin_role = Helpers::is_admin_role();

        return $is_admin_role;
    }

    public function update(User $user, Announcement $announcement): bool
    {
        $is_admin_role = Helpers::is_admin_role();

        return $is_admin_role;
    }

    public function delete(User $user, Announcement $announcement): bool
    {
        $is_admin_role = Helpers::is_admin_role();

        return $is_admin_role;
    }

    public function restore(User $user, Announcement $announcement): bool
    {
        return $user->id === $announcement->user_id;
    }

    public function forceDelete(User $user, Announcement $announcement): bool
    {
        return $user->id === $announcement->user_id;
    }
}
