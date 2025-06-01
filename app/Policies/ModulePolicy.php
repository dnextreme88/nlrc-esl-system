<?php

namespace App\Policies;

use App\Helpers\Helpers;
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
        $is_admin_role = Helpers::is_admin_role();

        return $is_admin_role;
    }

    public function view(?User $user, Module $module): bool
    {
        $is_student_role = Helpers::is_student_role();
        $is_teacher_role = Helpers::is_teacher_role();

        if ($is_teacher_role) {
            $user_has_access_to_module = ModulesTeacher::isModuleId($module->id)->isTeacherId($user->id)
                ->first();
        } else if ($is_student_role) {
            $user_has_access_to_module = ModulesStudent::isModuleId($module->id)->isStudentId($user->id)
                ->first();
        }

        return $user_has_access_to_module ? true : false;
    }

    public function create(User $user): bool
    {
        $is_admin_role = Helpers::is_admin_role();

        return $is_admin_role;
    }

    public function update(User $user, Module $module): bool
    {
        $is_admin_role = Helpers::is_admin_role();

        return $is_admin_role;
    }

    public function delete(User $user, Module $module): bool
    {
        $is_admin_role = Helpers::is_admin_role();

        return $is_admin_role;
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
