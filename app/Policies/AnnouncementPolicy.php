<?php

namespace App\Policies;

use App\Enums\Roles;
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
        return $user->role->name == Roles::ADMIN->value;
    }

    public function view(?User $user, Announcement $announcement): bool
    {
        $user_has_announcement = $user->notifications()
            ->where('type', 'announcement-sent')
            ->where('data->announcement_id', $announcement->id)
            ->first();

        return $user_has_announcement || $user->role->name == Roles::ADMIN->value ? true : false;
    }

    public function create(User $user): bool
    {
        return $user->role->name == Roles::ADMIN->value;
    }

    public function update(User $user, Announcement $announcement): bool
    {
        return $user->role->name == Roles::ADMIN->value;
    }

    public function delete(User $user, Announcement $announcement): bool
    {
        return $user->role->name == Roles::ADMIN->value;
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
