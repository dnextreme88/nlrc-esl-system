<?php

namespace App\Traits;

trait IdTrait
{
    protected function scopeIsModuleId($query, int $module_id)
    {
        return $query->where('module_id', $module_id);
    }

    protected function scopeIsStudentId($query, int $user_id)
    {
        return $query->where('student_id', $user_id);
    }

    protected function scopeIsTeacherId($query, int $user_id)
    {
        return $query->where('teacher_id', $user_id);
    }
}
