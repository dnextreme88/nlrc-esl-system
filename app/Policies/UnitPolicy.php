<?php

namespace App\Policies;

use App\Enums\Roles;
use App\Models\Module;
use App\Models\ModulesStudent;
use App\Models\ModulesTeacher;
use App\Models\Unit;
use App\Models\User;

class UnitPolicy
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
        // Basing it from the modules table since units are dependent on modules anyway
        if (in_array($user->role->name, [Roles::HEAD_TEACHER->value, Roles::TEACHER->value])) {
            $user_has_access_to_unit = ModulesTeacher::where('module_id', $module->id)->isTeacherId($user->id)
                ->first();
        } else if ($user->role->name == Roles::STUDENT->value) {
            $user_has_access_to_unit = ModulesStudent::where('module_id', $module->id)->isStudentId($user->id)
                ->first();
        }

        return $user_has_access_to_unit ? true : false;
    }

    public function create(User $user): bool
    {
        return $user->role->name == Roles::ADMIN->value;
    }

    public function update(User $user, Unit $unit): bool
    {
        return $user->role->name == Roles::ADMIN->value;
    }

    public function delete(User $user, Unit $unit): bool
    {
        return $user->role->name == Roles::ADMIN->value;
    }

    public function restore(User $user, Unit $unit): bool
    {
        return $user->role->name == Roles::ADMIN->value;
    }

    public function forceDelete(User $user, Unit $unit): bool
    {
        return $user->role->name == Roles::ADMIN->value;
    }
}
