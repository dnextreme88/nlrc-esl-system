<?php

namespace App\Livewire\Modules;

use App\Helpers\Helpers;
use App\Models\Module;
use App\Models\ModulesStudent;
use App\Models\ModulesTeacher;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ModuleList extends Component
{
    public $user_modules;

    public function render()
    {
        $user = Auth::user();
        $is_student_role = Helpers::is_student_role();
        $is_teacher_role = Helpers::is_teacher_role();

        $modules_with_user_access = '';

        if ($is_teacher_role) {
            $modules_with_user_access = ModulesTeacher::isTeacherId(Auth::user()->id)
                ->pluck('module_id');
        } else if ($is_student_role) {
            $modules_with_user_access = ModulesStudent::isStudentId(Auth::user()->id)
                ->pluck('module_id');
        }

        $this->user_modules = Module::all()->map(function ($module) use ($modules_with_user_access) {
            $module->has_access = $modules_with_user_access->contains(function (int $module_id) use ($module) {
                return $module_id == $module->id;
            });

            return $module;
        });

        return view('livewire.modules.module-list');
    }
}
