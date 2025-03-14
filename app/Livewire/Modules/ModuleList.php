<?php

namespace App\Livewire\Modules;

use App\Enums\Roles;
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

        $modules_with_user_access = '';

        if (in_array($user->role->name, [Roles::HEAD_TEACHER->value, Roles::TEACHER->value])) {
            $modules_with_user_access = ModulesTeacher::where('teacher_id', Auth::user()->id)
                ->pluck('module_id');
        } else if ($user->role->name == Roles::STUDENT->value) {
            $modules_with_user_access = ModulesStudent::where('student_id', Auth::user()->id)
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
