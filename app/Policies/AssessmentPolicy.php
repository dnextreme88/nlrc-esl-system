<?php

namespace App\Policies;

use App\Enums\Roles;
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
        return $user->role->name == Roles::ADMIN->value;
    }

    public function view(?User $user, Assessment $assessment, Unit $unit): bool
    {
        // Check if the assessment is attached to the unit
        $unit_in_assessment = UnitsAssessment::where('unit_id', $unit->id)->where('assessment_id', $assessment->id)
            ->first();

        if (!$unit_in_assessment) {
            return false;
        }

        // Basing it from the modules table since assessments are dependent on unit->modules anyway
        if (in_array($user->role->name, [Roles::HEAD_TEACHER->value, Roles::TEACHER->value])) {
            $user_has_access_to_assessment = ModulesTeacher::isModuleId($unit->module->id)->isTeacherId($user->id)
                ->first();
        } else if ($user->role->name == Roles::STUDENT->value) {
            $user_has_access_to_assessment = ModulesStudent::isModuleId($unit->module->id)->isStudentId($user->id)
                ->first();
        }

        return $user_has_access_to_assessment ? true : false;
    }

    public function create(User $user): bool
    {
        return $user->role->name != Roles::STUDENT->value;
    }

    public function update(User $user, Assessment $assessment): bool
    {
        return $user->role->name != Roles::STUDENT->value;
    }

    public function delete(User $user, Assessment $assessment): bool
    {
        return $user->role->name == Roles::ADMIN->value;
    }

    public function restore(User $user, Assessment $assessment): bool
    {
        return $user->role->name == Roles::ADMIN->value;
    }

    public function forceDelete(User $user, Assessment $assessment): bool
    {
        return $user->role->name == Roles::ADMIN->value;
    }
}
