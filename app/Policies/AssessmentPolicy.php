<?php

namespace App\Policies;

use App\Helpers\Helpers;
use App\Models\Assessment;
use App\Models\ModulesStudent;
use App\Models\ModulesTeacher;
use App\Models\Unit;
use App\Models\UnitsAssessment;
use App\Models\User;

class AssessmentPolicy
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

    public function view(?User $user, Assessment $assessment, Unit $unit): bool
    {
        // Check if the assessment is attached to the unit
        $unit_in_assessment = UnitsAssessment::where('unit_id', $unit->id)->where('assessment_id', $assessment->id)
            ->first();

        if (!$unit_in_assessment) {
            return false;
        }

        $is_student_role = Helpers::is_student_role();
        $is_teacher_role = Helpers::is_teacher_role();

        // Basing it from the modules table since assessments are dependent on unit->modules anyway
        if ($is_teacher_role) {
            $user_has_access_to_assessment = ModulesTeacher::isModuleId($unit->module->id)->isTeacherId($user->id)
                ->first();
        } else if ($is_student_role) {
            $user_has_access_to_assessment = ModulesStudent::isModuleId($unit->module->id)->isStudentId($user->id)
                ->first();
        }

        return $user_has_access_to_assessment ? true : false;
    }

    public function create(User $user): bool
    {
        $is_student_role = Helpers::is_student_role();

        return !$is_student_role;
    }

    public function update(User $user, Assessment $assessment): bool
    {
        $is_student_role = Helpers::is_student_role();

        return !$is_student_role;
    }

    public function delete(User $user, Assessment $assessment): bool
    {
        $is_admin_role = Helpers::is_admin_role();

        return $is_admin_role;
    }

    public function restore(User $user, Assessment $assessment): bool
    {
        $is_admin_role = Helpers::is_admin_role();

        return $is_admin_role;
    }

    public function forceDelete(User $user, Assessment $assessment): bool
    {
        $is_admin_role = Helpers::is_admin_role();

        return $is_admin_role;
    }
}
