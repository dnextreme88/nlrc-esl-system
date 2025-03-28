<?php

namespace App\Policies;

use App\Enums\Roles;
use App\Models\Module;
use App\Models\ModulesStudent;
use App\Models\ModulesTeacher;
use App\Models\User;

class ModulePolicy
{
    // SAMPLE USAGE IN CONTROLLERS
    // if ($user->cannot('view', $current_module)) {
    //     abort(403);
    // }

    public function viewAny(User $user): bool
    {
        return $user->role->name == Roles::ADMIN->value;
    }

    public function view(?User $user, Module $module): bool
    {
        if (in_array($user->role->name, [Roles::HEAD_TEACHER->value, Roles::TEACHER->value])) {
            $user_has_access_to_module = ModulesTeacher::isModuleId($module->id)->isTeacherId($user->id)
                ->first();
        } else if ($user->role->name == Roles::STUDENT->value) {
            $user_has_access_to_module = ModulesStudent::isModuleId($module->id)->isStudentId($user->id)
                ->first();
        }

        return $user_has_access_to_module ? true : false;
    }

    public function create(User $user): bool
    {
        return $user->role->name == Roles::ADMIN->value;
    }

    public function update(User $user, Module $module): bool
    {
        return $user->role->name == Roles::ADMIN->value;
    }

    public function delete(User $user, Module $module): bool
    {
        return $user->role->name == Roles::ADMIN->value;
    }

    public function restore(User $user, Module $module): bool
    {
        return $user->id === $module->user_id;
    }

    public function forceDelete(User $user, Module $module): bool
    {
        return $user->id === $module->user_id;
    }
}
